# Guía de Despliegue - Hostinger

## ✅ Archivos Modificados para Producción

### Frontend (URLs Actualizadas)
- ✅ `src/pages/login.astro` - Cambio de `localhost:8000` a `/api`
- ✅ `src/pages/dashboard/perfil.astro` - API_BASE actualizado
- ✅ `src/components/dashboard/Ventas.astro` - API_BASE actualizado
- ✅ `src/components/dashboard/Formulario.astro` - API_BASE actualizado

### Backend (Archivos de Infraestructura Creados)
- ✅ `api/.htaccess` - Configuración Apache (CORS, seguridad, PHP)
- ✅ `api/index.php` - Router central (health check)

---

## 📋 Pasos para Configurar en Hostinger

### 1. Verificar Base de Datos

```bash
# En Hostinger File Manager o SSH:
# La base de datos DEBE estar en:
/database/jlc_ventas.db

# Permisos (IMPORTANTE):
chmod 666 database/jlc_ventas.db
chmod 777 database/
```

### 2. Verificar Extensión PHP SQLite

**En cPanel de Hostinger:**
1. Ir a `Software` → `Select PHP Version`
2. Verificar que esté habilitada: `php-sqlite3` o `pdo_sqlite`
3. Si no está, activarla manualmente

### 3. Configurar Variable de Entorno (.env)

**Crear/Editar `.env` en el servidor:**

```env
# Base de datos
DB_PATH=/home/[TU_USUARIO]/public_html/ventas/database/jlc_ventas.db

# JWT
JWT_SECRET=[TU_SECRET_AQUI]

# Upload
UPLOAD_MAX_SIZE=5242880
```

> **IMPORTANTE:** Reemplaza [TU_USUARIO] con tu nombre de usuario de Hostinger

### 4. Verificar Estructura de Directorios

```
public_html/ventas/
├── api/
│   ├── .htaccess         ← NUEVO
│   ├── index.php         ← NUEVO
│   ├── auth/
│   ├── sales/
│   ├── products/
│   ├── uploads/
│   ├── config/
│   └── ...
├── database/
│   └── jlc_ventas.db     ← VERIFICAR QUE EXISTA
├── uploads/
│   └── facturas/
├── .env                  ← CONFIGURAR
└── [archivos de Astro build]
```

### 5. Pruebas de Endpoints

**Health Check (debería retornar 200):**
```
https://ventas.jlc-electronics.com/api/
https://ventas.jlc-electronics.com/api/index.php
```

**Login (debería retornar 401 o datos válidos, NO 500):**
```
POST https://ventas.jlc-electronics.com/api/auth/login.php
```

---

## 🔍 Diagnóstico de Errores

### Si ves Error 500:

1. **Ver logs de PHP en Hostinger:**
   - cPanel → `Errors` → `Error Log`

2. **Causas comunes:**
   - ❌ Base de datos no existe
   - ❌ Permisos incorrectos en BD
   - ❌ Extensión SQLite no habilitada
   - ❌ Ruta en `.env` incorrecta

### Si ves CORS Error:

- Verificar que `api/.htaccess` se haya subido correctamente
- Revisar que Apache tenga `mod_headers` habilitado

### Si ves 404 en /api/auth/login.php:

- Verificar que la estructura de directorios sea correcta
- Verificar que FTP haya subido todos los archivos de `api/`

---

## 🚀 Después de Configurar

**Comandos para commit:**

```bash
git add .
git commit -m "fix: Configure production URLs and add deployment infrastructure"
git push origin deploy
```

**GitHub → Hostinger sincronizará automáticamente vía FTP**

---

## ✅ Checklist Final

- [ ] Base de datos existe en `/database/jlc_ventas.db`
- [ ] Permisos de BD son 666 (escritura)
- [ ] Directorio `/database/` tiene permisos 777
- [ ] PHP versión 7.4+ con SQLite habilitado
- [ ] `.env` configurado con rutas absolutas
- [ ] Archivos subidos vía Git/FTP
- [ ] Login funciona sin error 500
- [ ] CORS no da error en consola
