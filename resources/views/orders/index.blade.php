<x-app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:bx-6 lg:px-8"> 
                <div class=" flex bg-white dark:bg-gray-800 shadow-md dark:shadow-none rounded-lg p-4">
                    @foreach ($orders as $order )
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 m-2">
                           <a href="{{ route('order.show', $order)}}">
                           <img src="{{ $order->products->first()?->getFirstMediaUrl() }}"  class="object-cover"  \>

                           <div class="justify-between w-full flex">
                            <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $order->products->first()?->title}}</h2>
                            <p class="p-2 bg-gray-200 rounded-xl text-sm text-slat-800">{{ $order->status}}</p>
                           </div>

                           <div class="flex justify-between align-baseline justify-self-center items-center font-medium text-center text-black">

                            <p class="text-2xl">
                                {{ money($order->total) }}
                            </p>

                            <span class="text-sm">
                                {{ $order->created_at }}
                            </span>
                            
                           </div>
                           </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
</x-app-layout>