<x-guest-layout>
<style>
    .professional-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    .dark .professional-bg {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
    }

    .card-login-flux {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.1), 0 8px 16px -4px rgba(0, 0, 0, 0.06) !important;
    }
    .dark .card-login-flux {
        background: rgba(39, 39, 42, 0.85) !important;
        border: 1px solid rgba(63, 63, 70, 0.4) !important;
    }

    /* ── FIX: Flux inputs visibles en light mode ── */
    /* Labels */
    [data-flux-label],
    .card-login-flux label {
        color: #374151 !important;
    }
    /* Input text */
    [data-flux-input] input,
    [data-flux-input] {
        color: #111827 !important;
        background: #ffffff !important;
        border-color: #d1d5db !important;
    }
    [data-flux-input] input::placeholder {
        color: #9ca3af !important;
    }
    /* Checkbox label */
    [data-flux-checkbox-label] {
        color: #4b5563 !important;
    }
    /* Heading / subheading dentro de la card */
    .card-login-flux [data-flux-heading] {
        color: #111827 !important;
    }
    .card-login-flux [data-flux-subheading] {
        color: #6b7280 !important;
    }
    /* Dark mode — revertir a colores claros de Flux */
    .dark [data-flux-label],
    .dark .card-login-flux label { color: #e4e4e7 !important; }
    .dark [data-flux-input] input,
    .dark [data-flux-input] { color: #f4f4f5 !important; background: #27272a !important; border-color: #3f3f46 !important; }
    .dark [data-flux-input] input::placeholder { color: #71717a !important; }
    .dark [data-flux-checkbox-label] { color: #a1a1aa !important; }
    .dark .card-login-flux [data-flux-heading]    { color: #f4f4f5 !important; }
    .dark .card-login-flux [data-flux-subheading] { color: #a1a1aa !important; }

    /* Blob decorativo */
    .blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        opacity: 0.5;
        z-index: 0;
    }

    /* Divider con texto */
    .divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0.25rem 0;
    }
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(203, 213, 225, 0.6);
    }
    .dark .divider::before,
    .dark .divider::after {
        background: rgba(63, 63, 70, 0.5);
    }

    /* Logo badge */
    .logo-badge {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #0891b2, #0284c7);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 8px 24px rgba(8, 145, 178, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
        margin: 0 auto;
    }
    .logo-badge:hover {
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 12px 32px rgba(8, 145, 178, 0.45);
    }

    /* Pill footer */
    .footer-pill {
        background: rgba(255,255,255,0.6);
        border: 1px solid rgba(203,213,225,0.7);
        backdrop-filter: blur(8px);
        border-radius: 999px;
        padding: 0.55rem 1.25rem;
    }
    .dark .footer-pill {
        background: rgba(39,39,42,0.6);
        border-color: rgba(63,63,70,0.5);
    }
</style>

<div class="professional-bg min-h-screen flex flex-col items-center justify-center px-6 py-12 relative overflow-hidden">

    {{-- Blobs decorativos --}}
    <div class="blob" style="width:480px;height:480px;background:rgba(8,145,178,0.1);top:-120px;right:-80px;"></div>
    <div class="blob" style="width:360px;height:360px;background:rgba(249,115,22,0.06);bottom:-80px;left:-100px;"></div>

    {{-- Logo --}}
    <div class="mb-7 text-center z-10">
        <div class="logo-badge">🐟</div>
        <p class="mt-3 text-sm font-semibold text-zinc-500 dark:text-zinc-400 tracking-widest uppercase">StockMaster</p>
    </div>

    {{-- Card --}}
    <flux:card class="card-login-flux w-full max-w-md p-8 z-10">

        {{-- Header --}}
        <div class="text-center mb-7">
            <flux:heading size="xl" level="1" class="text-zinc-900 dark:text-white">
                Bienvenido de vuelta
            </flux:heading>
            <flux:subheading class="text-zinc-500 dark:text-zinc-400 mt-1">
                Ingresa tus credenciales para continuar
            </flux:subheading>
        </div>

        {{-- Errores de validación --}}
        <x-validation-errors class="mb-5" />

        {{-- Session status --}}
        @session('status')
            <div class="mb-5 flex items-center gap-2 text-sm text-cyan-700 bg-cyan-50 dark:bg-cyan-900/20 dark:text-cyan-300 px-4 py-3 rounded-xl border border-cyan-100 dark:border-cyan-800">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ $value }}
            </div>
        @endsession

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <flux:input
                label="{{ __('Correo electrónico') }}"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                placeholder="usuario@tuempresa.com"
                icon="envelope"
            />

            <div class="space-y-1.5">
                <flux:input
                    label="{{ __('Contraseña') }}"
                    type="password"
                    name="password"
                    required
                    viewable
                    icon="key"
                />
            </div>

            <flux:button
                type="submit"
                variant="primary"
                class="w-full shadow-lg shadow-cyan-500/20 py-3 mt-1"
            >
                Iniciar sesión
            </flux:button>
        </form>

    </flux:card>
</div>
</x-guest-layout>