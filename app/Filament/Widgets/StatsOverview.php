<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\Purchase;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';
    // public $purchase = Purchase::all();
    protected function getStats(): array
    {
        // $totalTransactions = Transaction::count();
        // $completedTransactions = Transaction::where('status', 'completed')->count();
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        return [
            Stat::make('Total Purchases', Purchase::count()),
            Stat::make('Total Documents', Document::count()),
            Stat::make('Total Earning', '₹' . number_format($totalRevenue, 2)),
        ];
    }
}
