<?php

namespace App\Services\Contract;

use App\Models\Cart;
use App\Models\User;

interface CartManager{

        public function add($productId, $quantity ,$variantId = null);
        public function exists();
        public function associateWithUser();
        public function restoreForUser(User $user): bool;
        public function syncForAuthenticatedUser(User $user): void;
        public function remove();
        public function update();
        public function getCart();
        public function ensureCartExists(): Cart;
        public function getSubtotal();
        public function clear(): void;

}