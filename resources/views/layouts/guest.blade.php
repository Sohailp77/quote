<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts (Plus Jakarta Sans) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">

        <!-- Scripts -->
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
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-500">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-[grid-slate-200/50] dark:bg-[grid-slate-800/30] relative overflow-hidden">
            <!-- Background Orbs -->
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-500/10 blur-[120px] rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-brand-600/10 blur-[120px] rounded-full pointer-events-none"></div>

            <div class="w-full sm:max-w-md relative z-10">
                <div class="flex justify-center mb-10">
                    <a href="/" class="group transition-transform hover:scale-105 active:scale-95">
                        <div class="w-16 h-16 bg-brand-600 rounded-3xl shadow-xl shadow-brand-500/20 flex items-center justify-center text-white font-bold text-3xl">
                            {{ $providerShortName }}
                        </div>
                    </a>
                </div>

                <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-white dark:border-slate-800 shadow-2xl shadow-slate-200/50 dark:shadow-none rounded-[32px] overflow-hidden p-8 sm:p-10">
                    {{ $slot }}
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-400 dark:text-slate-500 font-medium">
                        &copy; {{ date('Y') }} {{ $providerName }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
