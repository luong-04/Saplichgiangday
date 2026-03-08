<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixedPeriodResource\Pages;
use App\Models\FixedPeriod;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FixedPeriodResource extends Resource
{
    protected static ?string $model = FixedPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Tiết cố định';
    protected static ?string $modelLabel = 'Tiết cố định';
    protected static ?string $pluralModelLabel = 'Tiết cố định';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        $periodsPerDay = 10;
        $daysStart = 2;
        $daysEnd = 7;
        try {
            $periodsPerDay = Setting::periodsPerDay();
            $daysStart = Setting::daysStart();
            $daysEnd = Setting::daysEnd();
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Setting load failed in FixedPeriodResource: ' . $e->getMessage());
        }

        $dayOptions = [];
        for ($d = $daysStart; $d <= $daysEnd; $d++) {
            $dayOptions[$d] = "Thứ $d";
        }
        $periodOptions = [];
        for ($p = 1; $p <= $periodsPerDay; $p++) {
            $periodOptions[$p] = "Tiết $p";
        }

        return $form->schema([
            Forms\Components\Section::make('Cấu hình tiết cố định')
            ->description('Định nghĩa các tiết tự động gán cho tất cả lớp theo buổi học')
            ->schema([
                Forms\Components\TextInput::make('subject_name')
                ->label('Tên môn / hoạt động')
                ->placeholder('VD: Chào cờ, Sinh hoạt lớp')
                ->required(),
                Forms\Components\Select::make('day')
                ->label('Thứ')
                ->options($dayOptions)
                ->required(),
                Forms\Components\Select::make('period')
                ->label('Tiết')
                ->options($periodOptions)
                ->required(),
                Forms\Components\Select::make('shift')
                ->label('Buổi')
                ->options([
                    'morning' => '🌅 Buổi sáng',
                    'afternoon' => '🌇 Buổi chiều',
                ])
                ->required(),
                Forms\Components\Toggle::make('auto_assign_homeroom')
                ->label('Tự động gán GVCN')
                ->helperText('Bật: hệ thống sẽ tự điền giáo viên chủ nhiệm vào ô này')
                ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('subject_name')
            ->label('Tên')
            ->weight('bold')
            ->searchable(),
            Tables\Columns\TextColumn::make('day')
            ->label('Thứ')
            ->formatStateUsing(fn($state) => "Thứ $state")
            ->badge()->color('info'),
            Tables\Columns\TextColumn::make('period')
            ->label('Tiết')
            ->formatStateUsing(fn($state) => "Tiết $state")
            ->badge()->color('warning'),
            Tables\Columns\TextColumn::make('shift')
            ->label('Buổi')
            ->formatStateUsing(fn($state) => $state === 'morning' ? '🌅 Sáng' : '🌇 Chiều')
            ->badge()
            ->color(fn($state) => $state === 'morning' ? 'success' : 'danger'),
            Tables\Columns\IconColumn::make('auto_assign_homeroom')
            ->label('Tự gán GVCN')
            ->boolean(),
        ])
            ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFixedPeriods::route('/'),
            'create' => Pages\CreateFixedPeriod::route('/create'),
            'edit' => Pages\EditFixedPeriod::route('/{record}/edit'),
        ];
    }
}
