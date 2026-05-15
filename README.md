# Bookie 2.0

Bookie 2.0 es una aplicación web Laravel para gestionar una plataforma de apuestas con parte pública, autenticación, zona privada de usuario, panel de administración, billetera, chat, notificaciones, rankings y varios juegos integrados.

## Funcionalidades principales

- Página pública con acceso a inicio, registro, login y sobre nosotros.
- Registro y autenticación de usuarios.
- Roles de usuario: administrador, operador y jugador.
- Zona privada protegida por middleware de autenticación.
- Dashboard de usuario con resumen de saldo, apuestas, ganancias y pérdidas.
- Panel de administración con estadísticas globales, usuarios, apuestas, predicciones, billeteras, juegos, notificaciones y rankings.
- Billetera con ingresos y retiradas validadas.
- Juegos disponibles:
  - Ruleta.
  - Predicción.
  - Dados.
  - Cara o Cruz.
- Chat privado entre usuarios.
- Sistema de notificaciones.
- Seeders preparados para cargar datos iniciales coherentes.

## Arquitectura

El proyecto sigue la arquitectura MVC de Laravel:

- **Presentación:** vistas Blade en `resources/views` y assets públicos en `public`.
- **Controladores:** clases en `app/Http/Controllers` que reciben peticiones, validan formularios y coordinan la respuesta.
- **Servicios:** `app/Services/ApuestaService.php` centraliza el proceso de apuesta.
- **Modelo/Dominio:** modelos Eloquent en `app/Models`.
- **Persistencia:** migraciones y seeders en `database` sobre MySQL.
- **Rutas y middleware:** `routes/web.php` define la navegación pública, privada y de administración.

### Servicio de apuestas

La capa de servicios contiene el método principal:

```php
ApuestaService::procesarApuesta(User $user, array $datos): array
```

Este método coordina de forma transaccional:

1. Comprobación de saldo disponible.
2. Actualización de la billetera.
3. Creación de la entrada en `apuestas`.
4. Creación de notificación asociada cuando procede.

El servicio se apoya en métodos específicos de `BilleteraController` y `ApuestaController`, y es usado por los controladores de juegos como Ruleta, Dados, Cara o Cruz y Predicción.

## Requisitos

- PHP 8.2 o superior.
- Composer.
- MySQL o MariaDB.
- Extensiones PHP habituales de Laravel: `mbstring`, `pdo_mysql`, `xml`, `curl`, `zip`.

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configura la conexión MySQL en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dss
DB_USERNAME=dss
DB_PASSWORD=tu_password
```

Ejecuta migraciones y seeders:

```bash
php artisan migrate:fresh --seed
```

Levanta la aplicación:

```bash
php artisan serve
```

## Usuarios iniciales

Tras ejecutar los seeders puedes entrar con:

```txt
Administrador: admin@bookie20.test
Contraseña: password123
```

También existen varios usuarios jugadores generados por los seeders con la misma contraseña.

## Rutas útiles

```txt
/                 Página pública
/login            Inicio de sesión
/register         Registro
/dashboard        Dashboard privado
/juegos           Juegos
/ruleta           Ruleta
/prediccion       Predicción
/dados            Dados
/cara-o-cruz      Cara o Cruz
/billetera        Billetera
/chat             Chat
/mis-notificaciones Notificaciones
/admin            Panel de administración
```

## Entrega

La versión correspondiente a la entrega debe etiquetarse en GitHub como:

```txt
entrega-03
```

Para preparar una base limpia antes de entregar:

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan serve
```

##Adminer Acces: http://localhost/adminer?server=&username=dss&db=dss&table=transaccion


Ejecuta seeders: php artisan migrate:fresh --seed

Para falllos satánicos: 
1. composer install
2. php artisan key:generate
3. php artisan migrate:fresh --seed



##Ejecutar Seeders: php artisan db:seed (Todo tiene que estar limpio, ejecutar comandos de limpieza)

##Comando pa borrar las tablas y volver a crearlas: php artisan migrate:fresh
