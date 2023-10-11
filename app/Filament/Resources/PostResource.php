<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class PostResource extends Resource
{
  protected static ?string $model = Post::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('title')
          ->debounce()
          ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
          ->required()
          ->rules(['max:255']),
        TextInput::make('slug')->required()->unique(ignoreRecord: true),

        Card::make()->schema([
          RichEditor::make('content')->disableToolbarButtons([
            'undo',
            'redo',
            // 'attachFiles',
          ])->required(),
          FileUpload::make('cover')
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
              null,
              '16:9',
              '4:3',
              '1:1',
            ]),
          Select::make('category_id')
            ->label('Category')
            ->options(Category::all()->pluck('name', 'id'))
            ->searchable()
            ->required(),
          Toggle::make('status')
        ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('title')->limit(50)->sortable(),
        TextColumn::make('category.name')->limit(50),

        // Hanya Gabut
        TextColumn::make('content')->html(),
        ImageColumn::make('cover'),
        ToggleColumn::make('status'),
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
      'index' => Pages\ListPosts::route('/'),
      'create' => Pages\CreatePost::route('/create'),
      'edit' => Pages\EditPost::route('/{record}/edit'),
    ];
  }
}
