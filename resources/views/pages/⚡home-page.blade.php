<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\Attributes\Layout;

new #[Layout('layouts::front-end-layout')] class extends Component {
    public function render()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->inStock()
            ->with(['category', 'brand', 'primaryImage'])
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->sorted()
            ->withCount('products')
            ->limit(6)
            ->get();
        $newArrivals = Product::active()
            ->inStock()
            ->with(['category', 'brand', 'primaryImage'])
            ->latest()
            ->limit(8)
            ->get();

        return $this->view([
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newArrivals' => $newArrivals
        ]);
    }
};
?>

<div>
    <div>
        <!-- Hero Section -->
        <section class="bg-amber-600 text-white py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">
                        Welcome to {{ config('app.name') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-yellow-100">
                        We will satisfy all your knitting needs
                    </p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-white text-blue-950 px-8 py-3 rounded-lg hover:invert font-semibold transition">
                        Shop Now
                    </a>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-16 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Shop by Category</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group">
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 mb-3">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-red-700">
                                        <span class="text-4xl text-white">{{ substr($category->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <h3 class="text-center font-medium text-2xl text-gray-900 group-hover:text-orange-600">
                                {{ $category->name }}
                            </h3>
                            <p class="text-center text-4x1 text-gray-500">{{ $category->products_count }} items</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-16 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Featured Products</h2>
                    <a href="{{ route('products.index', ['featured' => 1]) }}"
                        class="text-blue-600 hover:text-indigo-700 font-medium">
                        View All →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <livewire:product-card :product="$product" :key="$product->id" />
                    @endforeach
                </div>
            </div>
        </section>

        <!-- New Arrivals -->
        <section class="py-16 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">New Arrivals</h2>
                    <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                        class="text-blue-600 hover:text-indigo-700 font-medium">
                        View All →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($newArrivals as $product)
                        <livewire:product-card :product="$product" :key="'new-' . $product->id" />
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-16 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 text-orange-600 rounded-full mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">High Quality</h3>
                        <p class="text-gray-600">All products are tested for quality</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 text-orange-600 rounded-full mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Fast Shipping</h3>
                        <p class="text-gray-600">Quick delivery right to your home</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 text-orange-600 rounded-full mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Secure Payment</h3>
                        <p class="text-gray-600">Your payment information is safe</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>