<header class="bg-slate-100 dark:bg-slate-950 px-4 sm:px-6 lg:px-10 pt-6 pb-2 transition-colors duration-500"
    x-data="{ showUserMenu: false, showMobileMenu: false }">
    <div class="max-w-[1400px] mx-auto">
        <div
            class="bg-white dark:bg-slate-900 rounded-[28px] shadow-[0_4px_24px_rgba(0,0,0,0.06)] dark:shadow-none dark:border dark:border-slate-800 px-6 py-3 flex items-center justify-between gap-4 transition-colors duration-500">

            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 flex-shrink-0">
                <div
                    class="w-9 h-9 bg-brand-600 rounded-full flex items-center justify-center text-white font-bold text-base leading-none">
                    {{ strtoupper(substr($companyName ?? 'C', 0, 1)) }}
                </div>
                <span
                    class="font-bold text-base tracking-tight text-slate-900 dark:text-white hidden sm:block truncate max-w-[140px]">
                    {{ $companyName ?? 'CatalogApp' }}
                </span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Dashboard
                </a>
                <a href="{{ route('categories.index') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Categories
                </a>
                <a href="{{ route('products.index') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Products
                </a>
                <a href="{{ route('quotes.index') }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ request()->routeIs('quotes.*') ? 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Quotes
                </a>
                @if(Auth::user() && Auth::user()->isBoss())
                    <a href="{{ route('settings.index') }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Settings
                    </a>
                    <a href="{{ route('analytics.index') }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ request()->routeIs('analytics.*') ? 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Analytics
                    </a>
                @endif
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- Search -->
                <div class="relative hidden lg:block">
                    <input type="text" placeholder="Search..."
                        class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-400 dark:text-slate-300 placeholder-slate-400 rounded-full px-4 py-1.5 w-48 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white dark:focus:bg-slate-900 transition-all border-0 ring-1 ring-inset ring-slate-200 dark:ring-slate-700">
                    <x-lucide-search class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 dark:text-slate-500" />
                </div>

                <!-- Settings Icon -->
                @if(Auth::user()->isBoss())
                    <a href="{{ route('settings.index') }}"
                        class="w-9 h-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all relative">
                        <x-lucide-settings class="w-4 h-4" />
                    </a>
                @endif

                <!-- User Avatar & Dropdown -->
                <div class="relative" @click.away="showUserMenu = false">
                    <button @click="showUserMenu = !showUserMenu"
                        class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300 hidden sm:block">
                            {{ explode(' ', Auth::user()->name)[0] }}
                        </span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 hidden sm:block" />
                    </button>

                    <div x-show="showUserMenu" style="display: none;"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,0.12)] border border-slate-100 dark:border-slate-800 z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
                            <x-lucide-user class="w-4 h-4" /> Profile
                        </a>

                        @if(Auth::user()->isBoss())
                            <a href="{{ route('settings.index') }}"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
                                <x-lucide-settings class="w-4 h-4" /> Settings
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}"
                            class="m-0 border-t border-slate-100 dark:border-slate-800">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors text-left">
                                <x-lucide-log-out class="w-4 h-4" /> Log Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile menu toggle -->
                <button @click="showMobileMenu = !showMobileMenu"
                    class="md:hidden w-9 h-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <x-lucide-menu x-show="!showMobileMenu" class="w-4 h-4" />
                    <x-lucide-x x-show="showMobileMenu" style="display: none;" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Mobile Nav Dropdown -->
        <div x-show="showMobileMenu" style="display: none;"
            class="md:hidden mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-800 overflow-hidden">
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-5 py-3 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Dashboard</a>
            <a href="{{ route('categories.index') }}"
                class="flex items-center px-5 py-3 text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Categories</a>
            <a href="{{ route('products.index') }}"
                class="flex items-center px-5 py-3 text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Products</a>
            <a href="{{ route('quotes.index') }}"
                class="flex items-center px-5 py-3 text-sm font-medium transition-colors {{ request()->routeIs('quotes.*') ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Quotes</a>

            @if(Auth::user()->isBoss())
                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-5 py-3 text-sm font-medium transition-colors {{ request()->routeIs('settings.*') ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Settings</a>
                <a href="{{ route('analytics.index') }}"
                    class="flex items-center px-5 py-3 text-sm font-medium transition-colors {{ request()->routeIs('analytics.*') ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Analytics</a>
            @endif

            <div class="border-t border-slate-100 dark:border-slate-800 px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ Auth::user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-xs text-red-500 dark:text-red-400 font-medium">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</header>