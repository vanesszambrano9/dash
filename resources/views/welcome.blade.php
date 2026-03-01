<x-guest-layout>
<style>
    .professional-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    .dark .professional-bg {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
    }

    /* NAV */
    .nav-glass {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255,255,255,0.4);
        box-shadow: 0 1px 12px rgba(0,0,0,0.06);
    }
    .dark .nav-glass {
        background: rgba(24,24,27,0.7);
        border-bottom: 1px solid rgba(63,63,70,0.4);
    }

    /* CARDS */
    .card-glass {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.4);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08), 0 4px 10px -3px rgba(0,0,0,0.06);
        border-radius: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dark .card-glass {
        background: rgba(39,39,42,0.8);
        border: 1px solid rgba(63,63,70,0.4);
    }
    .card-glass:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -8px rgba(8,145,178,0.15), 0 8px 16px -4px rgba(0,0,0,0.08);
    }

    /* STATS BAR */
    .stats-glass {
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .dark .stats-glass {
        background: rgba(39,39,42,0.6);
        border: 1px solid rgba(63,63,70,0.4);
    }

    /* BADGE */
    .badge-cyan {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(8,145,178,0.08);
        border: 1px solid rgba(8,145,178,0.2);
        color: #0891b2;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
    }
    .dark .badge-cyan {
        background: rgba(8,145,178,0.12);
        border-color: rgba(8,145,178,0.3);
        color: #22d3ee;
    }

    /* ICON CIRCLE */
    .icon-circle {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: rgba(8,145,178,0.08);
        border: 1px solid rgba(8,145,178,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1rem;
        transition: background 0.2s;
    }
    .card-glass:hover .icon-circle {
        background: rgba(8,145,178,0.14);
    }

    /* CTA BUTTONS */
    .btn-main {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: linear-gradient(135deg, #0891b2, #0284c7);
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.75rem 1.75rem;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(8,145,178,0.3);
        transition: all 0.2s;
    }
    .btn-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(8,145,178,0.4);
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255,255,255,0.8);
        color: #334155;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.75rem 1.75rem;
        border-radius: 10px;
        text-decoration: none;
        border: 1px solid rgba(203,213,225,0.8);
        backdrop-filter: blur(8px);
        transition: all 0.2s;
    }
    .btn-outline:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,1);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .dark .btn-outline {
        background: rgba(39,39,42,0.8);
        color: #e4e4e7;
        border-color: rgba(63,63,70,0.6);
    }

    /* FOOTER PILL */
    .footer-pill {
        background: rgba(255,255,255,0.5);
        border: 1px solid rgba(203,213,225,0.6);
        backdrop-filter: blur(8px);
        border-radius: 999px;
        padding: 0.6rem 1.5rem;
        font-size: 0.82rem;
        color: #64748b;
    }
    .dark .footer-pill {
        background: rgba(39,39,42,0.5);
        border-color: rgba(63,63,70,0.5);
        color: #a1a1aa;
    }

    /* DECORATIVE BLOBS */
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        opacity: 0.45;
    }
</style>

