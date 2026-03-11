# Dash — Panel Administrativo

Panel de gestión empresarial construido con **Laravel 12**, **Livewire 4**, **Filament v5** y **Flux UI**.

---

## Stack

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 / PHP 8.2+ |
| Frontend reactivo | Livewire 4 + Flux 2 |
| Componentes UI | Filament v5 (forms, tables, actions, widgets) |
| Estilos | Tailwind CSS v4 (via Vite) |
| Permisos | Spatie Laravel Permission |
| PDF | barryvdh/laravel-dompdf |
| Base de datos | MySQL |
| Colas | Database queue |

---

## Módulos

- **Categorías** — CRUD de categorías de productos
- **Proveedores** — Gestión de proveedores
- **Productos** — Catálogo con stock mínimo y unidades
- **Ventas** — Registro de ventas e ítems
- **Inventario** — Movimientos de stock (entradas, salidas, ajustes, mermas, etc.)

---

## Módulo de Inventario

Los movimientos soportados son: `Compra`, `Venta`, `Ajuste`, `Merma`, `Devolución`, `Devolución a proveedor`, `Inventario inicial`, `Transferencia`.

### Widgets del dashboard de inventario

| Widget | Descripción |
|---|---|
| Entradas del mes | Suma de unidades ingresadas |
| Salidas del mes | Suma de unidades egresadas |
| Valor en inventario | Stock actual × costo unitario (L) |
| Bajo mínimo | Productos con stock_after < min_stock |
| Mermas del mes | Movimientos de tipo `waste` |

### Exportar CSV

La tabla tiene un botón **Exportar CSV** que descarga únicamente los registros visibles según los filtros activos:

| Filtro | Parámetro GET |
|---|---|
| Fecha desde | `?desde=YYYY-MM-DD` |
| Fecha hasta | `?hasta=YYYY-MM-DD` |
| Tipo | `?tipo=purchase\|sale\|waste\|…` |
| Producto | `?producto={id}` |
| Proveedor | `?proveedor={id}` |
| Bajo mínimo | `?bajo_min=1` |
| Búsqueda | `?q=texto` |

El archivo incluye BOM UTF-8 para compatibilidad con Excel.  
Ruta: `GET /inventario/exportar-csv` (middleware `auth`).

---

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Para desarrollo:

```bash
npm run dev
php artisan serve
```

---

## Moneda

Todos los valores monetarios se muestran en **Lempiras (L)**.
