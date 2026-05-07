<?php

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::front-end-layout')] class extends Component {
    public function render()
    {
        return $this->view([
            'categories' => Category::active()->sorted()->limit(5)->get(),
        ]);
    }
};
?>

<ul class="flex items-center gap-8">
    @foreach($categories as $category)
        <li>
            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                class="text-gray-700 hover:text-orange-600">
                {{ $category->name }}
            </a>
        </li>
    @endforeach
</ul>