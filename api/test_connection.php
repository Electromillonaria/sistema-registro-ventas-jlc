<?php
/**
 * Archivo de Diagnóstico - Hostinger
 * Sube este archivo a: public_html/ventas/api/test_connection.php
 * Luego visita: https://ventas.jlc-electronics.com/api/test_connection.php
 */

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$diagnostics = [
    'php_version' => phpversion(),
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

// Test 1: Verificar archivos requeridos
$diagnostics['tests']['files'] = [
    'database.php' => file_exists(__DIR__ . '/config/database.php'),
    'cors.php' => file_exists(__DIR__ . '/config/cors.php'),
    'auth.php' => file_exists(__DIR__ . '/middleware/auth.php'),
    'login.php' => file_exists(__DIR__ . '/auth/login.php'),
];

// Test 2: Verificar .env
$env_paths = [
    __DIR__ . '/../.env',
    $_SERVER['DOCUMENT_ROOT'] . '/.env',
    dirname($_SERVER['DOCUMENT_ROOT']) . '/.env',
];

$env_found = false;
$env_location = null;
foreach ($env_paths as $path) {
    if (file_exists($path)) {
        $env_found = true;
        $env_location = $path;
        break;
    }
}

$diagnostics['tests']['env_file'] = [
    'found' => $env_found,
    'location' => $env_location,
    'searched_paths' => $env_paths
];

// Test 3: Verificar variables de entorno (después de cargar .env)
if ($env_found) {
    require_once __DIR__ . '/config/database.php';
    $diagnostics['tests']['env_vars'] = [
        'DB_CONNECTION' => getenv('DB_CONNECTION') ?: 'not_set',
        'DB_HOST' => getenv('DB_HOST') ?: 'not_set',
        'DB_NAME' => getenv('DB_NAME') ?: 'not_set',
        'DB_USER' => getenv('DB_USER') ?: 'not_set',
        'DB_PASS' => getenv('DB_PASS') ? '***SET***' : 'not_set',
        'JWT_SECRET' => getenv('JWT_SECRET') ? '***SET***' : 'not_set',
    ];
}

// Test 4: Intentar conectar a la BD
try {
    if ($env_found) {
        require_once __DIR__ . '/config/database.php';
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $diagnostics['tests']['database'] = [
            'connection' => 'SUCCESS',
            'info' => $db->getDatabaseInfo()
        ];
        
        // Test 5: Verificar tablas
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $diagnostics['tests']['tables'] = [
            'count' => count($tables),
            'list' => $tables
        ];
        
    } else {
        $diagnostics['tests']['database'] = [
            'connection' => 'SKIPPED',
            'reason' => '.env file not found'
        ];
    }
} catch (Exception $e) {
    $diagnostics['tests']['database'] = [
        'connection' => 'FAILED',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ];
}

// Test 6: Verificar PHP extensions
$diagnostics['tests']['php_extensions'] = [
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'mbstring' => extension_loaded('mbstring'),
    'json' => extension_loaded('json'),
    'openssl' => extension_loaded('openssl'),
];

// Test 7: Permisos de escritura
$diagnostics['tests']['permissions'] = [
    'uploads_dir' => is_writable(__DIR__ . '/../uploads'),
    'database_dir' => is_writable(__DIR__ . '/../database'),
];

// Output
echo json_encode($diagnostics, JSON_PRETTY_PRINT);
