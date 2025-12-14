# SneakerHub - Sistema E-commerce de Zapatillas

Sistema completo de comercio electrónico para tienda de zapatillas, desarrollado con Laravel 12, Tailwind CSS y Alpine.js.

## 🚀 Características

### Multi-Rol de Acceso
- **Owner**: Acceso total al sistema incluyendo configuración
- **Admin**: Gestión completa de productos, órdenes, usuarios y reportes
- **Worker**: Procesamiento de órdenes y gestión de stock
- **Client**: Navegación, carrito, compras y seguimiento de pedidos

### Gestión de Catálogo
- CRUD completo de productos con imágenes múltiples
- Categorías y marcas con imágenes
- Gestión de stock con movimientos auditados
- SKU automático y slugs SEO-friendly

### Carrito y Checkout
- Carrito para usuarios autenticados y guests
- Merge automático de carrito al hacer login
- Cupones de descuento (% y monto fijo)
- Selección de tallas

### Pagos
- Pago en efectivo
- Simulación de Yape con webhook
- Generación de boletas PDF

### Dashboard Admin
- KPIs en tiempo real
- Alertas de stock bajo
- Reportes de ventas (CSV/PDF)

## 📋 Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+ o MariaDB 10.6+
- Extensiones PHP: GD, BCMath, PDO, Mbstring

## 🔧 Instalación

### 1. Clonar e instalar dependencias

```bash
cd sneakerhub
composer install
npm install
```

### 2. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tu configuración de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sneakerhub
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Crear base de datos

Crear la base de datos `sneakerhub` manualmente en MySQL.

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate
php artisan db:seed
```

### 5. Crear enlace de almacenamiento

```bash
php artisan storage:link
```

### 6. Compilar assets

```bash
npm run build
# O para desarrollo:
npm run dev
```

### 7. Iniciar el servidor

```bash
php artisan serve
```

Visitar: http://localhost:8000

## 👤 Usuarios Demo

| Rol | Email | Contraseña |
|-----|-------|------------|
| Owner | owner@sneakerhub.com | password |
| Admin | admin@sneakerhub.com | password |
| Worker | worker@sneakerhub.com | password |
| Cliente | cliente@sneakerhub.com | password |

## ⚙️ Variables de Entorno

### Tienda
```env
STORE_NAME=SneakerHub
STORE_RUC=20123456789
STORE_ADDRESS="Av. Principal 123, Lima, Perú"
STORE_PHONE="+51 999 999 999"
STORE_EMAIL=tienda@sneakerhub.com
```

### Pagos y Envío
```env
TAX_RATE=18
DELIVERY_FEE=15.00
CURRENCY_CODE=PEN
CURRENCY_SYMBOL="S/"
```

### Yape (Simulación)
```env
YAPE_ENABLED=true
YAPE_PHONE_NUMBER=999999999
YAPE_TEST_MODE=true
```

## 📁 Estructura del Proyecto

```
sneakerhub/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/           # Controladores del panel admin
│   │   ├── Api/             # API endpoints y webhooks
│   │   ├── Auth/            # Autenticación
│   │   └── Client/          # Tienda pública
│   ├── Models/              # Modelos Eloquent
│   └── Services/            # Servicios de negocio
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/             # Datos de prueba
├── resources/
│   ├── css/                 # Estilos Tailwind
│   ├── js/                  # JavaScript/Alpine.js
│   └── views/               # Vistas Blade
└── routes/
    ├── web.php              # Rutas web
    └── api.php              # Rutas API
```

## 🔐 Permisos por Rol

| Permiso | Owner | Admin | Worker | Client |
|---------|-------|-------|--------|--------|
| Ver Dashboard | ✅ | ✅ | ✅ | ❌ |
| Gestionar Productos | ✅ | ✅ | ⚠️ Stock | ❌ |
| Gestionar Categorías | ✅ | ✅ | ❌ | ❌ |
| Gestionar Marcas | ✅ | ✅ | ❌ | ❌ |
| Ver Órdenes | ✅ | ✅ | ✅ | ❌ |
| Procesar Órdenes | ✅ | ✅ | ✅ | ❌ |
| Ver Reportes | ✅ | ✅ | ❌ | ❌ |
| Gestionar Usuarios | ✅ | ✅ | ❌ | ❌ |
| Configuración | ✅ | ❌ | ❌ | ❌ |

## 💳 Webhook Yape (Simulación)

Para simular confirmación de pago Yape:

```bash
curl -X POST http://localhost:8000/api/webhooks/yape \
  -H "Content-Type: application/json" \
  -d '{
    "yape_reference": "YAPE-XXXXXX",
    "transaction_id": "TXN-123456",
    "amount": 150.00,
    "status": "completed"
  }'
```

## 🧪 Tests

```bash
php artisan test
```

## 📄 Licencia

Este proyecto es software propietario de SneakerHub.

---

Desarrollado con ❤️ por el equipo SneakerHub
