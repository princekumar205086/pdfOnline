<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use recentdocs;

class LatestDocs extends TableWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Document::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('document_type')
                    ->searchable(),
                    TextColumn::make('district')
                        ->searchable(),
                    TextColumn::make('anchal')
                        ->searchable(),
                    TextColumn::make('mauza')
                        ->searchable(),
                    TextColumn::make('thana_no')
                        ->searchable(),
                    TextColumn::make('file_path')
                        ->searchable(),
                    TextColumn::make('price')
                        ->money('INR', true)
                        ->sortable(),
                    ToggleColumn::make('is_active'),
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
            ])->paginated([5])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
