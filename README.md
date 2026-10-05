<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Biblioteca ArquiSoft

Sistema web para gestionar el catálogo, préstamos, devoluciones y multas de la biblioteca.

## Procesos implementados

- Consulta y búsqueda de libros por título, autor, categoría y disponibilidad.
- Registro transaccional de préstamos con control de ejemplares y vencimiento de 15 días.
- Devolución autorizada para bibliotecarios y administradores.
- Cálculo configurable de multa por atraso (`BIBLIOTECA_MULTA_POR_DIA_CENTAVOS`).
- Inicio de sesión exclusivo para los roles de personal (`bibliotecario`, `cajero`, `administrador`).
- Registro presencial de clientes y préstamos por el bibliotecario, con estado pendiente de pago.
- Confirmación del pago por el cajero; solo al aprobarse se activa el préstamo y se genera el comprobante.

## Arquitectura

La presentación usa Blade y Tailwind/Vite. Las rutas delegan en controladores; las reglas transaccionales de circulación viven en `app/Services/PrestamoService.php`; Eloquent representa libros, usuarios, préstamos, pagos y multas; las migraciones mantienen la consistencia de inventario y estados.

## Puesta en marcha

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Ejecutar con Docker

Configura las variables de entorno en `.env` y levanta la aplicación:

```sh
docker compose up --build
```

La aplicación estará disponible en `http://localhost:8080`. El contenedor no ejecuta migraciones automáticamente; ejecútalas cuando la base de datos esté configurada:

```sh
docker compose exec app php artisan migrate --seed
```

## Credenciales demo

Después de ejecutar `php artisan migrate --seed`, se crean estas cuentas para probar los permisos:

| Perfil | Correo | Contraseña | Accesos principales |
| --- | --- | --- | --- |
| Administrador | `admin@arquisoft.test` | `admin12345` | Control general. |
| Bibliotecario | `bibliotecario@arquisoft.test` | `biblio12345` | Registro de clientes, préstamos y devoluciones. |
| Cajero | Configurado por variables de entorno | Configurada por variables de entorno | Confirmación de pagos en `/gestion/pagos`. |

Estas credenciales son solo para desarrollo local. En producción deben cambiarse o eliminarse.

### Habilitar el cajero en Supabase

La cuenta se crea en la base de datos a la que apunta la aplicación Laravel. En el entorno que tenga configurada la conexión de Supabase, establece `BIBLIOTECA_CAJERO_NOMBRE`, `BIBLIOTECA_CAJERO_EMAIL` y `BIBLIOTECA_CAJERO_PASSWORD` (usa una contraseña única de al menos 12 caracteres). Luego ejecuta:

```sh
php artisan config:clear
php artisan db:seed --class='Database\Seeders\CajeroSeeder'
```

El seeder crea la cuenta o actualiza el rol de una cuenta existente con ese correo. Después, el cajero inicia sesión en `/login` y entra a **Consultar pagos** para confirmar los pagos pendientes. No ejecutes este seeder desde el SQL Editor de Supabase: debe ejecutarse mediante Laravel para que la contraseña se guarde con hash.

Los clientes son atendidos presencialmente y no inician sesión. El catálogo público es solo de consulta; el préstamo se solicita al bibliotecario, queda pendiente de pago y el cajero lo activa tras confirmar el pago.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
