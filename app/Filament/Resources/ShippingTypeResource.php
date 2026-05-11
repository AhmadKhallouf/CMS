<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingTypeResource\Pages;
use App\Filament\Resources\ShippingTypeResource\RelationManagers;
use App\Models\ShippingType;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ShippingTypeResource extends Resource
{
    protected static ?string $model = ShippingType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->autofocus()
                ->required()
                ->placeholder(__('Name')),
                TextInput::make('price')->required()->integer(),
                Hidden::make('user_id')->dehydrateStateUsing(fn ($state) => Auth::id())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('price')->label('Price')->formatStateUsing(fn ($state) => money($state)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingTypes::route('/'),
            'create' => Pages\CreateShippingType::route('/create'),
            'edit' => Pages\EditShippingType::route('/{record}/edit'),
        ];
    }
}
