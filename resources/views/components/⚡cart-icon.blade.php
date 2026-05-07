<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

new #[Layout('layouts::front-end-layout')] class extends Component {
    public $cartCount = 0;

    public function mount()
    {
        $this->updateCartCount();
    }

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($cart, 'quantity'));
    }
    public function render()
    {
        return $this->view();
    }
};
?>

<div>
    <a href="{{ route('cart.index') }}" class="relative">
        <svg class="w-6 h-6 text-gray-700 hover:text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @if ($cartCount > 0)
            <span
                class="absolute -top-2 -right-2 bg-orange-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                {{ $cartCount }}
            </span>
        @endif
    </a>
</div>