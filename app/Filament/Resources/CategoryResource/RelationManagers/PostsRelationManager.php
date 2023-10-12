<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class PostsRelationManager extends RelationManager
{
  protected static string $relationship = 'posts';

  public function form(Form $form): Form
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
            ])
            ->getUploadedFileNameForStorageUsing(
              fn (TemporaryUploadedFile $file): string => (string) str('.' . $file->getClientOriginalExtension())
                ->prepend(auth()->user()->id . '-' . Str::random(10))
            )
            ->moveFiles(),
          Select::make('category_id')
            ->label('Category')
            ->options(Category::all()->pluck('name', 'id'))
            ->searchable()
            ->required(),
          Toggle::make('status')
        ]),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('title')
      ->columns([
        TextColumn::make('title')->limit(50)->sortable(),
        TextColumn::make('category.name')->limit(50)->sortable(),

        // Hanya Gabut
        TextColumn::make('content')->html()->limit(100),

        ImageColumn::make('cover'),
        ToggleColumn::make('status')->sortable(),
      ])
      ->filters([
        //
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }
}
