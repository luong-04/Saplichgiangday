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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Họ và tên Giáo viên')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('short_code')
                    ->label('Tên viết tắt (VD: NVA)')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('lookup_code')
                    ->label('Mã tra cứu (Lookup Code)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Mã dùng để giáo viên tra cứu lịch dạy. (VD: GV_A)')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('short_code')
                    ->label('Viết tắt')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('lookup_code')
                    ->label('Mã tra cứu')
                    ->searchable()
                    ->copyable()
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}