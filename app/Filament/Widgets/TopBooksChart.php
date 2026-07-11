<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;

class TopBooksChart extends ChartWidget
{
    protected ?string $heading = 'Libros más leídos';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $books = Book::withCount('progress')
            ->orderByDesc('progress_count')
            ->take(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Lecturas',
                    'data' => $books->pluck('progress_count'),
                ],
            ],
            'labels' => $books->pluck('title'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
