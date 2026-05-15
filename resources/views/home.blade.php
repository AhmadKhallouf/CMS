<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <h1 class="text-5xl text-center mb-4">Featured Posts</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ( $posts as $post )
                    
                <x-post-card :post="$post" /> 

                @endforeach
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <h1 class="text-5xl text-center mb-4">Products</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ( $products as $product )
                    
                <x-product-card  :product="$product"/> 

                @endforeach
            </div>
        </div>
    </div>
    <div>
        <a href="{{ route('getPage') }}">get page now</a>
    </div>
</x-app-layout>
