<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;

class TagResource extends Resource
{
  protected static ?string $model = Tag::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        // Pakai card
        Card::make()->schema([
          TextInput::make('name')
            ->debounce()
            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
            ->required()
            ->rules(['max:255']),
          TextInput::make('slug')->required()->unique(ignoreRecord: true)
        ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        // Gaada gunanya Nomor Index
        TextColumn::make('No')->state(
          static function (HasTable $livewire, $rowLoop): string {
            return (string) ($rowLoop->iteration +
              ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1
              ))
            );
          }
        ),
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
      'index' => Pages\ListTags::route('/'),
      'create' => Pages\CreateTag::route('/create'),
      'edit' => Pages\EditTag::route('/{record}/edit'),
    ];
  }
}
