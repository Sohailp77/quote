<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>

    @php
        function fmt($v)
        {
            $n = (float) ($v ?: 0);
            if ($n >= 100000)
                return number_format($n / 100000, 1) . 'L';
            if ($n >= 1000)
                return number_format($n / 1000, 1) . 'K';
            return number_format($n, 0);
        }
    @endphp

    <div class="py-6 lg:py-8">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl relative flex justify-between items-center shadow-sm">
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            @endif

            <div class="bg-slate-50/50 dark:bg-slate-800/50 min-h-[500px] rounded-[40px] p-6 lg:p-8 font-sans">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <x-lucide-users class="w-5 h-5 text-brand-500 dark:text-brand-400" /> Team
                        </h1>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ count($employees) }} employees
                        </p>
                    </div>
                    <a href="{{ route('employees.create') }}"
                        class="flex items-center gap-2 bg-slate-900 dark:bg-brand-500 text-white px-5 py-2.5 rounded-full text-sm font-bold shadow hover:bg-slate-700 dark:hover:bg-brand-600 transition">
                        <x-lucide-user-plus class="w-4 h-4" /> Add Employee
                    </a>
                </div>

                <!-- Performance Table -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-[28px] shadow-[0_2px_16px_-2px_rgba(0,0,0,0.06)] overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <x-lucide-award class="w-4 h-4 text-brand-500 dark:text-brand-400" />
                        <h2 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Performance Ranking</h2>
                    </div>

                    @if(count($employees) === 0)
                        <div class="flex flex-col items-center justify-center py-16 text-slate-300 dark:text-slate-600">
                            <x-lucide-users class="w-12 h-12 mb-3" />
                            <p class="text-sm text-slate-400 dark:text-slate-500">No employees yet.</p>
                            <a href="{{ route('employees.create') }}"
                                class="mt-3 text-xs text-brand-500 dark:text-brand-400 font-semibold hover:underline">
                                Add your first employee &rarr;
                            </a>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @foreach($employees as $index => $emp)
                                @php
                                    $convRate = $emp->quotes_count > 0
                                        ? round(($emp->accepted_quotes_count / $emp->quotes_count) * 100)
                                        : 0;

                                    $badgeClass = 'bg-slate-200 dark:bg-slate-800 !text-slate-600 dark:text-slate-400';
                                    if ($index === 0)
                                        $badgeClass = 'bg-brand-500 text-white dark:text-white';
                                    elseif ($index === 1)
                                        $badgeClass = 'bg-slate-400 dark:bg-slate-600 text-white dark:text-white';
                                    elseif ($index === 2)
                                        $badgeClass = 'bg-amber-600 text-white dark:text-white';
                                @endphp
                                <div
                                    class="flex items-center justify-between px-6 py-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition group">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0 {{ $badgeClass }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <div
                                            class="w-9 h-9 rounded-full bg-gradient-to-tr from-slate-300 to-slate-200 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($emp->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $emp->name }}</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                                <x-lucide-mail class="w-3 h-3" /> {{ $emp->email }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-8">
                                        <div class="text-center hidden md:block">
                                            <p
                                                class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center justify-center gap-1">
                                                <x-lucide-file-text class="w-3 h-3 text-slate-400 dark:text-slate-500" />
                                                {{ $emp->quotes_count }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Quotes</p>
                                        </div>
                                        <div class="text-center hidden md:block">
                                            <p
                                                class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center justify-center gap-1">
                                                <x-lucide-dollar-sign class="w-3 h-3 text-slate-400 dark:text-slate-500" />
                                                {{ fmt($emp->quotes_sum_total_amount) }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Revenue</p>
                                        </div>
                                        <div class="text-center hidden lg:block">
                                            <p
                                                class="text-sm font-bold {{ $convRate >= 50 ? 'text-emerald-600 dark:text-emerald-400' : ($convRate > 0 ? 'text-amber-600' : 'text-slate-400 dark:text-slate-500') }}">
                                                {{ $convRate }}%
                                            </p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Conversion</p>
                                        </div>

                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST"
                                            onsubmit="return confirm('Remove this employee? Their quotes will remain.');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-xl text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition opacity-0 group-hover:opacity-100"
                                                title="Remove employee">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>