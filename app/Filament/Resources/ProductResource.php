<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\VariationsRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Tabs::make('post')->tabs([
                Tab::make('Content')->schema([
                    TextInput::make('title')->required()->minLength(2),
                    TextInput::make('slug')->required()->minLength(2),
                    RichEditor::make('content')->required(),
                    DatePicker::make('published_at')->required(),
                    TextInput::make('price')->numeric(),
                    TextInput::make('SKU'),
                    Hidden::make('user_id')->dehydrateStateUsing(fn ($state) => Auth::id()),
                    Select::make('categories')->multiple()->relationship('categories','title')                   
                ]),
                Tab::make('Meta')->schema([
                    TextInput::make('meta_description'),
                    SpatieMediaLibraryFileUpload::make('images')
                    ->image()
                    ->optimize('webp')
                    ->imageEditor()
                    ->multiple(),
                ])->icon('heroicon-o-tag'),
                // Tab::make('Variants')->schema([
                //     AdjacencyList::make('variants')
                //         ->form([
                //             TextInput::make('label')->required(),
                //             TextInput::make('type')->required(),
                //             TextInput::make('price')->required(),
                //             TextInput::make('SKU')->required(),
                //         ])
                        
                // ])->icon('heroicon-o-rectangle-stack'),
            ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images'),
                TextColumn::make('title')->searchable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('price')->formatStateUsing(fn ($state) => money($state)),
                TextColumn::make('SKU')->label('Sku')->searchable(),
                TextColumn::make('published_at'),
                TextColumn::make('categories.title')->searchable()->badge(),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            VariationsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }    
}
