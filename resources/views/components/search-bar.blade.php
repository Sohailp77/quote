@props(['placeholder' => 'Search...'])

<form action="{{ url()->current() }}" method="GET" class="relative group">
    <div
        class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
        <x-lucide-search class="w-4 h-4" />
    </div>
    <input type="text" name="search" value="{{ request('search') }}"
        class="w-full md:w-64 lg:w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-sm rounded-2xl pl-11 pr-10 py-2.5 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 font-medium"
        placeholder="{{ $placeholder }}">

    @if (request('search'))
        <a href="{{ url()->current() }}"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
            <x-lucide-x class="w-4 h-4" />
        </a>
    @endif
</form>
