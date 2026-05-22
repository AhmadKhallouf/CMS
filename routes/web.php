<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductShowController;
use App\Http\Controllers\TestController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\OrderShowController;
use App\Http\Controllers\OrdersShowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/cart', CartController::class)->name('cart');
Route::get('/checkout', CheckoutController::class)->name('checkout');
Route::get('/orders', OrdersShowController::class)->name('orders.show');
Route::get('/order/{order:order_id}', OrderShowController::class)->name('order.show');
Route::get('/articles/{post:slug}',PostController::class)->name('post.show');
Route::get('/products/{product:slug}',ProductShowController::class)->name('products.show');
Route::get('/categories/{category:slug}',CategoryController::class)->name('category.show');



Route::middleware(['auth'])->group(function () {
    // Conversations list
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations');
    Route::get('/conversations/search', [ConversationController::class, 'search'])->name('conversations.search');
    
    // Chat routes
    Route::get('/chat/{user}', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/{user}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/{user}/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/{user}/read', [ChatController::class, 'markAsRead']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
});

Route::get('/get-page-test',[TestController::class,'getPage'])->name('getPage');
Route::get('/send-post',[TestController::class,'formTest'])->name('formTest');
Route::get('/send-post',[TestController::class,'formTest'])->name('test-1');



