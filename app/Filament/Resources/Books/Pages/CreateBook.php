<?php

namespace App\Filament\Resources\Books\Pages;

use App\Jobs\ProcessPdfJob;
use App\Filament\Resources\Books\BookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function afterCreate(): void
    {
        ProcessPdfJob::dispatch(
            $this->record->id,
            $this->record->pdf_path
        );
    }
}
