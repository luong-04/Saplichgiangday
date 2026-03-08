<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculumResource\Pages;
use App\Models\Curriculum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurriculumResource extends Resource
{
    protected static ?string $model = Curriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Khung chương trình';
    protected static ?string $modelLabel = 'Khung chương trình';
    protected static ?string $pluralModelLabel = 'Khung chương trình';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Chi tiết định mức tiết học')
            ->description('Cấu hình số tiết chuẩn cho mỗi môn học theo từng khối lớp.')
            ->schema([
                Forms\Components\Select::make('grade')
                ->label('Khối lớp')
                ->options([
                    '10' => 'Khối 10',
                    '11' => 'Khối 11',
                    '12' => 'Khối 12',
                ])
                ->required(),
                Forms\Components\Select::make('subject_id')
                ->label('Môn học')
                ->relationship('subject', 'name')
                ->searchable()
                ->preload()
                ->required(),
                Forms\Components\TextInput::make('lessons_per_week')
                ->label('Số tiết/tuần')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->maxValue(20)
                ->required(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('grade')
            ->label('Khối lớp')
            ->formatStateUsing(fn(string $state): string => "Khối {$state}")
            ->sortable()
            ->searchable()
            ->badge()
            ->color('primary'),
            Tables\Columns\TextColumn::make('subject.name')
            ->label('Môn học')
            ->sortable()
            ->searchable()
            ->weight('bold'),
            Tables\Columns\TextColumn::make('lessons_per_week')
            ->label('Số tiết/tuần')
            ->numeric()
            ->sortable()
            ->badge()
            ->color('success'),
        ])
            ->defaultSort('grade')
            ->filters([
            Tables\Filters\SelectFilter::make('grade')
            ->label('Lọc theo khối')
            ->options([
                '10' => 'Khối 10',
                '11' => 'Khối 11',
                '12' => 'Khối 12',
            ]),
            Tables\Filters\SelectFilter::make('subject_id')
            ->label('Lọc theo môn')
            ->relationship('subject', 'name')
            ->searchable()
            ->preload(),
        ])
            ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
            ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
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
            'index' => Pages\ListCurricula::route('/'),
            'create' => Pages\CreateCurriculum::route('/create'),
            'edit' => Pages\EditCurriculum::route('/{record}/edit'),
        ];
    }
}
