<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSubject extends EditRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->before(function () {
            if ($this->record->schedules()->exists()) {
                Notification::make()->warning()
                    ->title('Không thể xóa')
                    ->body("Môn {$this->record->name} đang có lịch học. Hãy xóa lịch trước.")
                    ->send();
                $this->halt();
            }
        }),
        ];
    }
}
