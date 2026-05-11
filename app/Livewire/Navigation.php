<?php

namespace App\Livewire;

use App\Services\CartManager;
use Livewire\Component;
use App\Models\Navigation as NavigationModel;

class Navigation extends Component
{
    public array $navigationItems = [];
    public array $navigationItemsSidebar = [];

    protected $listeners = [
        'cart.updated' => '$refresh'
    ];

    public function mount()
    {
        // Get ALL active navigation records
        $activeNavigations = NavigationModel::where('is_active', true)->get();
        
        $allItems = [];
        $allSidebarItems = [];
        
        foreach ($activeNavigations as $navigation) {
            // Decode items (handle double-encoding)
            $items = $navigation->items;
            if (is_string($items)) {
                $items = json_decode($items, true);
                if (is_string($items)) {
                    $items = json_decode($items, true);
                }
            }
            
            // Decode sidebar items
            $sidebar = $navigation->items_sidebar;
            if (is_string($sidebar)) {
                $sidebar = json_decode($sidebar, true);
                if (is_string($sidebar)) {
                    $sidebar = json_decode($sidebar, true);
                }
            }
            
            // Merge items if they're arrays
            if (is_array($items)) {
                $allItems = array_merge($allItems, $items);
            }
            
            if (is_array($sidebar)) {
                $allSidebarItems = array_merge($allSidebarItems, $sidebar);
            }
        }
        
        // Filter by authentication status
        $this->navigationItems = $this->filterByAuth($allItems);
        $this->navigationItemsSidebar = $this->filterByAuth($allSidebarItems);
        
        // Debug: See what we have
        // \Log::info('Navigation Items:', ['items' => $this->navigationItems]);
    }
    
    private function filterByAuth(array $items): array
    {
        return array_filter($items, function($item) {
            $showFor = $item['show_for'] ?? 'everyone';
            
            if ($showFor === 'public') {
                return !auth()->check(); // Show only when logged out
            } elseif ($showFor === 'users') {
                return auth()->check(); // Show only when logged in
            } else {
                return true; // 'everyone' - show to everyone
            }
        });
    }

    public function render()
    {
        return view('livewire.navigation', [
            'cart' => app(CartManager::class)
        ]);
    }
}