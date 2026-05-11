<?php

namespace App\Livewire;

use App\Models\ProdcutVariation;
use Illuminate\Support\Collection;
use Livewire\Component;

class VariantDropdown extends Component
{
    public Collection $variations;

    public $selectedVariant;

    public $childrenVariation;

    public function updatedSelectedVariant($value)
    {
        $variant = ProdcutVariation::find($value);
       $this->childrenVariation = $variant->children;

       if($variant->children->isEmpty()){
            $this->dispatch('finalVariantSelected',$variant->id);
       }
       
    }
    
    public function render()
    {
        return view('livewire.variant-dropdown');
    }
}
