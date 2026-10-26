<?php

namespace App\Livewire\Storefront;

use App\Services\Contracts\StorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class HomeCatalog extends Component
{
    #[Url(except: '')]
    public string $search = '';

    protected StorefrontServiceInterface $storefrontService;

    public function boot(StorefrontServiceInterface $storefrontService): void
    {
        $this->storefrontService = $storefrontService;
    }

    public function updatedSearch(): void
    {
        $term = trim($this->search);

        if ($term !== '') {
            $this->redirectRoute('shop.index', ['search' => $term], navigate: true);
        }
    }

    public function render(): View
    {
        $search = trim($this->search);
        $showHomeContent = $search === '';
        $sections = $showHomeContent ? $this->storefrontService->homeSections() : collect();


        return view('livewire.storefront.home-catalog', [
            'categories' => $showHomeContent ? $this->storefrontService->homeCategories() : collect(),
            'sliders' => $showHomeContent ? $this->storefrontService->homeSliders($sections->modelKeys()) : collect(),
            'banners' => $showHomeContent ? $this->storefrontService->homeBanners() : collect(),
            'amazingItems' => $showHomeContent ? $this->storefrontService->amazingItems() : collect(),
            'sections' => $sections,
        ]);
    }
}
