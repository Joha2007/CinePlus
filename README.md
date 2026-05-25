# 🎬 CinePlus — Sistema de Reservas de Cine

**Proyecto de Cátedra — DSP Desarrollo de Aplicaciones Web Interpretadas en el Servidor**  
Universidad Don Bosco · Ciclo DSS404 G01T

| Integrante | Carnet |
|---|---|
| Montano González, Valeria del Rosario | MG250290 |
| Alvarenga Molina, Alison Andrea | AM252994 |
| Rodríguez Montoya, Carlos Eduardo | RM252980 |
| Portillo Anzora, Johanna Marisela | PA252991 |
| Molina Hernández, Nelson Eduardo | MH252987 |

>
---

## 📋 Descripción

CinePlus es una plataforma web de gestión y reserva de entradas de cine con múltiples sucursales. Permite a los clientes explorar la cartelera, seleccionar asientos e incluir snacks en su reserva, mientras que cada administrador gestiona únicamente su propia sucursal desde un panel dedicado. El sistema expone además una **API REST** completa consumible por aplicaciones externas.

---

## 🛠️ Tecnologías

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Base de datos | MySQL 8 (XAMPP) |
| Autenticación Web | Sesiones PHP personalizadas |
| Autenticación API | Laravel Sanctum (tokens Bearer) |
| Frontend | Bootstrap 5.3 + Bootstrap Icons |
| Almacenamiento | Laravel Storage (disco público) |

---

## 🗄️ Modelo de Base de Datos

```
clientes          → id_cliente, nombre, apellido, correo, edad, contraseña, contacto
administradores   → id_admin, id_suc (FK), nombre, apellido, correo, contraseña, contacto
sucursales        → id_suc, nombre_suc, dir_suc, contacto_suc
salas             → id_sala, id_suc2 (FK), num_sala, capaci_sala
asientos          → id_asiento, id_sala1 (FK), num_fila, num_asiento, estado
peliculas         → id_pelicula, nom_pelicula, descripcion, duracion, img, rango_edad
categorias        → id_categoria, nom_categoria
pelicula_categoria → (pivot) id_pelicula ↔ id_categoria
horarios          → id_horario, id_pelicula1 (FK), id_sala2 (FK), fecha, hora_inicio, tec_proyecc
productos         → id_producto, id_admin1 (FK), nom_productos, descripcion, precio, stock, img
reservas          → id_reserva, id_cliente1 (FK), id_horario1 (FK), fecha_compra, metodo_pago, monto, estado, num_confirmacion
reserva_asiento   → (pivot) id_reserva1 ↔ id_asiento1
orden_dulcerias   → id_orden, id_reserva2 (FK), total, cant_produc
orden_productos   → (pivot) id_orden1 ↔ id_producto1, cantidad
```

---

## ⚙️ Instalación

### Requisitos previos
- XAMPP con PHP 8.2+ y MySQL 8
- Composer

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/Joha2007/CinePlus.git
cd CinePlus

# 2. Instalar dependencias PHP
composer install

# 3. Copiar el archivo de entorno y configurarlo
cp .env.example .env
```

Editar `.env` con los datos de la base de datos:
```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CinePlus
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Generar la clave de aplicación
php artisan key:generate

# 5. Generar la clave de aplicación
composer require laravel/sanctum

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Crear enlace simbólico para imágenes
php artisan storage:link

