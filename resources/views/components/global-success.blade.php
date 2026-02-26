<!-- Global Success in bottom right corner with auto remove and manual x remove option -->
<div class="fixed bottom-4 right-4 z-50">
    @if(session('success'))
        <div
            class="bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in-down mb-2">
            <x-lucide-check-circle-2 class="w-5 h-5 cursor-pointer" onclick="this.parentElement.remove()" />
            <span class="font-medium font-sans">{{ session('success') }}</span>
        </div>
    @endif
</div>
<script>
    setTimeout(() => {
        document.querySelector('.fixed.bottom-4.right-4.z-50').remove();
    }, 10000);
    document.querySelector('.fixed.bottom-4.right-4.z-50').addEventListener('click', () => {
        document.querySelector('.fixed.bottom-4.right-4.z-50').remove();
    });
</script>