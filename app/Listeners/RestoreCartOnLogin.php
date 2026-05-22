<?php

namespace App\Listeners;

use App\Services\CartManager;
use Illuminate\Auth\Events\Login;

class RestoreCartOnLogin
{
    public function __construct(
        protected CartManager $cart
    ) {}

    public function handle(Login $event): void
    {
        $this->cart->syncForAuthenticatedUser($event->user);
    }
}
