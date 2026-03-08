<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomCategoryResource\Pages;
use App\Models\RoomCategory;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomCategoryResource extends Resource
{
    protected static ?string $model = RoomCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Phòng chức năng';
    protected static ?string $modelLabel = 'Danh mục & Phòng chức năng';
    protected static ?string $pluralModelLabel = 'Phòng chức năng';

    protected static bool $shouldRegisterNavigation = true;
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Thông tin loại phòng')
            ->description('Tạo danh mục phòng chức năng và gán các môn học cần sử dụng loại phòng này.')
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Tên loại phòng (VD: Thực hành Lý)')
                ->required()
                ->maxLength(255),
                Forms\Components\CheckboxList::make('subject_ids')
                ->label('Gán môn học vào loại phòng này')
                ->options(fn() => Subject::pluck('name', 'id'))
                ->columns(2)
                ->afterStateHydrated(function (Forms\Components\CheckboxList $component, ?RoomCategory $record) {
            if ($record) {
                $component->state($record->subjects()->pluck('id')->toArray());
            }
        }),
            ])->columns(1),

            Forms\Components\Section::make('Danh sách phòng thuộc loại này')
            ->description('Tạo trực tiếp các phòng cụ thể (VD: Phòng Lý 1, Phòng Lý 2) thuộc danh mục này.')
            ->schema([
                Forms\Components\Repeater::make('rooms')
                ->relationship()
                ->label('Các phòng cụ thể')
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label('Tên phòng (VD: Phòng lí 1)')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('capacity')
                    ->label('Sức chứa (lớp)')
                    ->numeric()
                    ->default(1)
                    ->required(),
                    Forms\Components\Toggle::make('status')
                    ->label('Hoạt động')
                    ->default(true),
                ])
                ->columns(3)
                ->addActionLabel('Thêm phòng mới'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')
            ->label('Loại phòng')
            ->searchable()
            ->weight('bold'),
            Tables\Columns\TextColumn::make('subjects.name')
            ->label('Môn học yêu cầu')
            ->badge()
            ->color('success'),
            Tables\Columns\TextColumn::make('rooms_count')
            ->counts('rooms')
            ->label('Số lượng phòng')
            ->badge()
            ->color('info'),
        ])
            ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
            ->hidden(fn(RoomCategory $record) => $record->rooms()->exists() || $record->subjects()->exists()),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomCategories::route('/'),
            'create' => Pages\CreateRoomCategory::route('/create'),
            'edit' => Pages\EditRoomCategory::route('/{record}/edit'),
        ];
    }
}
