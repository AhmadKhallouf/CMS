<?php

namespace App\Livewire;

use App\Livewire\Forms\AddressCheckoutForm;
use App\Livewire\Forms\CustomerCheckoutForm;
use App\Models\Order;
use App\Models\ShippingType;
use App\Services\CartManager;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Checkout extends Component
{
    public $items;

    public CustomerCheckoutForm $customerForm;

    public AddressCheckoutForm $addressForm;

    public $showAddressForm = false;

    public $shippingType;

    public $address_model;

    public $shippingTypeModel;

    protected $listeners = [
        'cart.updated' => '$refresh',
    ];

    public function getCartProperty()
    {
        return app(CartManager::class);
    }

    public function getAddressesProperty()
    {
        return auth()->user()->addresses ?? null;
    }

    public function getShippingTypesProperty()
    {
        return ShippingType::all();
    }

    public function updatedShippingType()
    {
        $this->shippingTypeModel = ShippingType::find($this->shippingType);
    }

    #[On('shippingType')]
    public function getSelectedShippingTypeProperty()
    {
        return $this->shippingTypeModel;
    }

    public function mount()
    {
        $this->items = $this->cart->getCart()->items;
        $this->shippingType = $this->shippingTypes->first()->id;
        $this->shippingTypeModel = $this->shippingTypes->first();
        $this->customerForm->email = auth()->user()->email ?? null;
        $this->address_model = $this?->addresses?->first()->id ?? null;
        if (! auth()->check()) {
            $this->showAddressForm = true;
        }
    }

    public function getTotalProperty(): int
    {
        return (int) $this->cart->getSubtotal() + (int) $this->shippingTypeModel->price;
    }

    public function addAddress()
    {
        if (auth()->user()->addresses()->create($this->addressForm->toArray())) {
            $this->dispatch('cart.updated');
            $this->showAddressForm = false;

            $this->addressForm->reset();
        }
    }

    public function getSetupIntentProperty()
    {
        return auth()->user()->createSetupIntent();
    }

    public function callValidate()
    {
        $this->validate();
    }

    public function getErrorCount()
    {
        return $this->getErrorBag()->count();
    }

    public function checkout($paymentMethodId)
    {
        $this->customerForm->validate();

        $user = auth()->user();

        if (! $user) {
            Toaster::error('You must be logged in to complete checkout.');

            return;
        }

        $paymentMethod = $this->resolvePaymentMethodId($paymentMethodId);

        try {
            if (! $user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $user->addPaymentMethod($paymentMethod);

            $user->charge($this->total, $paymentMethod, [
                'return_url' => route('checkout').'?success=true',
            ]);

            $cart = $this->cart->getCart();

            $order = $user->orders()->create([
                'total' => $this->total,
                'address_id' => $this->address_model,
                'shipping_type_id' => $this->shippingType,
                'email' => $this->customerForm->email,
            ]);

            foreach ($cart->items as $item) {
                $price = $item->variant ? $item->variant->price : $item->product->price;

                $pivot = [
                    'quantity' => $item->quantity,
                    'price' => $price,
                ];

                if ($item->variant_id) {
                    $pivot['variant_id'] = $item->variant_id;
                }

                $order->products()->attach($item->product_id, $pivot);
            }

            $this->cart->clear();

            Toaster::success('Order placed successfully!');

            $this->redirect(route('home').'?orderId='.$order->order_id);
        } catch (\Exception $e) {
            Toaster::error('Payment failed: '.$e->getMessage());

            throw $e;
        }
    }

    private function resolvePaymentMethodId(mixed $paymentMethodId): string
    {
        if (is_array($paymentMethodId)) {
            return $paymentMethodId['id'] ?? throw new \InvalidArgumentException('Invalid payment method.');
        }

        if (is_object($paymentMethodId)) {
            return $paymentMethodId->id ?? throw new \InvalidArgumentException('Invalid payment method.');
        }

        if (! is_string($paymentMethodId) || $paymentMethodId === '') {
            throw new \InvalidArgumentException('Invalid payment method.');
        }

        return $paymentMethodId;
    }
}
