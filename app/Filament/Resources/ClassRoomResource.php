<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Lớp học';
    protected static ?string $modelLabel = 'Lớp học';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên lớp (VD: 10A1)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('grade')
                    ->label('Khối')
                    ->options([
                        '10' => 'Khối 10',
                        '11' => 'Khối 11',
                        '12' => 'Khối 12',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('lookup_code')
                    ->label('Mã tra cứu của lớp')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Mã dùng để học sinh tra cứu (VD: K10_A1)')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tên lớp')->searchable(),
                Tables\Columns\TextColumn::make('grade')->label('Khối')->searchable(),
                Tables\Columns\TextColumn::make('lookup_code')->label('Mã tra cứu')->badge()->copyable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}