# 8. Iniciar el servidor
php artisan serve
```

La aplicación estará disponible en **http://127.0.0.1:8000**

---

## 🌐 Funcionalidades Web

### Área Pública (sin login)

| Ruta | Descripción |
|---|---|
| `/` | Inicio — películas destacadas y sucursales |
| `/peliculas` | Catálogo completo de películas con filtro |
| `/peliculas/{id}` | Detalle de película con horarios disponibles |
| `/cartelera` | Funciones agrupadas por sucursal (tabs) |
| `/sucursales` | Listado de sucursales con dirección y contacto |
| `/login` | Inicio de sesión de cliente |
| `/registro` | Registro de nuevo cliente |

### Área Cliente (requiere sesión)

| Ruta | Descripción |
|---|---|
| `/reservar/{horario}` | Mapa interactivo de asientos + dulcería |
| `/mis-reservas` | Historial de reservas del cliente |
| `/reservas/{id}/editar` | Modificar asientos y/o snacks |
| `/reservas/{id}/cancelar` | Cancelar reserva |

**Flujo de reserva:**
1. El cliente selecciona una función desde la cartelera o el detalle de película
2. Elige asientos en el mapa interactivo (la disponibilidad se evalúa por horario, no de forma global)
3. Agrega snacks opcionales de la dulcería con control de cantidades
4. Elige método de pago (Tarjeta / Efectivo / Transferencia)
5. Confirma y recibe un código único de confirmación (`CP-XXXXXXXX`)
6. Puede editar o cancelar la reserva desde "Mis Reservas"

### Panel Administrador (requiere sesión)

Cada administrador gestiona **exclusivamente su propia sucursal**.

| Ruta | Módulo | Operaciones |
|---|---|---|
| `/admin/dashboard` | Panel de control | Estadísticas filtradas por sucursal |
| `/admin/peliculas` | Películas | Crear, editar, eliminar + subida de imagen |
| `/admin/horarios` | Funciones | Crear, editar, eliminar + validación de solapamiento |
| `/admin/categorias` | Categorías | Crear, eliminar |
| `/admin/salas` | Salas | Crear, editar, eliminar + generación automática de asientos |
| `/admin/productos` | Dulcería | Crear, editar, eliminar + subida de imagen |
| `/admin/reservas` | Reservas | Ver y cancelar reservas de la sucursal |

**Reglas de negocio:**
- Al crear una sala se generan automáticamente sus asientos (filas A–Z × N por fila)
- No se puede modificar la capacidad de una sala con reservas confirmadas activas
- No se puede eliminar una sala que tenga horarios programados
- No se puede programar una función que se solape con otra en la misma sala y fecha
- Las imágenes son opcionales al editar (se conserva la existente si no se sube una nueva)
- Cancelar una reserva libera los asientos y devuelve el stock de snacks

---

## 🔌 API REST

**Base URL:** `http://127.0.0.1:8000/api/v1`  
**Versión:** v1  
**Formato de respuesta:** JSON con Laravel API Resources (campos normalizados, sin datos internos de BD)  
**Rate limiting:** 60 req/min (clientes) · 120 req/min (admins)  
**Paginación:** parámetro `?per_page=N` (máx. 50, defecto 15) en endpoints de listado

### Autenticación

```http
POST /api/v1/auth/cliente/register   # Registro de cliente → devuelve token
POST /api/v1/auth/cliente/login      # Login de cliente   → devuelve token
POST /api/v1/auth/admin/login        # Login de admin     → devuelve token
```

Cabecera requerida en rutas protegidas:
```
Authorization: Bearer {token}
```

### Endpoints Públicos (sin token)

```http
GET  /api/v1/peliculas                        # ?titulo=&rango_edad=&categoria=&per_page=
GET  /api/v1/peliculas/{id}
GET  /api/v1/peliculas/{id}/horarios
GET  /api/v1/peliculas/{id}/categorias
GET  /api/v1/horarios                         # ?fecha=&sala_id=&pelicula_id=&proximos=1&per_page=
GET  /api/v1/horarios/{id}
GET  /api/v1/categorias
GET  /api/v1/sucursales
GET  /api/v1/sucursales/{id}/salas
GET  /api/v1/salas/{id}/asientos
```

### Endpoints Cliente (token Sanctum · 60 req/min)

```http
GET    /api/v1/cliente/me
POST   /api/v1/cliente/logout
GET    /api/v1/cliente/reservas              # ?estado=&per_page=
POST   /api/v1/cliente/reservas
GET    /api/v1/cliente/reservas/{id}
PUT    /api/v1/cliente/reservas/{id}
DELETE /api/v1/cliente/reservas/{id}
POST   /api/v1/cliente/ordenes
GET    /api/v1/cliente/ordenes/{id}
```

### Endpoints Admin (token Sanctum · 120 req/min)

