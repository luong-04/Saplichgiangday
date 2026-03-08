<?php

namespace App\Filament\Resources\RoomCategoryResource\Pages;

use App\Filament\Resources\RoomCategoryResource;
use App\Models\Subject;
use Filament\Resources\Pages\CreateRecord;

class CreateRoomCategory extends CreateRecord
{
    protected static string $resource = RoomCategoryResource::class;

    protected function afterCreate(): void
    {
        // Sync subjects: cập nhật room_category_id trên bảng subjects
        $subjectIds = $this->data['subject_ids'] ?? [];
        if (!empty($subjectIds)) {
            Subject::whereIn('id', $subjectIds)->update(['room_category_id' => $this->record->id]);
        }
    }
}
