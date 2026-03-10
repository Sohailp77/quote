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
        $brandColor = config('app.guest_brand_color', '#2563eb');
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

<body
    class="font-sans antialiased bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-500 overflow-x-hidden">
    <!-- Navbar -->
    <nav
        class="fixed top-0 left-0 right-0 z-50 bg-white/70 dark:bg-slate-950/70 backdrop-blur-xl border-b border-slate-100 dark:border-slate-900">
        <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-brand-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-brand-500/20">
                    {{ $providerShortName }}
                </div>
                <span class="font-bold text-xl tracking-tight">{{ $providerName }}</span>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-6 py-2.5 bg-slate-900 dark:bg-brand-500 text-white rounded-full text-sm font-bold shadow-lg shadow-slate-900/10 transition-transform hover:scale-105 active:scale-95">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-6 py-2.5 bg-brand-600 text-white rounded-full text-sm font-bold shadow-lg shadow-brand-500/20 transition-transform hover:scale-105 active:scale-95">
                            Log in
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-40 pb-24 lg:pt-56 lg:pb-40 overflow-hidden">
        <!-- Background Accents -->
        <div
            class="absolute top-[20%] right-[-5%] w-[40%] h-[40%] bg-brand-500/10 blur-[120px] rounded-full pointer-events-none animate-pulse">
        </div>
        <div
            class="absolute bottom-[10%] left-[-5%] w-[30%] h-[30%] bg-brand-600/10 blur-[120px] rounded-full pointer-events-none">
        </div>

        <div class="max-w-[1400px] mx-auto px-6 relative z-10 text-center">
            <div class="max-w-4xl mx-auto">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20 rounded-full text-brand-600 dark:text-brand-400 text-xs font-black uppercase tracking-widest mb-6 animate-fade-in-down">
                    ✨ Elevate your sales process
                </div>
                <h1 class="text-6xl lg:text-9xl font-black tracking-tight leading-[0.85] mb-8 animate-fade-in-down"
                    style="animation-delay: 100ms">
                    Premium <span
                        class="bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent">Quoting</span><br />Simplified.
                </h1>
                <p class="text-xl lg:text-2xl text-slate-500 dark:text-slate-400 leading-relaxed max-w-2xl mx-auto mb-12 animate-fade-in-down"
                    style="animation-delay: 200ms">
                    The ultimate sales engine for modern businesses. Effortless quotes, real-time stock, and stunning
                    analytics.
                </p>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-in-down" style="animation-delay: 300ms">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="px-10 py-5 bg-brand-600 text-white rounded-[24px] text-lg font-bold shadow-2xl shadow-brand-500/30 transition-all hover:scale-105 hover:bg-brand-700 active:scale-95 flex items-center gap-3 group">
                            Return to Dashboard
                            <x-lucide-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-10 py-5 bg-brand-600 text-white rounded-[24px] text-lg font-bold shadow-2xl shadow-brand-500/30 transition-all hover:scale-105 hover:bg-brand-700 active:scale-95 flex items-center gap-3 group">
                            Log in to get started
                            <x-lucide-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                    @endauth
                    <a href="#features"
                        class="px-10 py-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[24px] text-lg font-bold transition-all hover:bg-slate-50 dark:hover:bg-slate-800 active:scale-95">
                        Explore Features
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview Mockup -->
    <section class="pb-24 lg:pb-40 px-6 relative overflow-hidden" id="dashboard-preview">
        <div class="max-w-[1300px] mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-black mb-4">Command your business.</h2>
                <p class="text-slate-400">A high-fidelity cockpit built for speed and clarity.</p>
            </div>

            <div class="relative group">
                <!-- Glossy Container -->
                <div
                    class="absolute -inset-4 bg-gradient-to-tr from-brand-500/20 to-indigo-500/20 rounded-[48px] blur-2xl opacity-50">
                </div>

                <div
                    class="relative bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[40px] shadow-2xl overflow-hidden aspect-[16/10] lg:aspect-video flex flex-col p-4">
                    <!-- Fake Sidebar & Content -->
                    <div class="flex flex-1 gap-4 overflow-hidden">
                        <!-- Sidebar Mockup -->
                        <div
                            class="w-16 lg:w-20 bg-white dark:bg-slate-800/50 rounded-3xl p-4 flex flex-col items-center gap-6 border border-slate-100 dark:border-slate-800">
                            <div class="w-10 h-10 bg-brand-600 rounded-2xl"></div>
                            <div class="flex flex-col gap-4 mt-8">
                                <div class="w-8 h-8 bg-brand-500/20 rounded-xl"></div>
                                <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
                                <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
                                <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
                            </div>
                        </div>

                        <!-- Main Content Mockup -->
                        <div class="flex-1 flex flex-col gap-6 p-2 overflow-hidden">
                            <!-- Header Area -->
                            <div class="h-12 flex justify-between items-center">
                                <div class="h-6 w-32 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                                <div class="flex gap-2">
                                    <div class="h-10 w-10 bg-slate-100 dark:bg-slate-800 rounded-full"></div>
                                    <div class="h-10 w-10 bg-slate-100 dark:bg-slate-800 rounded-full"></div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-4 gap-4">
                                @foreach([1, 2, 3, 4] as $i)
                                    <div
                                        class="h-32 bg-white dark:bg-slate-800/50 rounded-3xl p-4 border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
                                        <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-lg"></div>
                                        <div class="h-6 w-full bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Large Chart Area -->
                            <div
                                class="flex-1 bg-white dark:bg-slate-800 p-6 rounded-[32px] border border-slate-100 dark:border-slate-700 relative overflow-hidden">
                                <div class="flex justify-between items-end h-full gap-2">
                                    @foreach([40, 70, 50, 90, 60, 30, 80, 55, 95] as $h)
                                        <div class="flex-1 bg-brand-500/20 dark:bg-brand-500/10 rounded-t-xl group/bar relative"
                                            style="height: {{ $h }}%">
                                            <div
                                                class="absolute inset-0 bg-brand-600 rounded-t-xl transform origin-bottom scale-y-0 group-hover/bar:scale-y-100 transition-transform duration-500">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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
                <h2 class="text-4xl lg:text-7xl font-black mb-6">Designed for scale.</h2>
                <p class="text-xl text-slate-500 dark:text-slate-400">Every detail has been crafted to help you sell
                    more,
                    faster, and with absolute professional precision.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Card 1 -->
                <div
                    class="group bg-white dark:bg-slate-900 p-10 rounded-[40px] border border-white dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all hover:shadow-2xl hover:shadow-brand-500/5 hover:-translate-y-4">
                    <div
                        class="w-16 h-16 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-8 group-hover:scale-110 transition-transform">
                        <x-lucide-file-plus class="w-8 h-8" />
                    </div>
                    <h3 class="text-2xl font-black mb-4">Smart Quoting</h3>
                    <p class="text-lg text-slate-500 dark:text-slate-400 leading-relaxed">Generate beautiful PDF quotes
                        in
                        seconds. Our auto-suggest features and instant tax calculation save you hours of work.</p>
                </div>

                <!-- Card 2 -->
                <div
                    class="group bg-white dark:bg-slate-900 p-10 rounded-[40px] border border-white dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all hover:shadow-2xl hover:shadow-brand-500/5 hover:-translate-y-4">
                    <div
                        class="w-16 h-16 bg-brand-50 dark:bg-brand-500/10 rounded-2xl flex items-center justify-center text-brand-600 dark:text-brand-400 mb-8 group-hover:scale-110 transition-transform">
                        <x-lucide-package class="w-8 h-8" />
                    </div>
                    <h3 class="text-2xl font-black mb-4">Inventory Engine</h3>
                    <p class="text-lg text-slate-500 dark:text-slate-400 leading-relaxed">Manage your catalog with
                        variants,
                        stock tracking, and real-time adjustments. Never sell what you don't have.</p>
                </div>

                <!-- Card 3 -->
                <div
                    class="group bg-white dark:bg-slate-900 p-10 rounded-[40px] border border-white dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all hover:shadow-2xl hover:shadow-brand-500/5 hover:-translate-y-4">
                    <div
                        class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-8 group-hover:scale-110 transition-transform">
                        <x-lucide-bar-chart-3 class="w-8 h-8" />
                    </div>
                    <h3 class="text-2xl font-black mb-4">Live Analytics</h3>
                    <p class="text-lg text-slate-500 dark:text-slate-400 leading-relaxed">Visualize your sales
                        performance.
                        Track trends, top products, and conversion rates with high-fidelity charts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 lg:py-40 px-6 relative">
        <div
            class="max-w-[1000px] mx-auto bg-slate-900 dark:bg-brand-600 rounded-[48px] p-12 lg:p-20 text-white relative overflow-hidden shadow-2xl shadow-brand-500/20">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 blur-3xl rounded-full"></div>
            <div class="relative z-10 text-center">
                <h2 class="text-4xl lg:text-7xl font-black mb-6">Ready to scale?</h2>
                <p class="text-xl text-white/70 mb-12 max-w-xl mx-auto">Skip the setup and start selling today. Contact
                    us directly to get your premium profile ready in minutes.</p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <!-- Secure WhatsApp Link -->
                    <a href="https://wa.me/{{ config('contact.whatsapp', '918080943707') }}?text=Hi%2C%20I'm%20interested%20in%20CatalogApp%20Pro!"
                        target="_blank"
                        class="w-full sm:w-auto px-8 py-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-3xl font-bold flex items-center justify-center gap-3 transition-all hover:scale-105 active:scale-95 group">
                        <x-lucide-phone class="w-6 h-6" />
                        Contact on WhatsApp
                        <x-lucide-arrow-right class="w-5 h-5 opacity-50 group-hover:translate-x-1" />
                    </a>

                    <!-- Secure Email (Obfuscated-ish link) -->
                    <a href="#"
                        onclick="window.location.href='mailto:' + atob('c3VwcG9ydEBjYXRhbG9nLWFwcC5pbyA='); return false;"
                        class="w-full sm:w-auto px-8 py-5 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-3xl font-bold flex items-center justify-center gap-3 transition-all hover:scale-105 active:scale-95 group">
                        <x-lucide-mail class="w-6 h-6" />
                        Send Email
                        <x-lucide-arrow-right class="w-5 h-5 opacity-50 group-hover:translate-x-1" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-slate-100 dark:border-slate-900">
        <div class="max-w-[1400px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 bg-brand-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                    {{ $providerShortName }}
                </div>
                <span class="font-bold text-lg tracking-tight">{{ $providerName }}</span>
            </div>

            <div class="flex gap-8 text-sm font-bold text-slate-400">
                <a href="#" class="hover:text-brand-600 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-brand-600 transition-colors">Terms of Service</a>
                <a href="#contact" class="hover:text-brand-600 transition-colors">Contact Support</a>
            </div>

            <div class="text-sm text-slate-400">
                &copy; {{ date('Y') }} {{ $providerName }}. Built for professionals.
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>