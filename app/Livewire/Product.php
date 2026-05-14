<?php

namespace App\Livewire;

use App\Models\ProdcutVariation;
use Livewire\Component;
use App\Models\Product as ProductModel;
use App\Services\CartManager;
use Livewire\Attributes\On;

class Product extends Component
{
    public ProductModel $product; 
    
    public $cart;

    public $finalVariantId;
    #[On('finalVariantSelected')]
    public function handelFinalVariant($id)
    {
        $this->finalVariantId = $id;
    }


   public function addToCart(): void
    {
        $cart = app(CartManager::class);

        $productVariation = ProdcutVariation::findOrFail($this->finalVariantId) ?? null;

       // dd($this->finalVariantId);
        $cart->add($productVariation->product->id, $productVariation->id ?? null);

        $this->dispatch('cart.updated');

      //  Toaster::success('Product ' . $productVariation->product->title . ' added to cart');

    }
    
    public function render()
    {
        return view('livewire.product');
    }
}
