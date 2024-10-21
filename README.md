# 📊 Proyecto Laravel Dashboard

Este es un proyecto de Laravel que permite gestionar y visualizar datos de manera eficiente. Este README te guiará a través del proceso de configuración del proyecto para que puedas empezar a trabajar sin problemas.

---

## 🚀 Requisitos Previos

Asegúrate de tener instalados los siguientes componentes en tu máquina:

- **PHP** (versión mínima: `8.2`)
- **Composer**
- **Node.js y NPM** (si usas herramientas de frontend)
- **Git**
- Un servidor web como **Apache** o **Nginx**

---

## 📥 Clonación del Repositorio

Primero, clona el repositorio en tu máquina local:

```bash
git clone https://github.com/Jeremyabel710/LaravelDashboardgod.git
cd tu-repositorio
```
## 📦 Instalación de Dependencias
Instala las dependencias del proyecto usando Composer:

```bash
composer install
```
Si necesitas las dependencias de frontend, también ejecuta:


```bash
npm install
```
## 📋 Dependencias del Proyecto
Este proyecto utiliza las siguientes dependencias adicionales:

- **jeroennoten/laravel-adminlte**: `^3.13`
- **laravel/jetstream**: `^5.2`
- **laravel/sanctum**: `^4.0`
- **livewire/livewire**: `^3.0`

## 🛠️ Dependencias de Desarrollo
Las siguientes dependencias son necesarias solo para el desarrollo:

- **fakerphp/faker**: `^1.23`
- **laravel/pint**: `^1.13`
- **laravel/sail**: `^1.26`
- **mockery/mockery**: `^1.6`
- **nunomaduro/collision**: `^8.0`
- **phpunit/phpunit**: `^11.0.1`

## ⚙️ Configuración del Archivo .env
El archivo .env contiene las configuraciones específicas del entorno. Para evitar errores, sigue estos pasos:

Copia el archivo .env.example y renómbralo a .env:

```bash
cp .env.example .env
```
Abre el archivo .env y configura las variables de entorno según tu entorno local. Asegúrate de configurar las credenciales de la base de datos y otras configuraciones necesarias.

## 🔑 Generar Clave de Aplicación
Después de configurar el archivo .env, genera una clave de aplicación:

```bash
php artisan key:generate
```
## 🗄️ Migraciones de Base de Datos
Ejecuta las migraciones para crear las tablas necesarias en tu base de datos:

```bash
php artisan migrate
```
## ⚙️ Compilación de Activos
Si estás utilizando herramientas de frontend, compila los activos:

```bash
npm run dev
```
Para producción, ejecuta:

```bash
npm run build
```
## 🖥️ Ejecutar el Servidor
Puedes ejecutar el servidor de desarrollo de Laravel con el siguiente comando:

```bash
php artisan serve
```
Luego, abre tu navegador y dirígete a http://localhost:8000.

## ⚠️ Solución de Problemas
Si encuentras errores relacionados con la configuración de Git o bloqueos, verifica lo siguiente:

1. Asegúrate de estar en la carpeta correcta del proyecto.
2. Comprueba que tienes los permisos adecuados para ejecutar comandos de Git.
3. Revisa tu configuración de ramas remotas utilizando `git remote -v`.

## 🤝 Contribuciones
Si deseas contribuir a este proyecto, por favor abre un issue o un pull request. Cualquier ayuda es bienvenida.

## 📜 Licencia
Este proyecto está bajo la Licencia MIT. Consulta el archivo *LICENSE* para más detalles.

### Notas de Personalización

Asegúrate de cambiar los enlaces y detalles específicos a tu proyecto. ¡Espero que te guste el  proyecto.
