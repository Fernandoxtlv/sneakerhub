<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // KPIs
        $kpis = [
            'sales_today' => Order::whereDate('created_at', $today)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->count(),
            'revenue_today' => Order::whereDate('created_at', $today)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total'),
            'sales_month' => Order::where('created_at', '>=', $startOfMonth)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->count(),
            'revenue_month' => Order::where('created_at', '>=', $startOfMonth)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total'),
            'revenue_year' => Order::where('created_at', '>=', $startOfYear)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total'),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'low_stock_products' => Product::where('stock', '<=', config('sneakerhub.stock_alert_threshold', 5))
                ->where('stock', '>', 0)
                ->where('is_active', true)
                ->count(),
            'out_of_stock_products' => Product::where('stock', '<=', 0)
                ->where('is_active', true)
                ->count(),
        ];

        // Average ticket
        $paidOrdersCount = Order::where('payment_status', Order::PAYMENT_PAID)->count();
        $kpis['average_ticket'] = $paidOrdersCount > 0
            ? Order::where('payment_status', Order::PAYMENT_PAID)->sum('total') / $paidOrdersCount
            : 0;

        // Sales by payment method
        $salesByPaymentMethod = Order::where('payment_status', Order::PAYMENT_PAID)
            ->where('created_at', '>=', $startOfMonth)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method')
            ->toArray();

        // Last 30 days sales chart data
        $last30Days = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            $sales = Order::whereDate('created_at', $date)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total');
            return [
                'date' => $date->format('d/m'),
                'sales' => (float) $sales,
            ];
        });

        // Top 10 products
        $topProducts = Product::select('products.*')
            ->withCount([
                'orderItems as total_sold' => function ($query) use ($startOfMonth) {
                    $query->whereHas('order', function ($q) use ($startOfMonth) {
                        $q->where('payment_status', Order::PAYMENT_PAID)
                            ->where('created_at', '>=', $startOfMonth);
                    })->select(DB::raw('SUM(quantity)'));
                }
            ])
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['user', 'items'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::with(['brand', 'category'])
            ->where('stock', '<=', config('sneakerhub.stock_alert_threshold', 5))
            ->where('is_active', true)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'kpis',
            'salesByPaymentMethod',
            'last30Days',
            'topProducts',
            'recentOrders',
            'lowStockProducts'
        ));
    }
}
