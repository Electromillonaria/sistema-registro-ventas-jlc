# 🔐 Guía Rápida: Secretos de GitHub para Despliegue

Esta es una lista de referencia rápida de todos los secretos que necesitas configurar en GitHub para que el despliegue automático funcione.

## Dónde configurar

`Tu Repositorio → Settings → Secrets and variables → Actions → New repository secret`

---

## Lista de Secretos Requeridos

### 🌐 Credenciales FTP de Hostinger

| Nombre del Secreto | Ejemplo de Valor | Dónde Obtenerlo |
|-------------------|------------------|-----------------|
| `FTP_SERVER` | `ftp.tudominio.com` | hPanel → Archivos → Cuentas FTP |
| `FTP_USERNAME` | `u123456789` | hPanel → Archivos → Cuentas FTP |
| `FTP_PASSWORD` | `tu_contraseña_ftp` | hPanel → Archivos → Cuentas FTP |

### 🗄️ Base de Datos MySQL

| Nombre del Secreto | Ejemplo de Valor | Dónde Obtenerlo |
|-------------------|------------------|-----------------|
| `DB_HOST` | `localhost` | hPanel → Bases de datos (usualmente localhost) |
| `DB_NAME` | `u123456789_ventas_jlc` | hPanel → Bases de datos → Tu BD creada |
| `DB_USER` | `u123456789_admin` | hPanel → Bases de datos → Usuario creado |
| `DB_PASS` | `tu_contraseña_mysql` | La que definiste al crear el usuario |

### 🚀 URLs de la Aplicación

| Nombre del Secreto | Ejemplo de Valor | Notas |
|-------------------|------------------|-------|
| `APP_URL` | `https://ventas.ejemplo.com` | URL completa del subdominio |
| `API_URL` | `https://ventas.ejemplo.com/api` | URL de tu API backend |
| `PUBLIC_APP_URL` | `https://ventas.ejemplo.com` | Mismo valor que APP_URL |
| `PUBLIC_API_URL` | `https://ventas.ejemplo.com/api` | Mismo valor que API_URL |

###🔒 Seguridad

| Nombre del Secreto | Ejemplo de Valor | Notas |
|-------------------|------------------|-------|
| `JWT_SECRET` | `tu_clave_secreta_aleatoria_64_chars` | [Genera aquí](https://generate-secret.vercel.app/64) |
| `JWT_EXPIRATION` | `28800` | 8 horas en segundos |
| `SETUP_SECRET` | `clave_secreta_instalacion_unica` | Para script de instalación inicial |

### ⚙️ Configuración

| Nombre del Secreto | Valor Recomendado | Notas |
|-------------------|-------------------|-------|
| `UPLOAD_MAX_SIZE` | `5242880` | 5MB en bytes |

---

## 📝 Checklist de Configuración

- [ ] Crear base de datos MySQL en hPanel
- [ ] Crear usuario MySQL con todos los permisos
- [ ] Configurar los secretos listados arriba (15 en total)
- [ ] Verificar que el directorio `public_html/ventas/` existe
- [ ] Verificar permisos del directorio `uploads/` (755)

---

## 🚀 Activar Despliegue

Una vez configurados todos los secretos:

```bash
git checkout deploy
git merge main
git push origin deploy
```

Monitorea el progreso en: **GitHub → Actions → Desplegar a Hostinger**

---

## ⚠️ Troubleshooting Común

**"Context access might be invalid"** en GitHub Actions:
- Normal, solo significa que GitHub no puede validar si el secreto existe
- El workflow funcionará si configuraste los secretos correctamente

**Despliegue falla en FTP:**
- Verifica credenciales FTP
- Confirma que el directorio remoto existe
- Revisa que estés usando el servidor FTP correcto

**API devuelve error 500:**
- Verifica que el archivo `.env` se creó en el servidor
- Revisa logs de PHP en hPanel
- Confirma que la base de datos existe y las credenciales son correctas
