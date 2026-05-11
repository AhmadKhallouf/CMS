<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

     public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Orders'),
            'pending' => Tab::make('Published')->modifyQueryUsing(function ($query) {
                return $query->where('status', OrderStatus::PENDING);
            }),
            'procissing' => Tab::make('Procissing')->modifyQueryUsing(function ($query){
                return $query->where('status', OrderStatus::PROCISSING);
            }),
             'shipped' => Tab::make('Shipped')->modifyQueryUsing(function ($query){
                return $query->where('status', OrderStatus::SHIPPED);
            }),
            'delivered' => Tab::make('Delivered')->modifyQueryUsing(function ($query){
                return $query->where('status', OrderStatus::DELIVERED);
            }),
            'cancelled' => Tab::make('Cancelled')->modifyQueryUsing(function ($query){
                return $query->where('status', OrderStatus::CANCELLED);
            })
        ];
    }
}
