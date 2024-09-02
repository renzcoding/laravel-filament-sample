<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ? Carbon::parse($this->filters['startDate']) : null;
        $endDate = ! is_null($this->filters['endDate'] ?? null) ? Carbon::parse($this->filters['endDate']) : null;

        $income = Transaction::incomes()->get()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        // $income = Transaction::incomes()->get()->sum('amount');
        // $income = Transaction::join('categories', 'categories.id', '=', 'transactions.category_id')
        //     ->where('is_expense', false)
        //     ->sum('transactions.amount');
        $expense = Transaction::expenses()->get()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');

        return [
            Stat::make('Total Income', $income)
                ->color('success'),
            Stat::make('Total Expenses', $expense),
            Stat::make('Remaining', $income - $expense),
        ];
    }
}
