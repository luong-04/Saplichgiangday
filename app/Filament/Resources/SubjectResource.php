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
            Forms\Components\Section::make('Thông tin môn học')->schema([
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
            ])->columns(2),

            Forms\Components\Section::make('Cấu hình xếp lịch')
            ->description('Thiết lập ràng buộc số tiết khi xếp thời khóa biểu')
            ->schema([
                Forms\Components\TextInput::make('lessons_per_week')
                ->label('Số tiết / tuần')
                ->helperText('Số tiết quy định cho môn này mỗi tuần')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->maxValue(20)
                ->required(),
                Forms\Components\TextInput::make('max_lessons_per_day')
                ->label('Tối đa tiết / ngày')
                ->helperText('Số tiết tối đa môn này trong 1 ngày cho 1 lớp')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->maxValue(5)
                ->required(),
                Forms\Components\Select::make('consecutive_periods')
                ->label('Số tiết liên tiếp')
                ->helperText('1: Bình thường, 2-4: Tiết dài (Thực hành/Thể dục)')
                ->options([
                    1 => '1 tiết',
                    2 => '2 tiết',
                    3 => '3 tiết',
                    4 => '4 tiết',
                ])
                ->default(1)
                ->required(),
                Forms\Components\Select::make('preferred_room_category')
                ->label('Loại phòng ưu tiên')
                ->options([
                    'Tin học' => 'Tin học',
                    'Lab Lý' => 'Lab Lý',
                    'Lab Hóa' => 'Lab Hóa',
                    'Nhà đa năng' => 'Nhà đa năng',
                ])
                ->nullable(),
            ])->columns(2),
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
            Tables\Columns\TextColumn::make('lessons_per_week')
            ->label('Tiết/tuần')
            ->numeric()
            ->alignCenter()
            ->badge()
            ->color('info'),
            Tables\Columns\TextColumn::make('max_lessons_per_day')
            ->label('Max/ngày')
            ->numeric()
            ->alignCenter(),
            Tables\Columns\TextColumn::make('consecutive_periods')
            ->label('Tiết liền')
            ->numeric()
            ->alignCenter(),
            Tables\Columns\TextColumn::make('preferred_room_category')
            ->label('Phòng ưu tiên')
            ->searchable(),
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