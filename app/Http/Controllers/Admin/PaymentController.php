<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $query = Payment::with(['order.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderByDesc('created_at')
            ->paginate(20);

        // Calculate stats
        $stats = [
            'total' => Payment::where('status', Payment::STATUS_COMPLETED)->sum('amount'),
            'cash' => Payment::where('status', Payment::STATUS_COMPLETED)->where('payment_method', Payment::METHOD_CASH)->sum('amount'),
            'yape' => Payment::where('status', Payment::STATUS_COMPLETED)->where('payment_method', Payment::METHOD_YAPE)->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function confirm(Payment $payment)
    {
        if ($payment->is_completed) {
            return back()->with('info', 'Este pago ya fue confirmado.');
        }

        if ($payment->payment_method === Payment::METHOD_CASH) {
            $this->paymentService->confirmCashPayment($payment, auth()->id());
        } elseif ($payment->payment_method === Payment::METHOD_YAPE) {
            $this->paymentService->simulateYapeConfirmation($payment);
        }

        return back()->with('success', 'Pago confirmado exitosamente.');
    }
}
