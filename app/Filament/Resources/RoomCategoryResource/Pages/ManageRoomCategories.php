<?php

namespace App\Filament\Resources\RoomCategoryResource\Pages;

use App\Filament\Resources\RoomCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRoomCategories extends ManageRecords
{
    protected static string $resource = RoomCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
