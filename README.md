# 🧠 MindWare
**Plataforma digital para el seguimiento emocional y acompañamiento terapéutico**

MindWare es una plataforma web desarrollada para facilitar el seguimiento psicológico de pacientes mediante el registro de emociones, actividades terapéuticas, citas, reportes y comunicación con profesionales de la salud. El sistema busca mejorar el acompañamiento terapéutico y brindar herramientas claras tanto a médicos como a pacientes para monitorear la evolución emocional.

---

## 🚀 Características principales

- Inicio de sesión seguro por roles (Administrador, Médico y Paciente).
- Panel de control personalizado según el tipo de usuario.
- Registro, control y consulta de pacientes y tutores.
- Gestión de citas médicas con historial y seguimiento.
- Registro de actividades terapéuticas asignadas y su cumplimiento.
- Registro de emociones y construcción automática del historial emocional.
- Generación de reportes estadísticos interactivos para análisis clínico.
- Sistema de notificaciones internas y por correo electrónico.
- Respaldo y restauración de la base de datos desde el sistema.

---

## 🧱 Arquitectura y Tecnologías

| Componente | Tecnología |
|-----------|------------|
| Framework backend | **Laravel 10 (PHP 8+)** |
| Motor de plantillas | **Blade** |
| Base de datos | **MySQL / MariaDB** |
| Librerías UI | **Bootstrap 5**, **Iconos FontAwesome**, **SweetAlert2** |
| Reportes y Exportación | **Laravel-Excel** |
| Autenticación | **Laravel Auth con roles y middleware personalizados** |

---

## 📦 Requerimientos previos

Asegúrate de contar con:

- PHP 8.1+
- Composer
- MySQL o MariaDB
- Node.js y npm (para compilar assets)
- Extensiones comunes de PHP habilitadas (mbstring, openssl, pdo, etc.)

---

## 🔧 Instalación y Configuración

```bash
# Clonar el repositorio
git clone https://github.com/deb1802/Estadia.git
cd Estadia

# Instalar dependencias de Laravel
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de la aplicación
php artisan key:generate

# Configurar la base de datos en el archivo .env
# DB_DATABASE=mindware
# DB_USERNAME=root
# DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate --seed

# Instalar dependencias front-end
npm install && npm run build
