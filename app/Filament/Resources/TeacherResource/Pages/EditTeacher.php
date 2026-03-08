<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->before(function () {
            if ($this->record->schedules()->exists()) {
                Notification::make()->warning()
                    ->title('Không thể xóa')
                    ->body("GV {$this->record->name} đang có lịch dạy. Hãy xóa lịch trước.")
                    ->send();
                $this->halt();
            }
        }),
        ];
    }
}
