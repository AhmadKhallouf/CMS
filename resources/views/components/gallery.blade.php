<div x-data="carousel()" x-init="init()" class="relative w-full">
    <!-- Carousel wrapper -->
    <div class="relative h-56 overflow-hidden rounded-lg md:h-[500px] bg-gray-100 dark:bg-gray-800">
        <div class="relative w-full h-full">
            @foreach ($media as $index => $image)
                <div x-show="active === {{ $index }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 w-full h-full flex items-center justify-center"
                     x-cloak>
                    <img src="{{ $image->getUrl() }}"
                         class="max-w-full max-h-full w-auto h-auto object-contain"
                         alt="Product image {{ $index + 1 }}">
                </div>
            @endforeach
        </div>
    </div>

    <!-- Slider indicators -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
        @foreach ($media as $index => $image)
            <button @click="active = {{ $index }}"
                    class="w-2 h-2 rounded-full transition-all duration-300"
                    :class="{
                        'bg-blue-600 w-4': active === {{ $index }},
                        'bg-gray-400 dark:bg-gray-500': active !== {{ $index }}
                    }"
                    :aria-label="'Go to slide {{ $index + 1 }}'"></button>
        @endforeach
    </div>

    <!-- Previous/Next buttons -->
    <button @click="prev()"
            class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-white/30 dark:bg-gray-800/30 hover:bg-white/50 dark:hover:bg-gray-800/50 rounded-full p-2 z-10 transition-colors">
        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
        </svg>
        <span class="sr-only">Previous</span>
    </button>
    
    <button @click="next()"
            class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-white/30 dark:bg-gray-800/30 hover:bg-white/50 dark:hover:bg-gray-800/50 rounded-full p-2 z-10 transition-colors">
        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Next</span>
    </button>
</div>

<script>
    function carousel() {
        return {
            active: 0,
            total: {{ count($media) }},
            init() {
                // Auto-advance every 5 seconds (optional)
                // setInterval(() => {
                //     this.next();
                // }, 5000);
            },
            next() {
                this.active = (this.active + 1) % this.total;
            },
            prev() {
                this.active = (this.active - 1 + this.total) % this.total;
            }
        }
    }
</script>