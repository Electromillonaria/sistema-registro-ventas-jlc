<?php
/**
 * Script de Instalación Única - Crear Usuario Administrador
 * Sistema JLC - Registro de Ventas
 * 
 * SEGURIDAD:
 * - Solo funciona si NO hay usuarios en la base de datos
 * - Requiere SETUP_SECRET del archivo .env
 * - Se auto-bloquea después de crear el primer admin
 * - Las contraseñas se hashean con bcrypt
 * 
 * USO:
 * 1. Configurar SETUP_SECRET en tu archivo .env
 * 2. Acceder a: https://tudominio.com/ventas/api/setup/create_admin.php
 * 3. Ingresar el SETUP_SECRET y los datos del admin
 * 4. El script se auto-bloquea automáticamente
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Archivo de bloqueo
$lock_file = __DIR__ . '/.setup_completed';

/**
 * Responder con JSON
 */
function respond($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Verificar si el setup ya fue completado
 */
function isSetupCompleted() {
    global $lock_file;
    return file_exists($lock_file);
}

/**
 * Marcar setup como completado
 */
function markSetupCompleted() {
    global $lock_file;
    $content = "Setup completado el: " . date('Y-m-d H:i:s') . "\n";
    $content .= "Este archivo bloquea el script de instalación.\n";
    $content .= "Para volver a ejecutarlo, elimina este archivo (solo en emergencias).\n";
    file_put_contents($lock_file, $content);
    chmod($lock_file, 0444); // Solo lectura
}

/**
 * Verificar que la BD esté vacía (sin usuarios)
 */
function isDatabaseEmpty() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] == 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * HTML del formulario de setup
 */
function renderSetupForm($error = null) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instalación Inicial - JLC Ventas</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #1a1f3a 0%, #0a0e1a 100%);
                color: #e0e0e0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: rgba(26, 31, 58, 0.95);
                border: 1px solid rgba(99, 102, 241, 0.3);
                border-radius: 12px;
                padding: 40px;
                max-width: 500px;
                width: 100%;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            }
            h1 {
                color: #818cf8;
                font-size: 24px;
                margin-bottom: 8px;
            }
            .subtitle {
                color: #9ca3af;
                font-size: 14px;
                margin-bottom: 30px;
            }
            .warning {
                background: rgba(239, 68, 68, 0.1);
                border-left: 3px solid #ef4444;
                padding: 12px;
                margin-bottom: 20px;
                border-radius: 4px;
                font-size: 14px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                color: #c7d2fe;
                font-size: 14px;
                margin-bottom: 8px;
                font-weight: 500;
            }
            input {
                width: 100%;
                padding: 12px;
                background: rgba(17, 24, 39, 0.8);
                border: 1px solid rgba(99, 102, 241, 0.3);
                border-radius: 6px;
                color: #e0e0e0;
                font-size: 14px;
                transition: all 0.3s;
            }
            input:focus {
                outline: none;
                border-color: #818cf8;
                box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.1);
            }
            .hint {
                font-size: 12px;
                color: #9ca3af;
                margin-top: 4px;
            }
            button {
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }
            button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            }
            .error {
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid #ef4444;
                color: #fca5a5;
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 20px;
                font-size: 14px;
            }
            .success {
                background: rgba(34, 197, 94, 0.1);
                border: 1px solid #22c55e;
                color: #86efac;
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔐 Instalación Inicial</h1>
            <p class="subtitle">Sistema JLC - Registro de Ventas</p>
            
            <div class="warning">
                ⚠️ <strong>Importante:</strong> Este script solo puede ejecutarse una vez. Crea el usuario administrador inicial del sistema.
            </div>

            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="setup_secret">Clave Secreta de Instalación *</label>
                    <input type="password" id="setup_secret" name="setup_secret" required>
                    <div class="hint">Configurada en tu archivo .env como SETUP_SECRET</div>
                </div>

                <div class="form-group">
                    <label for="cedula">Cédula del Administrador *</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password" required minlength="8">
                    <div class="hint">Mínimo 8 caracteres. Usa mayúsculas, números y símbolos.</div>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo Electrónico *</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <button type="submit">Crear Administrador e Instalar Sistema</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ===== VERIFICACIONES DE SEGURIDAD =====

// 1. Verificar si ya fue completado
if (isSetupCompleted()) {
    respond(false, 'El setup ya fue completado. Este script está bloqueado.', null, 403);
}

// 2. Si es GET, mostrar formulario
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    renderSetupForm();
}

