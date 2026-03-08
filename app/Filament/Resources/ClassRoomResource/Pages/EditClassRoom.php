<?php

namespace App\Filament\Resources\ClassRoomResource\Pages;

use App\Filament\Resources\ClassRoomResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditClassRoom extends EditRecord
{
    protected static string $resource = ClassRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->before(function () {
            if ($this->record->schedules()->exists()) {
                Notification::make()->warning()
                    ->title('Không thể xóa')
                    ->body("Lớp {$this->record->name} đang có lịch học. Hãy xóa lịch trước.")
                    ->send();
                $this->halt();
            }
        }),
        ];
    }
}
