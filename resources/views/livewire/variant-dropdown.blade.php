<div>
    @if ($variations)
        <label for="variations">Please {{ $variations[0]['type']}}</label>
    <select wire:model.live="selectedVariant"
    class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body" 
    name="variation" 
    id="variation">
        <option value="">Select {{ $variations[0]['type']}}</option>
        @foreach ($variations as $variant)
           <option value="{{$variant->id}}">{{$variant->title}}</option>  
        @endforeach
    </select>

    @endif

    @if ($childrenVariation && $childrenVariation->isNotEmpty())
        <livewire:variant-dropdown :variations="$childrenVariation" :key="$selectedVariant"/>
    @endif

</div>
