<?php

namespace App\Services\Contract;



interface CartManager
{

        public function add($productId, $quantity ,$variantId = null);
        public function exists();
        public function associateWithUser();
        public function remove();
        public function update();
        public function getCart();
        public function getSubtotal();
      

}