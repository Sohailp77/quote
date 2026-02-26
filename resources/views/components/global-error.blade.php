<!-- Global Error in bottom right corner with auto remove and manual x remove option -->
<div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2">
    @if(session('error'))
        <div class="bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
            <x-lucide-x-circle class="w-5 h-5 cursor-pointer" onclick="this.parentElement.remove()" />
            <span class="font-medium font-sans">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down">
                <x-lucide-x-circle class="w-5 h-5 cursor-pointer" onclick="this.parentElement.remove()" />
                <span class="font-medium font-sans">{{ $error }}</span>
            </div>
        @endforeach
    @endif
</div>
<script>
    setTimeout(() => {
        document.querySelectorAll('.fixed.bottom-4.right-4.z-50 > div').forEach(el => el.remove());
    }, 10000);
</script>