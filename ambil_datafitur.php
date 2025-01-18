<?php
define('WP_USE_THEMES', false);

header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "honda_cars";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data mobil dengan harga terendah dari varian
$sql = "
    SELECT c.id, c.name, c.category, c.image, 
           (SELECT MIN(cv.price) 
            FROM car_variants cv 
            WHERE cv.car_id = c.id) AS startingPrice
    FROM cars c
";
$result = $conn->query($sql);

$cars = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $car = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'image' => $row['image'],
            'startingPrice' => $row['startingPrice'],
            'variants' => array()
        );

        // Query untuk mengambil varian mobil
        $variant_sql = "SELECT * FROM car_variants WHERE car_id = " . $row['id'];
        $variant_result = $conn->query($variant_sql);

        if ($variant_result->num_rows > 0) {
            while($variant_row = $variant_result->fetch_assoc()) {
                $car['variants'][] = array(
                    'name' => $variant_row['name'],
                    'price' => $variant_row['price'],
                    'specs' => array(
                        'mesin' => $variant_row['mesin'],
                        'tenaga' => $variant_row['tenaga'],
                        'transmisi' => $variant_row['transmisi'],
                        'konsumsiBahanBakar' => $variant_row['konsumsi_bahan_bakar'],
                        'kapasitasPenumpang' => $variant_row['kapasitas_penumpang'],
                        'dimensi' => $variant_row['dimensi'],
                        'groundClearance' => $variant_row['ground_clearance'],
                        'fiturKeamanan' => $variant_row['fitur_keamanan'],
                        'hiburan' => $variant_row['hiburan'],
                        'garansi' => $variant_row['garansi'],
                        'fiturRelevan' => explode(', ', $variant_row['fitur_relevan']),
                        'fiturUnik' => explode(', ', $variant_row['fitur_unik'])
                    )
                );
            }
        }

        $cars[] = $car;
    }
}

echo json_encode($cars);

$conn->close();
?>
