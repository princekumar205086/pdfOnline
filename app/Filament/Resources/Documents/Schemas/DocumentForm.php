<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Document;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),

                // ✅ Works like a hybrid input+dropdown
                TextInput::make('document_type')
                    ->label('Document Type')
                    ->datalist(
                        Document::query()
                            ->distinct()
                            ->pluck('document_type')
                            ->toArray()
                    )
                    ->required(),

                TextInput::make('district')
                    ->required(),

                TextInput::make('anchal')
                    ->required(),

                TextInput::make('mauza')
                    ->required(),

                TextInput::make('thana_no')
                    ->required(),

                FileUpload::make('file_path')
                    ->label('Upload PDF')
                    ->disk('private')
                    ->directory('documents')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(512000) 
                    ->enableOpen() 
                    ->enableDownload()
                    ->visibility('private')
                    ->required(),


                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₹'),

                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
