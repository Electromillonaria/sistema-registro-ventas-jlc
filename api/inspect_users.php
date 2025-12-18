<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

if ($driver === 'sqlite') {
    $stmt = $conn->query("PRAGMA table_info(usuarios)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['name'] . "\n";
    }
} else {
    $stmt = $conn->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . "\n";
    }
}
