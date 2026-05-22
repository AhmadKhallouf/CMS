<x-app-layout>

 <div class="py-12">
            <div class="max-w-4xl mx-auto sm:bx-6 lg:px-8">
                <div class="dark:shadow-none rounded-lg p-4 bg-white">

                   <h1 class="font-bold text-2xl my-4 text-center text-blue-600">{{ config('app.name') }}</h1>
                   <hr class="mb-2">

                   <div class="flex justify-between mb-6">
                    <h1 class="text-lg font-bold">Invoic</h1>
                    <div class="text-gray-700">
                        <div>Date <time title="{{ $order->created_at }}" datetime="{{$order->created_at}}">{{ $order->created_at->diffForHumans() }}</time> </div>
                        <div>Invoic #: {{ \Illuminate\Support\Str::limit($order->order_id,4) }} </div>
                    </div>
                   </div>

                   <div class="mb-8">
                    <h2 class="text-lg font-bold mb-4">Bill To:</h2>
                    <div class="text-gray-700 mb-2">{{ $order->user->name }}</div>
                    <div class="text-gray-700 mb-2">{{ $order->address->address }}</div>
                    <div class="text-gray-700 mb-2">{{ $order->address->address2 }} , {{ $order->address->post_code }} , {{ $order->address->city }}</div>
                    <div class="text-gray-700">{{ $order->email }}</div>
                   </div>

                   <table class="w-full mb-8">

                    <thead>
                        <tr>
                            <th class="text-left font-bold text-gray-700">Description</th>
                            <th class="text-left font-bold text-gray-700">Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ( $order->products as $product )
                            
                        <tr>
                            <td class="text-left font-bold text-gray-700">{{ $product->title }}</td>
                            <td class="text-left font-bold text-gray-700">{{ money($product->price) }}</td>
                        </tr>
                        @endforeach

                        @if ($order->shippingType)
                            
                         <tr>
                            <td class="text-left font-bold text-gray-700">Shipping method(<strong class="font-bold">{{ $order->shippingType?->name }}</strong>)</td>
                            <td class="text-left font-bold text-gray-700">{{ money($order->shippingType?->price) }}</td>
                        </tr>

                        @else

                         <tr>
                            <td class="text-left font-bold text-gray-700">Shipping Method</td>
                            <td class="text-left font-bold text-gray-700">Standard</td>
                        </tr>

                        @endif

                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-left font-bold text-gray-700">Total</td>
                            <td class="text-left font-bold text-gray-700">{{ money($order->total) }}</td>
                        </tr>
                    </tfoot>
                   </table>

                   <div class="text-gray-700 mb-2">Thanks for your business!</div>
                   <div class="text-gray-700 mb-2">Pleas remit payment within 30 days</div>
















        

                </div>
            </div>
 </div>













</x-app-layout>