// 3. Si es POST, procesar instalación
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Método no permitido', null, 405);
}

// 4. Verificar que la BD esté vacía
if (!isDatabaseEmpty()) {
    respond(false, 'La base de datos ya tiene usuarios. No se puede ejecutar el setup.', null, 403);
}

// 5. Obtener datos del POST
$setup_secret = $_POST['setup_secret'] ?? '';
$cedula = trim($_POST['cedula'] ?? '');
$password = $_POST['password'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo = trim($_POST['correo'] ?? '');

// 6. Validar campos requeridos
if (empty($setup_secret) || empty($cedula) || empty($password) || empty($nombre) || empty($apellido) || empty($correo)) {
    renderSetupForm('Todos los campos son obligatorios');
}

// 7. Verificar SETUP_SECRET
$expected_secret = getenv('SETUP_SECRET');
if (empty($expected_secret)) {
    respond(false, 'SETUP_SECRET no configurado en el servidor. Configúralo en .env', null, 500);
}

if ($setup_secret !== $expected_secret) {
    sleep(2); // Delay contra ataques de fuerza bruta
    renderSetupForm('Clave secreta incorrecta');
}

// 8. Validar formato de contraseña
if (strlen($password) < 8) {
    renderSetupForm('La contraseña debe tener al menos 8 caracteres');
}

// 9. Crear usuario administrador
try {
    $db = Database::getInstance()->getConnection();
    
    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertar usuario
    $sql = "INSERT INTO usuarios (
        cedula, password, rol, nombre, apellido,
        tipo_documento, numero_documento, fecha_nacimiento,
        ciudad_residencia, departamento, whatsapp, correo,
        nombre_distribuidor, ciudad_punto_venta, cargo,
        acepta_tratamiento_datos, acepta_contacto_comercial,
        declara_info_verdadera, activo
    ) VALUES (
        :cedula, :password, 'administrador', :nombre, :apellido,
        'CC', :cedula, '1990-01-01',
        'Bogotá', 'Cundinamarca', '3000000000', :correo,
        'JLC Distribución Colombia', 'Bogotá', 'Administrador',
        1, 1, 1, 1
    )";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':cedula' => $cedula,
        ':password' => $password_hash,
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':correo' => $correo
    ]);
    
    // Insertar productos de ejemplo
    $productos_sql = "INSERT INTO productos_jlc (modelo, descripcion, activo) VALUES
        ('JLC-AIR-001', 'Aire Acondicionado Split 12000 BTU', 1),
        ('JLC-AIR-002', 'Aire Acondicionado Split 18000 BTU', 1),
        ('JLC-AIR-003', 'Aire Acondicionado Split 24000 BTU', 1),
        ('JLC-REF-001', 'Refrigerador No Frost 350L', 1),
        ('JLC-REF-002', 'Refrigerador No Frost 450L', 1),
        ('JLC-LAV-001', 'Lavadora Automática 18kg', 1),
        ('JLC-LAV-002', 'Lavadora Automática 24kg', 1)";
    
    $db->exec($productos_sql);
    
    // Marcar setup como completado
    markSetupCompleted();
    
    // Respuesta exitosa
    respond(true, 'Sistema instalado correctamente. Usuario administrador creado.', [
        'cedula' => $cedula,
        'nombre' => $nombre . ' ' . $apellido,
        'correo' => $correo,
        'productos_creados' => 7
    ]);
    
} catch (PDOException $e) {
    error_log("Setup error: " . $e->getMessage());
    
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        renderSetupForm('La cédula o correo ya existen en el sistema');
    }
    
    respond(false, 'Error al crear el usuario: ' . $e->getMessage(), null, 500);
}
