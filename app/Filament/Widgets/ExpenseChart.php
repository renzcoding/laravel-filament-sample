<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Expense';
    protected static string $color = 'danger';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ? Carbon::parse($this->filters['startDate']) : null;
        $endDate = ! is_null($this->filters['endDate'] ?? null) ? Carbon::parse($this->filters['endDate']) : null;



        $data = Trend::query(Transaction::expenses())
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Expense Per Day',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
