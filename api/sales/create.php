<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../controllers/VentasController.php';

// Autenticación
$user = requireAuth();

// Obtener datos
$data = json_decode(file_get_contents("php://input"), true);

if (is_null($data)) {
    http_response_code(400);
    echo json_encode(['status' => 400, 'message' => 'Datos inválidos']);
    exit;
}

// Agregar ID del asesor
$data['asesor_id'] = $user['user_id'];

// Procesar
$controller = new VentasController();
$result = $controller->crearVenta($data);

http_response_code($result['status']);
echo json_encode($result);
