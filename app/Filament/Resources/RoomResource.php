<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Phòng chức năng';
    protected static ?string $modelLabel = 'Phòng chức năng';
    protected static ?string $pluralModelLabel = 'Phòng chức năng';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin phòng')->schema([
                Forms\Components\TextInput::make('name')
                ->label('Tên phòng')
                ->placeholder('VD: Phòng máy 1, Lab Hóa, Nhà đa năng...')
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('capacity')
                ->label('Sức chứa (số lớp cùng lúc)')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Môn học được phép sử dụng')
            ->description('Chọn các môn thực hành được phép dùng phòng này')
            ->schema([
                Forms\Components\Select::make('subjects')
                ->label('Môn học')
                ->multiple()
                ->relationship('subjects', 'name')
                ->preload(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')
            ->label('Tên phòng')
            ->searchable()
            ->weight('bold'),
            Tables\Columns\TextColumn::make('capacity')
            ->label('Sức chứa')
            ->suffix(' lớp')
            ->alignCenter(),
            Tables\Columns\TextColumn::make('subjects.name')
            ->label('Môn học')
            ->badge()
            ->color('info'),
        ])
            ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
