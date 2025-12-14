<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle Yape payment webhook
     * 
     * This endpoint receives notifications from Yape when a payment is completed.
     * In production, make sure to verify the webhook signature.
     */
    public function yape(Request $request)
    {
        Log::info('Yape webhook received', $request->all());

        $data = $request->all();

        // Validate required fields
        if (empty($data['yape_reference']) || empty($data['transaction_id']) || empty($data['status'])) {
            Log::warning('Yape webhook missing required fields', $data);
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        $result = $this->paymentService->handleYapeWebhook($data);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Payment processed']);
        }

        return response()->json(['error' => 'Payment processing failed'], 400);
    }
}
