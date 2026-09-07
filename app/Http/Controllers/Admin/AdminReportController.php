<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CustomCakeOrder;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminReportController extends Controller
{
    private const STATUSES = [
        'pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled',
    ];

    public function index(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $branchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;
        $status = in_array($request->input('status', 'delivered'), array_merge(['all'], self::STATUSES), true)
            ? $request->input('status', 'delivered')
            : 'delivered';

        return Inertia::render('Admin/Reports/Index', [
            ...$this->analytics($from, $to, $branchId, $status),
            'branches' => Branch::orderBy('sort_order')->get(['id', 'name']),
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'branch_id' => $branchId,
                'status' => $status,
            ],
            'statuses' => self::STATUSES,
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $branchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;
        $status = in_array($request->input('status', 'delivered'), array_merge(['all'], self::STATUSES), true)
            ? $request->input('status', 'delivered')
            : 'delivered';
        $rows = $this->salesRows($from, $to, $branchId, $status);
        $filename = 'lakeview-sales-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv';

        return response()->streamDownload(function () use ($rows) {
            echo "\xEF\xBB\xBF";
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Type', 'Order number', 'Date', 'Branch', 'Customer', 'Status', 'Delivery type', 'Subtotal', 'Delivery charge', 'Discount', 'Total']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['type'], $row['order_number'], $row['date'], $row['branch'], $row['customer'],
                    $row['status'], $row['delivery_type'], $row['subtotal'], $row['delivery_charge'],
                    $row['discount'], $row['total'],
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function analytics(Carbon $from, Carbon $to, ?int $branchId = null, string $status = 'delivered'): array
    {
        $rows = $this->salesRows($from, $to, $branchId, $status);
        $sales = collect($rows);
        $regular = $sales->where('type', 'Order');
        $cakes = $sales->where('type', 'Custom cake');
        $dayCursor = $from->copy()->startOfDay();
        $salesByDay = collect();

        while ($dayCursor->lte($to)) {
            $date = $dayCursor->toDateString();
            $dayRows = $sales->where('date', $date);
            $salesByDay->push([
                'date' => $date,
                'label' => $dayCursor->format('d M'),
                'sales' => round((float) $dayRows->sum('total'), 2),
                'orders' => $dayRows->count(),
            ]);
            $dayCursor->addDay();
        }

        $branchSales = $sales->groupBy('branch_id')->map(function ($branchRows) {
            return [
                'branch' => $branchRows->first()['branch'],
                'orders' => $branchRows->count(),
                'sales' => round((float) $branchRows->sum('total'), 2),
            ];
        })->sortByDesc('sales')->values();

        $productQuery = OrderItem::query()
            ->whereHas('order', function (Builder $query) use ($from, $to, $branchId, $status) {
                $this->applyOrderFilters($query, $from, $to, $branchId, $status);
            })
            ->selectRaw('product_name, SUM(quantity) as units, SUM(total) as sales')
            ->groupBy('product_name')
            ->orderByDesc('units')
            ->limit(10)
            ->get();

        return [
            'summary' => [
                'sales' => round((float) $sales->sum('total'), 2),
                'orders' => $sales->count(),
                'regular_orders' => $regular->count(),
                'regular_sales' => round((float) $regular->sum('total'), 2),
                'cake_orders' => $cakes->count(),
                'cake_sales' => round((float) $cakes->sum('total'), 2),
                'discounts' => round((float) $regular->sum('discount'), 2),
                'average_order' => $sales->count() ? round((float) $sales->sum('total') / $sales->count(), 2) : 0,
            ],
            'salesByDay' => $salesByDay,
            'branchSales' => $branchSales,
            'topProducts' => $productQuery->map(fn ($item) => [
                'name' => $item->product_name,
                'units' => (int) $item->units,
                'sales' => round((float) $item->sales, 2),
            ])->values(),
            'recentSales' => $sales->sortByDesc('created_at')->take(25)->values(),
        ];
    }

    private function salesRows(Carbon $from, Carbon $to, ?int $branchId, string $status)
    {
        $orderQuery = Order::query()->with('branch:id,name');
        $this->applyOrderFilters($orderQuery, $from, $to, $branchId, $status);
        $orders = $orderQuery->latest()->get();

        $cakeQuery = CustomCakeOrder::query()->with('branch:id,name');
        $this->applyCakeFilters($cakeQuery, $from, $to, $branchId, $status);
        $cakes = $cakeQuery->latest()->get();

        return $orders->map(fn (Order $order) => [
            'type' => 'Order',
            'order_number' => $order->order_number,
            'date' => $order->created_at->toDateString(),
            'created_at' => $order->created_at->toISOString(),
            'branch_id' => $order->branch_id,
            'branch' => $order->branch?->name ?: 'Main branch',
            'customer' => $order->customer_name,
            'status' => $order->status,
            'delivery_type' => $order->delivery_type,
            'subtotal' => round((float) $order->subtotal, 2),
            'delivery_charge' => round((float) $order->delivery_charge, 2),
            'discount' => round((float) $order->discount, 2),
            'total' => round((float) $order->total, 2),
        ])->concat($cakes->map(fn (CustomCakeOrder $order) => [
            'type' => 'Custom cake',
            'order_number' => $order->order_number,
            'date' => $order->created_at->toDateString(),
            'created_at' => $order->created_at->toISOString(),
            'branch_id' => $order->branch_id,
            'branch' => $order->branch?->name ?: 'Main branch',
            'customer' => $order->customer_name,
            'status' => $order->status,
            'delivery_type' => $order->delivery_type,
            'subtotal' => round((float) $order->estimated_price, 2),
            'delivery_charge' => round((float) $order->delivery_charge, 2),
            'discount' => 0,
            'total' => round((float) $order->total, 2),
        ]))->sortByDesc('created_at')->values();
    }

    private function applyOrderFilters(Builder $query, Carbon $from, Carbon $to, ?int $branchId, string $status): void
    {
        $query->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status === 'all') {
            $query->where('status', '!=', 'cancelled');
        } elseif ($status) {
            $query->where('status', $status);
        }
    }

    private function applyCakeFilters(Builder $query, Carbon $from, Carbon $to, ?int $branchId, string $status): void
    {
        $query->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status === 'all') {
            $query->where('status', '!=', 'cancelled');
        } elseif ($status && in_array($status, ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'], true)) {
            $query->where('status', $status);
        } elseif ($status === 'out_for_delivery') {
            $query->whereRaw('1 = 0');
        }
    }

    private function dateRange(Request $request): array
    {
        try {
            $from = Carbon::parse($request->input('from', now()->subDays(29)->toDateString()))->startOfDay();
            $to = Carbon::parse($request->input('to', now()->toDateString()))->startOfDay();
        } catch (\Throwable) {
            $from = now()->subDays(29)->startOfDay();
            $to = now()->startOfDay();
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
