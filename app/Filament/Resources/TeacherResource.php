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
            Forms\Components\Section::make('Thông tin đ?nh danh')->schema([
                Forms\Components\TextInput::make('name')->label('Họ tên')->required(),
                Forms\Components\TextInput::make('short_code')->label('Viết tắt'),
                Forms\Components\TextInput::make('lookup_code')->label('Mã tra cứu')->required()->unique(ignoreRecord: true),
            ])->columns(3),
    
            Forms\Components\Section::make('Phân công chuyên môn')->schema([
                Forms\Components\Select::make('subjects')
                    ->label('Môn giảng dạy (Ch?n nhi?u)')
                    ->multiple() 
                    ->relationship('subjects', 'name')
                    ->preload()->required(),
                Forms\Components\TextInput::make('quota')
                    ->label('Định mức ti?t/tu?n')->numeric()->default(17)->required(),
                Forms\Components\Select::make('homeroom_class_id')
                    ->label('Lớp Chủ nhiệm')
                    ->relationship('homeroomClass', 'name')->preload(),
                Forms\Components\TextInput::make('max_periods_per_day')
                    ->label('Tối đa tiết/ngày')->numeric()->default(5)->required(),
                Forms\Components\CheckboxList::make('teaching_shifts')
                    ->label('Ca dạy')
                    ->options([
                        'morning' => 'Sáng',
                        'afternoon' => 'Chiều'
                    ])
                    ->columns(2),
            ])->columns(3),

            Forms\Components\Section::make('Phân công chi tiết (Tùy ch?n)')->schema([
                Forms\Components\Repeater::make('assignments')
                    ->relationship()
                    ->label('Danh sách phân công Lớp & môn')
                    ->schema([
                        Forms\Components\Select::make('class_id')
                            ->label('Lớp')
                            ->relationship('classRoom', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('subject_id')
                            ->label('Môn học')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2)
                    ->addActionLabel('Thêm phân công')
                    ->rule(function (\Filament\Forms\Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $quota = (int) $get('quota');
                            $totalPeriods = 0;
                            
                            if (!is_array($value)) return;
                            
                            foreach ($value as $item) {
                                if (empty($item['class_id']) || empty($item['subject_id'])) continue;
                                
                                $class = \App\Models\ClassRoom::find($item['class_id']);
                                $subject = \App\Models\Subject::find($item['subject_id']);
                                
                                if ($class && $subject) {
                                    $curriculum = \App\Models\Curriculum::where('subject_id', $subject->id)
                                        ->where('grade', $class->grade)
                                        ->first();
                                    $totalPeriods += $curriculum ? $curriculum->lessons_per_week : $subject->lessons_per_week;
                                }
                            }
                            
                            if ($totalPeriods > $quota) {
                                $fail("Tổng số tiết phân công (" . $totalPeriods . ") đã vượt quá Định mức (" . $quota . " ti?t).");
                            }
                        };
                    }),
            ]),
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
                ->suffix(' ti?t/tu?n'),

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

