<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Statistics extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = '60s';
    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->
            sum('total');
        $todaysRevenue = Order::where('payment_status', 'paid')->
            whereDate('created_at', today())->sum('total');

        $totalOrders = Order::count();
        $todaysOrders = Order::where('payment_status', 'pending')->count();

        $totalCustomers = Customer::count();
        $monthlyCustomers = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        $lowStockProducts = Product::lowStock()->count();
        $outOfStockProducts = Product::where('stock_status',  'out_of_stock')->count();

        return [
            Stat::make('Total Revenue', '$ ' . number_format($totalRevenue, 2))
                ->description('Today $' . number_format($todaysRevenue, 2))
                ->descriptionIcon('phosphor-trend-up')
                ->color('success'),
            Stat::make('Total Orders', $totalOrders)
                ->description("$todaysOrders Pending")
                ->descriptionIcon('phosphor-package')
                ->color('warning')
                ->url(route('filament.admin.resources.orders.index')),
            Stat::make('Total Customers', $totalCustomers)
                ->description("$monthlyCustomers registered this month")
                ->descriptionIcon('phosphor-users-three')
                ->color('info')
                ->url(route('filament.admin.resources.customers.index')),
            Stat::make('Low on Stock', $lowStockProducts)
                ->description("$outOfStockProducts out of stock")
                ->descriptionIcon('phosphor-warning')
                ->color('danger')
                ->url(route('filament.admin.resources.products.index')),
        ];
    }
}
