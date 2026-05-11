<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-m-pencil';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Tabs::make('post')->tabs([
                    Tab::make('Content')->schema([
                        Forms\Components\TextInput::make('title')->required()->minlength(2),
                Forms\Components\TextInput::make('slug')->required()->minlength(2),
                Forms\Components\RichEditor::make('content')->required(),
                Forms\Components\Checkbox::make('is_published')->required(),
                Forms\Components\Checkbox::make('is_featured')->required(),
                Forms\Components\Select::make('categories')
                ->multiple()
                ->relationship('categories','title'),
                Forms\Components\Hidden::make('user_id')->dehydrateStateUsing(fn ($state) => Auth::id()),
                    ]),
                    Tab::make('Meta')->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')->multiple()->image()->optimize('webp')->imageEditor(),
                        Forms\Components\TextInput::make('meta_description')->required(),
                    ])
                ])
            ])->columns(1);

            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\CheckboxColumn::make('is_featured'),
                Tables\Columns\CheckboxColumn::make('is_published'),
                Tables\Columns\TextColumn::make('categories.title')->badge()->searchable(),
                
            ])
            ->filters([
                Tables\Filters\Filter::make('is_featured')
                ->label('Featured')
                ->query(fn (Builder $query): Builder => $query->where('is_featured',true)),
                Tables\Filters\Filter::make('is_published')
                ->label('Published')
                ->query(fn (Builder $query): Builder => $query->where('is_published',true)),
                Tables\Filters\SelectFilter::make('categories')
                ->multiple()
                ->relationship('categories','title'),
                Tables\Filters\TrashedFilter::make('deleted'),
               
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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }    
}
