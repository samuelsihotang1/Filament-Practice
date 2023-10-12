<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class TagsRelationManager extends RelationManager
{
  protected static string $relationship = 'tags';

  public function form(Form $form): Form
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

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('name')
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
      ->headerActions([
        Tables\Actions\CreateAction::make(),
        Tables\Actions\AttachAction::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DetachAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DetachBulkAction::make(),
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }
}
