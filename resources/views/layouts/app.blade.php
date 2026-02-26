<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-colors duration-500">

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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Theme Provider Simulation -->
    @php
        $settings = \App\Models\CompanySetting::whereIn('key', ['theme_mode', 'brand_color_primary', 'company_name'])->pluck('value', 'key')->toArray();
        $themeMode = $settings['theme_mode'] ?? 'system';
        $brandColor = $settings['brand_color_primary'] ?? '#6366f1';
        $companyName = $settings['company_name'] ?? 'CatalogApp';
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

        const themeMode = '{{ $themeMode }}';
        const primaryHex = '{{ $brandColor }}';

        function applyTheme() {
            const root = document.documentElement;
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
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    </script>
</head>

<body
    class="font-sans antialiased bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-500">
    <div class="min-h-screen">

        <!-- Header / Navigation -->
        @include('layouts.navigation', ['companyName' => $companyName])

        <!-- Main Content -->
        <main class="px-4 sm:px-6 lg:px-10 py-6">
            <div class="max-w-[1400px] mx-auto">
                {{ $slot }}
            </div>
        </main>
        <x-global-success />
        <x-global-error />
        <x-stock-warning-modal />
    </div>
    <!-- Initialize Lucide Icons -->
    <script>

    </script>
    @stack('scripts')
</body>

</html>