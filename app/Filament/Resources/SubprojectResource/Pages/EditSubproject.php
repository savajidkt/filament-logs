<?php

namespace App\Filament\Resources\SubprojectResource\Pages;

use App\Filament\Resources\SubprojectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubproject extends EditRecord
{
    protected static string $resource = SubprojectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
