<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Closure;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Xếp lịch';
    protected static ?string $modelLabel = 'Lịch dạy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->label('Giáo viên')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('class_id')
                    ->relationship('classRoom', 'name')
                    ->label('Lớp học')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Môn học')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('day')
                    ->label('Thứ')
                    ->options([
                        '2' => 'Thứ 2', '3' => 'Thứ 3', '4' => 'Thứ 4',
                        '5' => 'Thứ 5', '6' => 'Thứ 6', '7' => 'Thứ 7',
                    ])
                    ->required(),

                Forms\Components\Select::make('period')
                    ->label('Tiết học')
                    ->options([
                        // Buổi sáng
                        1 => 'Tiết 1 (Sáng)', 
                        2 => 'Tiết 2 (Sáng)', 
                        3 => 'Tiết 3 (Sáng)',
                        4 => 'Tiết 4 (Sáng)', 
                        5 => 'Tiết 5 (Sáng)',
                        // Buổi chiều
                        6 => 'Tiết 6 (Chiều)', 
                        7 => 'Tiết 7 (Chiều)', 
                        8 => 'Tiết 8 (Chiều)', 
                        9 => 'Tiết 9 (Chiều)', 
                        10 => 'Tiết 10 (Chiều)',
                    ])
                    ->required()
                    // GỌI THUẬT TOÁN CHECK CONFLICT TẠI ĐÂY
                    ->rules([
                        function (Forms\Get $get) {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                $service = new ScheduleService();
                                // Gọi hàm kiểm tra
                                $conflict = $service->checkConflict(
                                    $get('teacher_id'),
                                    $get('class_id'),
                                    $get('day'),
                                    $value, // value hiện tại là period (tiết)
                                    $get('id') // Bỏ qua id hiện tại nếu đang edit
                                );

                                if ($conflict) {
                                    $fail($conflict); // Hiển thị lỗi màu đỏ và chặn lưu
                                }
                            };
                        },
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')->label('Giáo viên')->searchable(),
                Tables\Columns\TextColumn::make('classRoom.name')->label('Lớp')->searchable(),
                Tables\Columns\TextColumn::make('subject.name')->label('Môn'),
                Tables\Columns\TextColumn::make('day')->label('Thứ')->prefix('Thứ '),
                Tables\Columns\TextColumn::make('period')->label('Tiết')->prefix('Tiết '),
            ])
            ->defaultSort('day', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}