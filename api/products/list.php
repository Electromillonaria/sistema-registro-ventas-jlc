<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';

// No requiere autenticación - productos son públicos
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Obtener solo productos activos
    $sql = "SELECT id, modelo, descripcion FROM productos_jlc WHERE activo = 1 ORDER BY modelo ASC";
    $stmt = $conn->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'status' => 200,
        'data' => $products
    ]);

} catch (PDOException $e) {
    error_log("Error listing products: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 500,
        'message' => 'Error al obtener productos'
    ]);
}