<div class="professional-bg min-h-screen relative overflow-x-hidden">

    {{-- Decorative blobs --}}
    <div class="blob" style="width:500px;height:500px;background:rgba(8,145,178,0.12);top:-150px;right:-100px;"></div>
    <div class="blob" style="width:400px;height:400px;background:rgba(249,115,22,0.07);bottom:100px;left:-120px;"></div>
    <div class="blob" style="width:300px;height:300px;background:rgba(8,145,178,0.08);bottom:300px;right:200px;"></div>

    {{-- NAV --}}
    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#0891b2,#0284c7);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;">🐟</div>
                <span style="font-weight:700;font-size:1.15rem;color:#0f172a;" class="dark:text-white">StockMaster</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-main">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-main">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <div class="max-w-6xl mx-auto px-6 pt-20 pb-16 text-center relative z-10">
        <span class="badge-cyan">🦐 Sistema para restaurantes de mariscos</span>

        <h1 style="font-size:clamp(2.4rem,5vw,3.8rem);font-weight:800;line-height:1.15;color:#0f172a;margin-top:1.5rem;margin-bottom:1.25rem;" class="dark:text-white">
            Control total de tu<br>
            <span style="background:linear-gradient(90deg,#0891b2,#0284c7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Inventario y Ventas</span>
        </h1>

        <p style="font-size:1.1rem;color:#475569;max-width:520px;margin:0 auto 2.5rem;line-height:1.75;" class="dark:text-zinc-400">
            Gestiona productos, bebidas, proveedores y cada venta de tu restaurante desde una sola plataforma. Simple y directo.
        </p>

        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-main">Ir al Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="btn-main">Comenzar →</a>
            @endauth
        </div>
    </div>

    {{-- FEATURE CARDS --}}
    <div class="max-w-6xl mx-auto px-6 pb-16 relative z-10">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.25rem;">

            <div class="card-glass p-7">
                <div class="icon-circle">📦</div>
                <div style="font-weight:700;font-size:1rem;color:#0f172a;margin-bottom:0.5rem;" class="dark:text-white">Stock en tiempo real</div>
                <div style="font-size:0.88rem;color:#64748b;line-height:1.65;" class="dark:text-zinc-400">Registra entradas de mercancía, ajustes y mermas. Alertas cuando el stock baje del mínimo configurado.</div>
            </div>

            <div class="card-glass p-7">
                <div class="icon-circle">🍽️</div>
                <div style="font-weight:700;font-size:1rem;color:#0f172a;margin-bottom:0.5rem;" class="dark:text-white">Menú y platillos</div>
                <div style="font-size:0.88rem;color:#64748b;line-height:1.65;" class="dark:text-zinc-400">Administra platos de pescado, mixtos, ceviches y entradas. Precios flexibles por cada platillo del menú.</div>
            </div>

            <div class="card-glass p-7">
                <div class="icon-circle">🧾</div>
                <div style="font-weight:700;font-size:1rem;color:#0f172a;margin-bottom:0.5rem;" class="dark:text-white">Registro de ventas</div>
                <div style="font-size:0.88rem;color:#64748b;line-height:1.65;" class="dark:text-zinc-400">Genera folios por mesa, agrega platillos, aplica descuentos y cierra la cuenta en efectivo, tarjeta o transferencia.</div>
            </div>

            <div class="card-glass p-7">
                <div class="icon-circle">🚛</div>
                <div style="font-weight:700;font-size:1rem;color:#0f172a;margin-bottom:0.5rem;" class="dark:text-white">Proveedores</div>
                <div style="font-size:0.88rem;color:#64748b;line-height:1.65;" class="dark:text-zinc-400">Registra al proveedor de pescado, de cervezas, de refrescos. Vincula cada compra con su entrada al inventario.</div>
            </div>

            <div class="card-glass p-7">
                <div class="icon-circle">🍺</div>
                <div style="font-weight:700;font-size:1rem;color:#0f172a;margin-bottom:0.5rem;" class="dark:text-white">Bebidas y productos</div>
                <div style="font-size:0.88rem;color:#64748b;line-height:1.65;" class="dark:text-zinc-400">Inventario separado por categorías: cervezas, refrescos, agua. Con precio de compra y precio de venta individual.</div>
            </div>

            <div class="card-glass p-7">
                <div class="icon-circle">📊</div>
                <div style="font-weight:700;font-size:1rem;color:#0f172a;margin-bottom:0.5rem;" class="dark:text-white">Historial completo</div>
                <div style="font-size:0.88rem;color:#64748b;line-height:1.65;" class="dark:text-zinc-400">Rastrea cada compra, ajuste o merma con fecha, motivo y proveedor. Control total de cada movimiento.</div>
            </div>

        </div>
    </div>

    {{-- STATS --}}
    <div class="max-w-6xl mx-auto px-6 pb-16 relative z-10">
        <div class="stats-glass" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
            <div style="padding:2.25rem 1.5rem;text-align:center;border-right:1px solid rgba(203,213,225,0.5);">
                <div style="font-size:2.2rem;font-weight:800;color:#0891b2;line-height:1;">0%</div>
                <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.09em;margin-top:0.4rem;font-weight:600;">Pérdida de stock</div>
            </div>
            <div style="padding:2.25rem 1.5rem;text-align:center;border-right:1px solid rgba(203,213,225,0.5);">
                <div style="font-size:2.2rem;font-weight:800;color:#0891b2;line-height:1;">+45%</div>
                <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.09em;margin-top:0.4rem;font-weight:600;">Eficiencia operativa</div>
            </div>
            <div style="padding:2.25rem 1.5rem;text-align:center;border-right:1px solid rgba(203,213,225,0.5);">
                <div style="font-size:2.2rem;font-weight:800;color:#0891b2;line-height:1;">24/7</div>
                <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.09em;margin-top:0.4rem;font-weight:600;">Acceso disponible</div>
            </div>
            <div style="padding:2.25rem 1.5rem;text-align:center;">
                <div style="font-size:2.2rem;font-weight:800;color:#0891b2;line-height:1;">100%</div>
                <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.09em;margin-top:0.4rem;font-weight:600;">Control de tu negocio</div>
            </div>
        </div>
    </div>

    {{-- CTA BOTTOM --}}
    <div class="relative z-10 text-center pb-20 px-6">

        <div style="margin-top:2.5rem;">
            <span class="footer-pill">
                © {{ date('Y') }} StockMaster &nbsp;·&nbsp; Sistema de inventario para restaurantes &nbsp;·&nbsp; Hecho con 🌊 para la costa
            </span>
        </div>
    </div>

</div>
</x-guest-layout>