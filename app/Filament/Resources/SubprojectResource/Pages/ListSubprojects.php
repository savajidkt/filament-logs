<?php

namespace App\Filament\Resources\SubprojectResource\Pages;

use App\Filament\Resources\SubprojectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubprojects extends ListRecords
{
    protected static string $resource = SubprojectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
