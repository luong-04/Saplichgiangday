<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Giáo viên';
    protected static ?string $modelLabel = 'Giáo viên';

    public static function form(Form $form): Form {
        return $form->schema([
            Forms\Components\Section::make('Thông tin định danh')->schema([
                Forms\Components\TextInput::make('name')->label('Họ tên')->required(),
                Forms\Components\TextInput::make('short_code')->label('Viết tắt'),
                Forms\Components\TextInput::make('lookup_code')->label('Mã tra cứu')->required()->unique(ignoreRecord: true),
            ])->columns(3),
    
            Forms\Components\Section::make('Phân công chuyên môn')->schema([
                Forms\Components\Select::make('subjects')
                    ->label('Môn giảng dạy (Chọn nhiều)')
                    ->multiple() // Quan trọng: Cho phép chọn nhiều môn
                    ->relationship('subjects', 'name')
                    ->preload()->required(),
                Forms\Components\TextInput::make('quota')
                    ->label('Định mức tiết/tuần')->numeric()->default(17)->required(),
                Forms\Components\Select::make('homeroom_class_id')
                    ->label('Lớp chủ nhiệm')
                    ->relationship('homeroomClass', 'name')->preload(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Họ tên Giáo viên')
                ->searchable()
                ->description(fn (Teacher $record): string => $record->short_code ?? ''),
            
            Tables\Columns\TextColumn::make('subjects.name')
                ->label('Môn giảng dạy')
                ->badge()
                ->color('info'),

            Tables\Columns\TextColumn::make('homeroomClass.name')
                ->label('Chủ nhiệm')
                ->placeholder('Không có')
                ->weight('bold'),

            Tables\Columns\TextColumn::make('quota')
                ->label('Định mức')
                ->numeric()
                ->suffix(' tiết/tuần'),

            Tables\Columns\TextColumn::make('remaining_quota')
                ->label('Còn lại')
                ->numeric()
                ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                ->weight('black'),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}