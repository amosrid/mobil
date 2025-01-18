<?php
// Get all files in current directory
$files = array();
$dir = opendir('.');

while (($file = readdir($dir)) !== false) {
    if ($file != '.' && $file != '..' && $file != 'index.php') {
        $files[] = array(
            'name' => $file,
            'mtime' => filemtime($file),
            'size' => filesize($file)
        );
    }
}
closedir($dir);

// Sort files by modification time (newest first)
usort($files, function($a, $b) {
    return $b['mtime'] - $a['mtime'];
});

// Format for human readable file size
function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Index of /cobamobil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .icon {
            width: 20px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>Index of /cobamobil</h1>
    <table>
        <tr>
            <th>Name</th>
            <th>Last Modified</th>
            <th>Size</th>
        </tr>
        <tr>
            <td><a href="..">..</a></td>
            <td>-</td>
            <td>-</td>
        </tr>
        <?php foreach ($files as $file): ?>
        <tr>
            <td><a href="<?php echo htmlspecialchars($file['name']); ?>"><?php echo htmlspecialchars($file['name']); ?></a></td>
            <td><?php echo date('Y-m-d H:i', $file['mtime']); ?></td>
            <td><?php echo formatSize($file['size']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>