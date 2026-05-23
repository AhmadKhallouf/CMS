<li class="flex gap-4 border-b border-gray-100 py-6 last:border-0">
    <div class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-100">
        <img
            class="h-full w-full object-cover"
            src="{{  $item->product->getFirstMediaUrl() }}"
            alt="{{ $item->variant?->title ?? $item->product->title }}"
        />
    </div>

    <div class="flex min-w-0 flex-1 flex-col">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1 pr-2">
                <p class="text-base font-semibold text-gray-900">{{ $item->product->title }}</p>
                @if ($item->variant)
                    <p class="mt-1 text-sm text-gray-400">
                        @foreach ($item->variant->parent() as $ancestor)
                            {{ $ancestor->title }}@if (! $loop->last) | @endif
                        @endforeach
                    </p>
                @endif
            </div>

            <div class="flex shrink-0 flex-col items-end gap-3">
                <p class="text-base font-semibold text-gray-900">
                    {{ money($item->variant ? $item->variant->price : $item->product->price) }}
                </p>

                <div class="inline-flex h-8 overflow-hidden rounded-md border border-gray-200 text-gray-600">
                    <button
                        type="button"
                        wire:click="decrement"
                        class="flex w-9 items-center justify-center bg-gray-50 transition hover:bg-gray-900 hover:text-white"
                    >−</button>
                    <span class="flex min-w-[2.5rem] items-center justify-center bg-white px-2 text-sm font-medium">
                        {{ $item->quantity }}
                    </span>
                    <button
                        type="button"
                        wire:click="increment"
                        class="flex w-9 items-center justify-center bg-gray-50 transition hover:bg-gray-900 hover:text-white"
                    >+</button>
                </div>

                <button
                    type="button"
                    wire:click="remove"
                    class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md bg-gray-100 px-3 py-1.5 text-sm text-gray-600 transition hover:bg-gray-900 hover:text-white"
                >
                    <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Remove</span>
                </button>
            </div>
        </div>
    </div>
</li>

