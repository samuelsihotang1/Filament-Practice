<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;

class CategoryResource extends Resource
{
  protected static ?string $model = Category::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name')
          ->debounce()
          ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
          ->required()
          ->rules(['max:255']),
        TextInput::make('slug')->required()->unique(ignoreRecord: true),

        // // Pakai Group
        // Group::make()->schema([
        //   TextInput::make('name')
        //     ->debounce()
        //     ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
        //     ->required()
        //     ->rules(['max:255']),
        //   TextInput::make('slug')->required()->unique(ignoreRecord: true)
        // ]),

        // // Pakai card
        // Card::make()->schema([
        //   TextInput::make('name')
        //     ->debounce()
        //     ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
        //     ->required()
        //     ->rules(['max:255']),
        //   TextInput::make('slug')->required()->unique(ignoreRecord: true)
        // ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        // Gaada gunanya Nomor Index
        TextColumn::make('Nomor Index')->state(
          static function (HasTable $livewire, $rowLoop): string {
            return (string) ($rowLoop->iteration +
              ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1
              ))
            );
          }
        ),
        TextColumn::make('id')->sortable(),
        TextColumn::make('name')->limit(50)->sortable(),
        TextColumn::make('slug')->limit(50)
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
      'index' => Pages\ListCategories::route('/'),
      'create' => Pages\CreateCategory::route('/create'),
      'edit' => Pages\EditCategory::route('/{record}/edit'),
    ];
  }
}
