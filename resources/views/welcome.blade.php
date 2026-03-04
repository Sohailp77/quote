<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CatalogApp') }} - Premium Quote & Sales Management</title>

    <!-- Fonts (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Brand Color Script -->
    @php
        $settings = \App\Models\CompanySetting::whereIn('key', ['brand_color_primary'])
            ->pluck('value', 'key')
            ->toArray();
        $brandColor = $settings['brand_color_primary'] ?? '#6366f1';
        $providerName = config('app.provider_name', 'CatalogApp Pro');
        $providerShortName = config('app.provider_short_name', 'CA');
    @endphp

    <script>
        function hexToRgb(hex) {
            let r = 0, g = 0, b = 0;
            if (hex.length === 4) {
                r = parseInt(hex[1] + hex[1], 16);
                g = parseInt(hex[2] + hex[2], 16);
                b = parseInt(hex[3] + hex[3], 16);
            } else if (hex.length === 7) {
                r = parseInt(hex[1] + hex[2], 16);
                g = parseInt(hex[3] + hex[4], 16);
                b = parseInt(hex[5] + hex[6], 16);
            }
            return [r, g, b];
        }

        function mix(color1, color2, weight) {
            const w = weight / 100;
            const r = Math.round(color1[0] * w + color2[0] * (1 - w));
            const g = Math.round(color1[1] * w + color2[1] * (1 - w));
            const b = Math.round(color1[2] * w + color2[2] * (1 - w));
            return `${r} ${g} ${b}`;
        }

        const primaryHex = '{{ $brandColor }}';

        function applyTheme(mode = null) {
            const root = document.documentElement;
            const themeMode = mode || localStorage.getItem('theme') || 'system';

            if (themeMode === 'dark' || (themeMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }

            const rgb = hexToRgb(primaryHex);
            const white = [255, 255, 255];
            const black = [0, 0, 0];

            root.style.setProperty('--color-brand-50', mix(rgb, white, 5));
            root.style.setProperty('--color-brand-100', mix(rgb, white, 10));
            root.style.setProperty('--color-brand-200', mix(rgb, white, 30));
            root.style.setProperty('--color-brand-300', mix(rgb, white, 50));
            root.style.setProperty('--color-brand-400', mix(rgb, white, 70));
            root.style.setProperty('--color-brand-500', `${rgb[0]} ${rgb[1]} ${rgb[2]}`);
            root.style.setProperty('--color-brand-600', mix(rgb, black, 85));
            root.style.setProperty('--color-brand-700', mix(rgb, black, 70));
            root.style.setProperty('--color-brand-800', mix(rgb, black, 50));
            root.style.setProperty('--color-brand-900', mix(rgb, black, 30));
            root.style.setProperty('--color-brand-950', mix(rgb, black, 15));
        }

        applyTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => applyTheme());
        window.addEventListener('theme-changed', (e) => applyTheme(e.detail));
    </script>
</head>

<body class="font-sans antialiased bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-500 overflow-x-hidden">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/70 dark:bg-slate-950/70 backdrop-blur-xl border-b border-slate-100 dark:border-slate-900">
        <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-brand-500/20">
                    {{ $providerShortName }}
                </div>
                <span class="font-bold text-xl tracking-tight">{{ $providerName }}</span>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-slate-900 dark:bg-brand-500 text-white rounded-full text-sm font-bold shadow-lg shadow-slate-900/10 transition-transform hover:scale-105 active:scale-95">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 text-slate-600 dark:text-slate-400 font-bold hover:text-brand-600 transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-brand-600 text-white rounded-full text-sm font-bold shadow-lg shadow-brand-500/20 transition-transform hover:scale-105 active:scale-95">
                                Get Started
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-40 pb-24 lg:pt-56 lg:pb-40 overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute top-[20%] right-[-5%] w-[40%] h-[40%] bg-brand-500/10 blur-[120px] rounded-full pointer-events-none animate-pulse"></div>
        <div class="absolute bottom-[10%] left-[[-5%] w-[30%] h-[30%] bg-brand-600/10 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="max-w-[1400px] mx-auto px-6 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20 rounded-full text-brand-600 dark:text-brand-400 text-xs font-black uppercase tracking-widest mb-6 animate-fade-in-down">
                    ✨ Elevate your sales process
                </div>
                <h1 class="text-6xl lg:text-8xl font-black tracking-tight leading-[0.9] mb-8 animate-fade-in-down" style="animation-delay: 100ms">
                    Premium <span class="text-brand-600">Product</span> Catalog & <span class="text-brand-700">Quotes</span>.
                </h1>
                <p class="text-lg lg:text-xl text-slate-500 dark:text-slate-400 leading-relaxed max-w-2xl mb-12 animate-fade-in-down" style="animation-delay: 200ms">
                    Everything you need to showcase products, manage stock, and create stunning quotes for your customers in seconds. Fully integrated, lightning-fast, and beautiful.
                </p>

                <div class="flex flex-wrap gap-4 animate-fade-in-down" style="animation-delay: 300ms">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-brand-600 text-white rounded-[24px] text-lg font-bold shadow-2xl shadow-brand-500/30 transition-all hover:scale-105 hover:bg-brand-700 active:scale-95 flex items-center gap-3 group">
                            Start Creating Now
                            <x-lucide-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                    @endif
                    <a href="#features" class="px-8 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[24px] text-lg font-bold transition-all hover:bg-slate-50 dark:hover:bg-slate-800 active:scale-95">
                        Explore Features
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- App Preview Mockup -->
    <section class="pb-24 lg:pb-40 px-6">
        <div class="max-w-[1200px] mx-auto">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-600 to-brand-400 rounded-[42px] blur opacity-25 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[40px] shadow-2xl overflow-hidden aspect-[16/9] flex flex-col">
                    <!-- Top Bar -->
                    <div class="h-14 border-b border-slate-100 dark:border-slate-800 px-6 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 px-4 py-1.5 rounded-xl text-[10px] font-bold text-slate-400">
                            catalog-app.io/quotes/create
                        </div>
                        <div class="w-10"></div>
                    </div>
                    <!-- Content Fake UI -->
                    <div class="flex-1 p-8 grid grid-cols-12 gap-8">
                        <div class="col-span-8 flex flex-col gap-6">
                            <div class="h-10 w-48 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
                            <div class="h-64 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800 flex items-center justify-center">
                                <x-lucide-file-text class="w-12 h-12 text-slate-200 dark:text-slate-800" />
                            </div>
                        </div>
                        <div class="col-span-4 flex flex-col gap-6">
                            <div class="h-10 w-full bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-800/50 rounded-3xl p-6 flex flex-col gap-4">
                                <div class="h-4 w-full bg-brand-500/20 rounded-full"></div>
                                <div class="h-4 w-3/4 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                                <div class="mt-auto h-12 w-full bg-brand-600 rounded-2xl"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 lg:py-40 bg-slate-50 dark:bg-slate-900/50 relative overflow-hidden">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-4xl lg:text-5xl font-black mb-6">Designed for modern commerce.</h2>
                <p class="text-slate-500 dark:text-slate-400">Every detail has been crafted to help you sell more, faster, and with absolute professional precision.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-slate-900 p-10 rounded-[32px] border border-white dark:border-slate-800 shadow-sm transition-transform hover:-translate-y-2">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-8">
                        <x-lucide-file-plus class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-black mb-4">Smart Quoting</h3>
                    <p class="text-slate-500 dark:text-slate-400 leading-relaxed">Generate beautiful PDF quotes in seconds. Our auto-suggest features and instant tax calculation save you hours of work.</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white dark:bg-slate-900 p-10 rounded-[32px] border border-white dark:border-slate-800 shadow-sm transition-transform hover:-translate-y-2">
                    <div class="w-14 h-14 bg-brand-50 dark:bg-brand-500/10 rounded-2xl flex items-center justify-center text-brand-600 dark:text-brand-400 mb-8">
                        <x-lucide-package class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-black mb-4">Inventory Engine</h3>
                    <p class="text-slate-500 dark:text-slate-400 leading-relaxed">Manage your catalog with variants, stock tracking, and real-time adjustments. Never sell what you don't have.</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white dark:bg-slate-900 p-10 rounded-[32px] border border-white dark:border-slate-800 shadow-sm transition-transform hover:-translate-y-2">
                    <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-8">
                        <x-lucide-bar-chart-3 class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-black mb-4">Live Analytics</h3>
                    <p class="text-slate-500 dark:text-slate-400 leading-relaxed">Visualize your sales performance. Track trends, top products, and conversion rates with high-fidelity charts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-slate-100 dark:border-slate-900">
        <div class="max-w-[1400px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-brand-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                    {{ $providerShortName }}
                </div>
                <span class="font-bold text-lg tracking-tight">{{ $providerName }}</span>
            </div>

            <div class="flex gap-8 text-sm font-bold text-slate-400">
                <a href="#" class="hover:text-brand-600 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-brand-600 transition-colors">Terms of Service</a>
                <a href="mailto:support@example.com" class="hover:text-brand-600 transition-colors">Contact Support</a>
            </div>

            <div class="text-sm text-slate-400">
                &copy; {{ date('Y') }} {{ $providerName }}. Built for professionals.
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>
