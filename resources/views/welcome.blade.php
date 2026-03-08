<x-guest-layout>
<style>
    .professional-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    .dark .professional-bg {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
    }
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
    .icon-circle {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: rgba(8,145,178,0.08);
        border: 1px solid rgba(8,145,178,0.15);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1rem;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .card-glass:hover .icon-circle {
        background: rgba(8,145,178,0.14);
    }
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
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        opacity: 0.45;
    }
    .stat-divider + .stat-divider {
        border-top: 1px solid rgba(203,213,225,0.5);
    }
    @media (min-width: 640px) {
        .stat-divider + .stat-divider {
            border-top: none;
            border-left: 1px solid rgba(203,213,225,0.5);
        }
    }
</style>

<div class="professional-bg min-h-screen relative overflow-x-hidden">

    {{-- Decorative blobs --}}
    <div class="blob hidden sm:block" style="width:500px;height:500px;background:rgba(8,145,178,0.12);top:-150px;right:-100px;"></div>
    <div class="blob hidden sm:block" style="width:400px;height:400px;background:rgba(249,115,22,0.07);bottom:100px;left:-120px;"></div>
    <div class="blob hidden lg:block" style="width:300px;height:300px;background:rgba(8,145,178,0.08);bottom:300px;right:200px;"></div>

    {{-- NAV --}}
    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#0891b2,#0284c7);border-radius:9px;display:flex;align-items:center;justify-content:center;" class="shrink-0">
                    <x-heroicon-s-building-storefront class="w-5 h-5 text-white" />
                </div>
                <span class="font-bold text-base sm:text-lg text-slate-900 dark:text-white">StockMaster</span>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-main text-sm sm:text-base px-4 sm:px-6">
                        <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-main text-sm sm:text-base px-4 sm:px-6">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                        <span>Iniciar sesión</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-14 sm:pt-20 pb-12 sm:pb-16 text-center relative z-10">
        <span class="badge-cyan">
            <x-heroicon-o-sparkles class="w-3.5 h-3.5" />
            Sistema para restaurantes de mariscos
        </span>

        <h1 class="dark:text-white mt-6 mb-5" style="font-size:clamp(2rem,5vw,3.8rem);font-weight:800;line-height:1.15;color:#0f172a;">
            Control total de tu<br>
            <span style="background:linear-gradient(90deg,#0891b2,#0284c7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Inventario y Ventas</span>
        </h1>

        <p class="dark:text-zinc-400 mx-auto mb-8 sm:mb-10" style="font-size:clamp(0.95rem,2.5vw,1.1rem);color:#475569;max-width:520px;line-height:1.75;">
            Gestiona productos, bebidas, proveedores y cada venta de tu restaurante desde una sola plataforma. Simple y directo.
        </p>

        <div class="flex flex-wrap gap-3 justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-main">
                    <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                    Ir al Dashboard
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-main">
                    <x-heroicon-o-rocket-launch class="w-4 h-4" />
                    Comenzar
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            @endauth
        </div>
    </div>

    {{-- FEATURE CARDS --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pb-12 sm:pb-16 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

            <div class="card-glass p-6 sm:p-7">
                <div class="icon-circle">
                    <x-heroicon-o-cube class="w-6 h-6 text-cyan-600" />
                </div>
                <div class="font-bold text-base text-slate-900 dark:text-white mb-1.5">Stock en tiempo real</div>
                <div class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Registra entradas de mercancía, ajustes y mermas. Alertas cuando el stock baje del mínimo configurado.</div>
            </div>

            <div class="card-glass p-6 sm:p-7">
                <div class="icon-circle">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-cyan-600" />
                </div>
                <div class="font-bold text-base text-slate-900 dark:text-white mb-1.5">Menú y platillos</div>
                <div class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Administra platos de pescado, mixtos, ceviches y entradas. Precios flexibles por cada platillo del menú.</div>
            </div>

            <div class="card-glass p-6 sm:p-7">
                <div class="icon-circle">
                    <x-heroicon-o-receipt-percent class="w-6 h-6 text-cyan-600" />
                </div>
                <div class="font-bold text-base text-slate-900 dark:text-white mb-1.5">Registro de ventas</div>
                <div class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Genera folios por mesa, agrega platillos, aplica descuentos y cierra la cuenta en efectivo, tarjeta o transferencia.</div>
            </div>

            <div class="card-glass p-6 sm:p-7">
                <div class="icon-circle">
                    <x-heroicon-o-truck class="w-6 h-6 text-cyan-600" />
                </div>
                <div class="font-bold text-base text-slate-900 dark:text-white mb-1.5">Proveedores</div>
                <div class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Registra al proveedor de pescado, de cervezas, de refrescos. Vincula cada compra con su entrada al inventario.</div>
            </div>

            <div class="card-glass p-6 sm:p-7">
                <div class="icon-circle">
                    <x-heroicon-o-beaker class="w-6 h-6 text-cyan-600" />
                </div>
                <div class="font-bold text-base text-slate-900 dark:text-white mb-1.5">Bebidas y productos</div>
                <div class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Inventario separado por categorías: cervezas, refrescos, agua. Con precio de compra y precio de venta individual.</div>
            </div>

            <div class="card-glass p-6 sm:p-7">
                <div class="icon-circle">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-cyan-600" />
                </div>
                <div class="font-bold text-base text-slate-900 dark:text-white mb-1.5">Historial completo</div>
                <div class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Rastrea cada compra, ajuste o merma con fecha, motivo y proveedor. Control total de cada movimiento.</div>
            </div>

        </div>
    </div>

    {{-- STATS --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pb-12 sm:pb-16 relative z-10">
        <div class="stats-glass grid grid-cols-2 sm:grid-cols-4">

            <div class="stat-divider py-8 sm:py-9 px-4 text-center">
                <div class="text-3xl sm:text-4xl font-extrabold text-cyan-600 leading-none">0%</div>
                <div class="text-xs text-slate-400 uppercase tracking-widest mt-2 font-semibold">Pérdida de stock</div>
            </div>

            <div class="stat-divider py-8 sm:py-9 px-4 text-center">
                <div class="text-3xl sm:text-4xl font-extrabold text-cyan-600 leading-none">+45%</div>
                <div class="text-xs text-slate-400 uppercase tracking-widest mt-2 font-semibold">Eficiencia operativa</div>
            </div>

            <div class="stat-divider py-8 sm:py-9 px-4 text-center">
                <div class="text-3xl sm:text-4xl font-extrabold text-cyan-600 leading-none">24/7</div>
                <div class="text-xs text-slate-400 uppercase tracking-widest mt-2 font-semibold">Acceso disponible</div>
            </div>

            <div class="stat-divider py-8 sm:py-9 px-4 text-center">
                <div class="text-3xl sm:text-4xl font-extrabold text-cyan-600 leading-none">100%</div>
                <div class="text-xs text-slate-400 uppercase tracking-widest mt-2 font-semibold">Control de tu negocio</div>
            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    <div class="relative z-10 text-center pb-16 sm:pb-20 px-4">
        <span class="footer-pill inline-flex flex-wrap items-center justify-center gap-1">
            <x-heroicon-o-globe-alt class="w-3.5 h-3.5 inline-block" />
            © {{ date('Y') }} StockMaster
            <span class="opacity-40">·</span>
            Sistema de inventario para restaurantes
            <span class="opacity-40 hidden sm:inline">·</span>
            <span class="hidden sm:inline">Hecho con</span>
            <x-heroicon-o-heart class="w-3.5 h-3.5 text-cyan-500 hidden sm:inline-block" />
            <span class="hidden sm:inline">para la costa</span>
        </span>
    </div>

</div>
</x-guest-layout>
