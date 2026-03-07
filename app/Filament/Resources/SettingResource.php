<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Cấu hình';
    protected static ?string $modelLabel = 'Cấu hình';
    protected static ?string $pluralModelLabel = 'Cấu hình hệ thống';
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin cấu hình')->schema([
                Forms\Components\TextInput::make('key')
                ->label('Khóa cấu hình')
                ->disabled()
                ->dehydrated(),
                Forms\Components\TextInput::make('label')
                ->label('Mô tả')
                ->disabled()
                ->dehydrated(),
                Forms\Components\TextInput::make('value')
                ->label('Giá trị')
                ->required(),
                Forms\Components\TextInput::make('group')
                ->label('Nhóm')
                ->disabled()
                ->dehydrated(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('label')
            ->label('Mô tả')
            ->searchable()
            ->weight('bold'),
            Tables\Columns\TextColumn::make('key')
            ->label('Khóa')
            ->badge()
            ->color('gray'),
            Tables\Columns\TextColumn::make('value')
            ->label('Giá trị')
            ->badge()
            ->color('info')
            ->size('lg'),
            Tables\Columns\TextColumn::make('group')
            ->label('Nhóm')
            ->badge()
            ->color(fn(string $state) => match ($state) {
            'timetable' => 'success',
            'constraint' => 'warning',
            default => 'gray',
        }),
        ])
            ->actions([
            Tables\Actions\EditAction::make()->label('Sửa'),
        ])
            ->defaultSort('group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
