<?php

namespace App\Filament\Resources\RoomCategoryResource\Pages;

use App\Filament\Resources\RoomCategoryResource;
use App\Models\Subject;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRoomCategory extends EditRecord
{
    protected static string $resource = RoomCategoryResource::class;

    protected function afterSave(): void
    {
        // Sync subjects: cập nhật room_category_id trên bảng subjects
        $subjectIds = $this->data['subject_ids'] ?? [];
        // Bỏ gán cũ
        Subject::where('room_category_id', $this->record->id)->update(['room_category_id' => null]);
        // Gán mới
        if (!empty($subjectIds)) {
            Subject::whereIn('id', $subjectIds)->update(['room_category_id' => $this->record->id]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->before(function () {
            if ($this->record->rooms()->exists()) {
                Notification::make()->warning()
                    ->title('Không thể xóa')
                    ->body('Loại phòng này còn phòng đang sử dụng.')
                    ->send();
                $this->halt();
            }
        }),
        ];
    }
}
