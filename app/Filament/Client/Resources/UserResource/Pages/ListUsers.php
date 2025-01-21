<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Staff List'; // Custom page label
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Create Staff'),
        ];
    }
}
