<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function afterSave(): void
    {
        // Clear cache cho setting vừa sửa
        Cache::forget("setting_{$this->record->key}");
        Cache::forget("settings_group_{$this->record->group}");
    }
}
