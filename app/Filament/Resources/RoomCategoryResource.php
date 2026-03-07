<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomCategoryResource\Pages;
use App\Filament\Resources\RoomCategoryResource\RelationManagers;
use App\Models\RoomCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomCategoryResource extends Resource
{
    protected static ?string $model = RoomCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $modelLabel = 'Danh mục Phòng';
    protected static ?string $pluralModelLabel = 'Danh mục Phòng';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('name')
            ->label('Tên danh mục')
            ->required()
            ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')
            ->label('Tên danh mục')
            ->searchable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoomCategories::route('/'),
        ];
    }
}
