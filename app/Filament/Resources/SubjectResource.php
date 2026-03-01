<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Môn học';
    protected static ?string $modelLabel = 'Môn học';
    protected static ?string $pluralModelLabel = 'Môn học';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('name')
            ->label('Tên môn học')
            ->required()
            ->maxLength(255),
            Forms\Components\Select::make('type')
            ->label('Loại môn học')
            ->options([
                '0' => 'Văn hóa',
                '1' => 'Thể dục',
                '2' => 'Thực hành',
            ])
            ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')->label('Tên môn')->searchable(),
            Tables\Columns\TextColumn::make('type')
            ->label('Loại môn')
            ->formatStateUsing(fn(string $state): string => match ($state) {
            '0' => 'Văn hóa',
            '1' => 'Thể dục',
            '2' => 'Thực hành',
            default => 'Khác',
        })
            ->badge()
            ->colors([
                'primary' => '0',
                'success' => '1',
                'warning' => '2',
            ]),
        ])
            ->actions([
            Tables\Actions\EditAction::make()->label('Sửa'),
            Tables\Actions\DeleteAction::make()->label('Xóa'),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}