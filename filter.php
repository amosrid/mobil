<?php
// filter.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mobil";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $features = json_decode($_POST['features'] ?? '[]');
    $search = $_POST['search'] ?? '';
    
    if (empty($features)) {
        // Jika tidak ada filter yang dipilih, tampilkan semua mobil
        $sql = "SELECT DISTINCT p.product_id, p.name, p.category, p.price, p.brand
                FROM products p
                WHERE p.name LIKE ?";
        
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("s", $searchTerm);
    } else {
        // Jika ada filter yang dipilih
        $placeholders = str_repeat('?,', count($features) - 1) . '?';
        
        $sql = "SELECT p.product_id, p.name, p.category, p.price, p.brand
                FROM products p
                JOIN product_features pf ON p.product_id = pf.product_id
                WHERE pf.feature_id IN ($placeholders)
                AND p.name LIKE ?
                GROUP BY p.product_id
                HAVING COUNT(DISTINCT pf.feature_id) = ?";
        
        $stmt = $conn->prepare($sql);
        
        // Buat array references untuk bind_param
        $params = array();
        $types = str_repeat('i', count($features)) . 's' . 'i';
        $params[] = &$types;
        
        foreach ($features as $key => $value) {
            $params[] = &$features[$key];
        }
        
        $searchTerm = "%$search%";
        $params[] = &$searchTerm;
        
        $featureCount = count($features);
        $params[] = &$featureCount;
        
        call_user_func_array(array($stmt, 'bind_param'), $params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Merek</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>{$row['product_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['category']}</td>
                <td>{$row['price']}</td>
                <td>{$row['brand']}</td>
            </tr>
            ";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada mobil yang ditemukan.</p>";
    }

    $stmt->close();
}

$conn->close();
?>