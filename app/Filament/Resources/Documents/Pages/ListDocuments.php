<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Document;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Document::query()
            ->leftJoin('document_files', 'document_files.document_id', '=', 'documents.id')
            ->select([
                'documents.*',
                'document_files.file_path as additional_file_path',
                'document_files.title as additional_title',
                'document_files.price as additional_price',
            ]);
    }
}
