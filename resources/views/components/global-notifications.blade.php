<!-- Global Notifications in Top Right Corner -->
<div class="fixed top-6 right-6 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none">

    <!-- Success Message -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-[-1rem]" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-x-10"
            class="bg-emerald-50 dark:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] flex items-start gap-3 backdrop-blur-md pointer-events-auto">
            <x-lucide-check-circle-2 class="w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-600 dark:text-emerald-400" />
            <div class="flex-1 font-medium text-sm">{{ session('success') }}</div>
            <button @click="show = false"
                class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-200 transition-colors p-0.5 mt-[-2px]">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Single Error Message -->
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-[-1rem]" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-x-10"
            class="bg-rose-50 dark:bg-rose-900/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] flex items-start gap-3 backdrop-blur-md pointer-events-auto">
            <x-lucide-alert-circle class="w-5 h-5 flex-shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" />
            <div class="flex-1 font-medium text-sm">{{ session('error') }}</div>
            <button @click="show = false"
                class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-200 transition-colors p-0.5 mt-[-2px]">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Validation Error Messages -->
    @if (isset($errors) && $errors->any())
        @foreach ($errors->all() as $error)
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-[-1rem]" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-x-10"
                class="bg-rose-50 dark:bg-rose-900/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] flex items-start gap-3 backdrop-blur-md pointer-events-auto">
                <x-lucide-alert-circle class="w-5 h-5 flex-shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" />
                <div class="flex-1 font-medium text-sm">{{ $error }}</div>
                <button @click="show = false"
                    class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-200 transition-colors p-0.5 mt-[-2px]">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endforeach
    @endif

</div>