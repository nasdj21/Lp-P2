<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## General

Este repositorio contiene el **backend del proyecto LP-P2**, desarrollado con **Laravel** y expuesto como **API REST** para ser consumido por el frontend en React.

Incluye:
- Autenticación con **Laravel Sanctum**
- Gestión de servicios
- Gestión de citas
- Gestión de usuarios
- Arquitectura desacoplada (API first)

---

## Especificaciones

- **Laravel** 10.x  
- **PHP** 8.1+
- **Composer** 2.x
- **Laravel Sanctum**
- **Vite**

---

## Requerimientos

Antes de iniciar, asegúrate de tener instalado:

- PHP ≥ 8.1
- Composer ≥ 2.0
- Node.js ≥ 18
- npm ≥ 9
- Git

### Extensiones PHP requeridas
- openssl
- pdo
- pdo_sqlite / pdo_mysql
- mbstring
- tokenizer
- xml
- ctype
- json
- fileinfo

---

## Installation

```bash
### 1. Clonar repositorio
git clone https://github.com/nasdj21/Lp-P2.git
cd Lp-P2

### 2. Instalar dependencias PHP
composer install

### 3. Crear archivo de entorno
cp .env.example .env

### Deberia  quedar algo similar a
DB_CONNECTION=prefered_Connection
DB_HOST=xxx.xx.xx.x
DB_PORT=xxx
DB_DATABASE=prefered_database
DB_USERNAME=prefered_database
DB_PASSWORD=prefered_password

### 4. Generar APP_KEY
php artisan key:generate

### 5. Migrar
php artisan migrate
```
## Desplegar el Backend

```bash
php artisan serve
```
Api disponible en:
http://127.0.0.1:8000