```http
GET|POST|PUT|DELETE  /api/v1/admin/sucursales/{id}
GET|POST|PUT|DELETE  /api/v1/admin/salas/{id}
GET|POST|PUT|DELETE  /api/v1/admin/asientos/{id}
GET|POST|PUT|DELETE  /api/v1/admin/peliculas/{id}
GET|POST|PUT|DELETE  /api/v1/admin/horarios/{id}
GET|POST|PUT|DELETE  /api/v1/admin/categorias/{id}
GET|POST|PUT|DELETE  /api/v1/admin/productos/{id}   # ?nombre=&per_page=
GET|POST|PUT|DELETE  /api/v1/admin/clientes/{id}
GET|POST|PUT|DELETE  /api/v1/admin/administradores/{id}
GET|POST|PUT|DELETE  /api/v1/admin/reservas/{id}    # ?estado=&per_page=
GET|POST|PUT|DELETE  /api/v1/admin/ordenes/{id}

# Rutas anidadas
GET /api/v1/admin/sucursales/{id}/administradores
GET /api/v1/admin/clientes/{id}/reservas
GET /api/v1/admin/reservas/{id}/asientos
GET /api/v1/admin/reservas/{id}/ordenes
```

### Ejemplo de respuesta paginada

```json
{
  "data": [ { "id": 1, "titulo": "Harry Potter", ... } ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 42 }
}
```

---

## 🔐 Seguridad

- Contraseñas cifradas con **bcrypt** (`Hash::make`)
- Cabeceras **no-cache** en todas las rutas protegidas (evita el acceso con el botón "atrás" después del logout)
- Middleware independientes `auth.admin` y `auth.cliente` para el área web
- Autenticación API con **Laravel Sanctum** (tokens Bearer) — guards separados para clientes y admins
- **Rate limiting** en la API: 60 req/min para clientes, 120 req/min para admins (configurado en `AppServiceProvider`)
- Mensajes de validación en **español** (`lang/es/validation.php`)
- **Form Requests** dedicados para toda la validación de la API (20 clases en `app/Http/Requests/`)
- **API Resources** para transformar y normalizar todos los JSON de respuesta (11 clases en `app/Http/Resources/`)
- Cada administrador solo puede acceder y modificar datos de su propia sucursal
- Disponibilidad de asientos evaluada **por horario específico** (cruzando `reserva_asiento` + `reservas`), evitando que reservas de una función bloqueen asientos de otra función distinta en la misma sala

---

## 📁 Estructura del Proyecto

```
CinePlus/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          ← Controladores de la API REST (Sanctum)
│   │   │   └── Web/          ← Controladores de la aplicación web (sesión)
│   │   ├── Middleware/
│   │   │   ├── AuthAdmin.php
│   │   │   └── AuthCliente.php
│   │   ├── Requests/         ← 20 Form Requests (validación de API)
│   │   └── Resources/        ← 11 API Resources (transformación de JSON)
│   ├── Models/               ← 15 modelos Eloquent
│   └── Providers/
│       └── AppServiceProvider.php  ← Rate limiting registrado aquí
├── database/
│   ├── migrations/           ← 20 migraciones
│   └── seeders/              ← 15 seeders con datos de prueba
├── lang/
│   └── es/
│       └── validation.php    ← Mensajes de validación en español
├── resources/
│   └── views/
│       ├── admin/            ← Vistas del panel administrativo
│       ├── cliente/          ← Vistas de reservas del cliente
│       ├── horarios/         ← Cartelera pública
│       ├── layouts/          ← Layouts base (app.blade.php + admin.blade.php)
│       └── peliculas/        ← Catálogo público
├── routes/
│   ├── api.php               ← API REST bajo prefijo /api/v1
│   └── web.php               ← Rutas de la aplicación web
└── storage/app/public/
    ├── peliculas/            ← Pósters de películas subidos
    └── productos/            ← Imágenes de snacks subidas
```

---

## 👤 Credenciales de Prueba

### Panel Administrador → `http://127.0.0.1:8000/admin/login`

| Sucursal | Correo | Contraseña |
|---|---|---|
| CinePlus Multiplaza | admin@multiplaza.com | password |
| CinePlus Metrocentro | admin@metrocentro.com | password |
| CinePlus Galerías | admin@galerias.com | password |

### Cliente

Crear cuenta en `http://127.0.0.1:8000/registro`

---

## 📄 Licencia

Proyecto académico — Universidad Don Bosco, 2026.
