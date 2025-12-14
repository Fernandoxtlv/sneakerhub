<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $salesData = $this->getSalesData($dateFrom, $dateTo);
        $productData = $this->getProductData($dateFrom, $dateTo);

        return view('admin.reports.index', compact('salesData', 'productData', 'dateFrom', 'dateTo'));
    }

    public function sales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $salesData = $this->getSalesData($dateFrom, $dateTo);

        return view('admin.reports.sales', compact('salesData', 'dateFrom', 'dateTo'));
    }

    public function products(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $productData = $this->getProductData($dateFrom, $dateTo);

        return view('admin.reports.products', compact('productData', 'dateFrom', 'dateTo'));
    }

    public function exportCsv(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $type = $request->get('type', 'sales');

        $filename = "reporte_{$type}_{$dateFrom}_a_{$dateTo}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        if ($type === 'sales') {
            $data = $this->getSalesDataForExport($dateFrom, $dateTo);
        } else {
            $data = $this->getProductDataForExport($dateFrom, $dateTo);
        }

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
            }

            // Data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $salesData = $this->getSalesData($dateFrom, $dateTo);
        $productData = $this->getProductData($dateFrom, $dateTo);

        $pdf = Pdf::loadView('pdf.report', compact('salesData', 'productData', 'dateFrom', 'dateTo'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("reporte_{$dateFrom}_a_{$dateTo}.pdf");
    }

    protected function getSalesData(string $dateFrom, string $dateTo): array
    {
        $orders = Order::where('payment_status', Order::PAYMENT_PAID)
            ->whereBetween('created_at', [$dateFrom, Carbon::parse($dateTo)->endOfDay()])
            ->get();

        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'total_tax' => $orders->sum('tax'),
            'average_ticket' => $orders->count() > 0 ? $orders->sum('total') / $orders->count() : 0,
            'by_payment_method' => $orders->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total'),
                ];
            }),
            'by_day' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total'),
                ];
            }),
        ];
    }

    protected function getProductData(string $dateFrom, string $dateTo): array
    {
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', Order::PAYMENT_PAID)
            ->whereBetween('orders.created_at', [$dateFrom, Carbon::parse($dateTo)->endOfDay()])
            ->select(
                'order_items.name',
                'order_items.sku',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.sku', 'order_items.name')
            ->orderByDesc('total_quantity')
            ->limit(20)
            ->get();

        $lowStock = Product::where('is_active', true)
            ->where('stock', '<=', config('sneakerhub.stock_alert_threshold', 5))
            ->orderBy('stock')
            ->get();

        return [
            'top_products' => $topProducts,
            'low_stock' => $lowStock,
        ];
    }

    protected function getSalesDataForExport(string $dateFrom, string $dateTo): array
    {
        return Order::where('payment_status', Order::PAYMENT_PAID)
            ->whereBetween('created_at', [$dateFrom, Carbon::parse($dateTo)->endOfDay()])
            ->get()
            ->map(function ($order) {
                return [
                    'N° Pedido' => $order->order_number,
                    'Fecha' => $order->created_at->format('d/m/Y H:i'),
                    'Cliente' => $order->customer_name,
                    'Subtotal' => $order->subtotal,
                    'IGV' => $order->tax,
                    'Total' => $order->total,
                    'Método de Pago' => $order->payment_method_label,
                    'Estado' => $order->status_label,
                ];
            })
            ->toArray();
    }

    protected function getProductDataForExport(string $dateFrom, string $dateTo): array
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', Order::PAYMENT_PAID)
            ->whereBetween('orders.created_at', [$dateFrom, Carbon::parse($dateTo)->endOfDay()])
            ->select(
                'order_items.sku',
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.sku', 'order_items.name')
            ->orderByDesc('total_quantity')
            ->get()
            ->map(function ($item) {
                return [
                    'SKU' => $item->sku,
                    'Producto' => $item->name,
                    'Cantidad Vendida' => $item->total_quantity,
                    'Ingresos' => $item->total_revenue,
                ];
            })
            ->toArray();
    }
}
