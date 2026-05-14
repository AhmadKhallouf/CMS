<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product as ProductModel;
use Illuminate\Support\Collection;

class VariantSelector extends Component
{
    public ProductModel $product;

    public Collection $variations; 

    public function mount()
    {
        $this->variations = $this->product->variations->sortBy('order')->groupBy('type')->first();

    }

    public function render()
    {
        return view('livewire.variant-selector');
    }
}
