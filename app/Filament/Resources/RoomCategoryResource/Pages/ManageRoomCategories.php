<?php

namespace App\Filament\Resources\RoomCategoryResource\Pages;

use App\Filament\Resources\RoomCategoryResource;
use App\Models\Subject;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRoomCategories extends ManageRecords
{
    protected static string $resource = RoomCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->after(function ($record, array $data) {
            // Sync subjects: cập nhật room_category_id trên bảng subjects
            $subjectIds = $data['subject_ids'] ?? [];
            // Bỏ gán cũ
            Subject::where('room_category_id', $record->id)->update(['room_category_id' => null]);
            // Gán mới
            if (!empty($subjectIds)) {
                Subject::whereIn('id', $subjectIds)->update(['room_category_id' => $record->id]);
            }
        }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Loại subject_ids ra khỏi data trước khi save vào RoomCategory
        unset($data['subject_ids']);
        return $data;
    }
}
