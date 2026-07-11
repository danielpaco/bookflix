<?php

namespace App\Filament\Resources\Books\Widgets;

use App\Models\User;
use App\Models\Book;
use App\Models\Subscription;
use App\Models\Page;
use App\Models\UserBookProgress;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $totalSeconds = UserBookProgress::sum(
            'reading_time_seconds'
        );

        $totalHours = round(
            $totalSeconds / 3600,
            1
        );

        $activeUsers = UserBookProgress::distinct('user_id')
            ->count('user_id');

        return [
            Stat::make(
                'Libros',
                Book::count()
            )
                ->description('Total catálogo')
                ->color('success'),

            Stat::make(
                'Suscripciones',
                Subscription::count()
            )
                ->description('Históricas')
                ->color('warning'),

            Stat::make(
                'Páginas',
                Page::count()
            )
                ->description('Procesadas')
                ->color('primary'),

            Stat::make(
                'Publicados', 
                Book::where('status', 'ready')->count()
            ),

            Stat::make(
                'Lecturas',
                UserBookProgress::count()
            )
                ->description('Leidos')
                ->color('info'),

            Stat::make(
                'Usuarios',
                User::count()
            )
                ->description('Registrados')
                ->color('info'),

            Stat::make(
                'Horas de lectura',
                $totalHours
            )
                ->description('Tiempo total leído')
                ->icon('heroicon-o-clock'),

            Stat::make(
                'Usuarios activos',
                $activeUsers
            )
                ->icon('heroicon-o-user-group')
                ->description('Han leído al menos un libro'),
        ];
    }
}
