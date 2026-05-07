<?php

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Layout;

new #[Layout('layouts::front-end-layout')] class extends Component
{
    public string $query = '';
    public bool $showResults = false;
    public int $selectedIndex = -1;

    public function updatedQuery(): void
    {
        $this->showResults = strlen($this->query) >= 2;
        $this->selectedIndex = -1;
    }

    public function products()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return Product::query()
            ->where('name', 'like', '%' . $this->query . '%')
            ->orWhere('description', 'like', '%' . $this->query . '%')
            ->where('is_active', true)
            ->with('primaryImage')
            ->take(6)
            ->get();
    }

    public function selectProduct(int $productId): void
    {
        $product = Product::find($productId);

        if ($product) {
            $this->reset(['query', 'showResults', 'selectedIndex']);
            $this->redirect(route('products.show', $product->slug), navigate: true);
        }
    }

    public function goToSearch(): void
    {
        if (strlen($this->query) >= 2) {
            $this->redirect(route('products.index', ['search' => $this->query]), navigate: true);
        }
    }

    public function incrementIndex(): void
    {
        if ($this->selectedIndex < $this->products()->count() - 1) {
            $this->selectedIndex++;
        }
    }

    public function decrementIndex(): void
    {
        if ($this->selectedIndex > -1) {
            $this->selectedIndex--;
        }
    }

    public function selectCurrent(): void
    {
        $products = $this->products();

        if ($this->selectedIndex === -1) {
            $this->goToSearch();
            return;
        }

        if (isset($products[$this->selectedIndex])) {
            $this->selectProduct($products[$this->selectedIndex]->id);
        }
    }

    public function close(): void
    {
        $this->reset(['showResults', 'selectedIndex']);
    }

};
?>

<div>
    <div class="relative w-full" x-data @click.away="$wire.close()">
        {{-- Search Input --}}
        <div class="relative">
            <input type="text" wire:model.live.debounce.250ms="query" @keydown.escape.window="$wire.close()"
                @keydown.arrow-down.prevent="$wire.incrementIndex()" @keydown.arrow-up.prevent="$wire.decrementIndex()"
                @keydown.enter.prevent="$wire.selectCurrent()" @focus="$wire.showResults = $wire.query.length >= 2"
                placeholder="Search yarn, patterns, accessories..." class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg
                           text-sm text-gray-900 placeholder-gray-400
                           focus:ring-2 focus:ring-orange-500 focus:border-orange-500
                           transition-colors">

            {{-- Search Icon / Spinner --}}
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {{-- Inactive --}}
                <svg wire:loading.remove wire:target="query" class="w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                {{-- Loading spinner --}}
                <svg wire:loading wire:target="query" class="w-5 h-5 text-orange-500 animate-spin" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
            </div>

            {{-- Clear Button --}}
            @if ($query)
                <button wire:click="$set('query', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 transition" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- Results Dropdown --}}
        @if ($showResults && strlen($query) >= 2)
            @php $results = $this->products(); @endphp

            <div class="absolute top-full mt-1.5 w-full bg-white rounded-lg shadow-lg
                               border border-gray-200 z-50 max-h-96 overflow-y-auto">
                @if ($results->isNotEmpty())
                    <ul class="py-2">
                        @foreach ($results as $index => $product)
                            <li>
                                <button wire:click="selectProduct({{ $product->id }})"
                                    @mouseenter="$wire.selectedIndex = {{ $index }}" class="w-full flex items-center gap-3 px-4 py-2.5 text-left
                                                           transition-colors
                                                           {{ $selectedIndex === $index
                            ? 'bg-orange-50'
                            : 'hover:bg-gray-50' }}">
                                    {{-- Thumbnail --}}
                                    <div class="w-10 h-10 rounded-md bg-gray-100 flex-shrink-0 overflow-hidden">
                                        @if ($product->primaryImage)
                                            <img src="{{ $product->primaryImage->url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $product->name }}
                                        </p>
                                        <p class="text-sm font-semibold text-orange-600">
                                            ${{ number_format($product->price, 2) }}
                                        </p>
                                    </div>

                                    {{-- Arrow --}}
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- View All Footer --}}
                    <div class="px-4 py-3 border-t bg-gray-50 rounded-b-lg">
                        <button wire:click="goToSearch"
                            class="w-full text-center text-sm text-orange-600 hover:text-orange-700 font-medium">
                            View all results for "{{ $query }}" →
                        </button>
                    </div>
                @else
                    <div class="px-4 py-10 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-sm text-gray-500">No products found for</p>
                        <p class="text-sm font-medium text-gray-700">"{{ $query }}"</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>