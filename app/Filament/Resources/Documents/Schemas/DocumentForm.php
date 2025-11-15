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
                    ->label('District')
                    ->datalist(
                        Document::query()
                            ->distinct()
                            ->pluck('district')
                            ->toArray()
                    )
                    ->required(),

                TextInput::make('anchal')
                ->label('Anchal')
                 ->datalist(
                        Document::query()
                            ->distinct()
                            ->pluck('anchal')
                            ->toArray()
                    )
                    ->required(),

                TextInput::make('mauza')
                ->label('Mauza')
                 ->datalist(
                        Document::query()
                            ->distinct()
                            ->pluck('mauza')
                            ->toArray()
                    )
                    ->required(),

                TextInput::make('thana_no')
                ->label('Thana No')
                 ->datalist(
                        Document::query()
                            ->distinct()
                            ->pluck('thana_no')
                            ->toArray()
                    )
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
