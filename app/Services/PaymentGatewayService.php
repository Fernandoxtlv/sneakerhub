<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Process payment based on method
     */
    public function processPayment(Order $order): Payment
    {
        $payment = Payment::createForOrder($order);

        return match ($order->payment_method) {
            Order::PAYMENT_CASH => $this->processCashPayment($payment),
            Order::PAYMENT_YAPE => $this->processYapePayment($payment),
            default => throw new \InvalidArgumentException("Método de pago no soportado: {$order->payment_method}"),
        };
    }

    /**
     * Process cash payment
     */
    protected function processCashPayment(Payment $payment): Payment
    {
        // Cash payments are pending until confirmed at pickup/delivery
        $payment->status = Payment::STATUS_PENDING;
        $payment->notes = 'Pago en efectivo - pendiente de cobro';
        $payment->save();

        Log::info("Pago en efectivo creado", [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'amount' => $payment->amount,
        ]);

        return $payment;
    }

    /**
     * Process Yape payment
     */
    protected function processYapePayment(Payment $payment): Payment
    {
        // Generate Yape reference and wait for webhook confirmation
        $payment->yape_reference = Payment::generateYapeReference();
        $payment->yape_phone = config('sneakerhub.yape_phone', env('YAPE_PHONE_NUMBER'));
        $payment->status = Payment::STATUS_PENDING;
        $payment->metadata = [
            'yape_merchant_id' => env('YAPE_MERCHANT_ID'),
            'created_at' => now()->toIso8601String(),
            'expires_at' => now()->addHours(24)->toIso8601String(),
        ];
        $payment->notes = 'Esperando confirmación de pago Yape';
        $payment->save();

        Log::info("Pago Yape creado", [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'yape_reference' => $payment->yape_reference,
            'amount' => $payment->amount,
        ]);

        return $payment;
    }

    /**
     * Confirm cash payment (called by staff)
     */
    public function confirmCashPayment(Payment $payment, ?int $staffId = null): bool
    {
        if ($payment->payment_method !== Payment::METHOD_CASH) {
            return false;
        }

        $payment->markAsCompleted('CASH-' . time());

        Log::info("Pago en efectivo confirmado", [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'staff_id' => $staffId,
        ]);

        return true;
    }

    /**
     * Handle Yape webhook notification
     * 
     * This simulates receiving a webhook from Yape
     */
    public function handleYapeWebhook(array $data): bool
    {
        // Validate webhook signature (in production)
        if (!$this->validateYapeWebhookSignature($data)) {
            Log::warning("Yape webhook signature inválida", $data);
            return false;
        }

        $yapeReference = $data['yape_reference'] ?? null;
        $transactionId = $data['transaction_id'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount'] ?? null;

        if (!$yapeReference || !$transactionId) {
            Log::warning("Yape webhook con datos incompletos", $data);
            return false;
        }

        $payment = Payment::where('yape_reference', $yapeReference)->first();

        if (!$payment) {
            Log::warning("Pago Yape no encontrado para referencia", ['reference' => $yapeReference]);
            return false;
        }

        // Validate amount matches
        if ($amount && abs($payment->amount - $amount) > 0.01) {
            Log::warning("Monto de pago Yape no coincide", [
                'expected' => $payment->amount,
                'received' => $amount,
            ]);
            return false;
        }

        if ($status === 'completed' || $status === 'success') {
            $payment->markAsCompleted($transactionId);
            $payment->metadata = array_merge($payment->metadata ?? [], [
                'webhook_received_at' => now()->toIso8601String(),
                'yape_transaction_id' => $transactionId,
            ]);
            $payment->save();

            Log::info("Pago Yape confirmado via webhook", [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'transaction_id' => $transactionId,
            ]);

            return true;
        }

        if ($status === 'failed' || $status === 'cancelled') {
            $payment->markAsFailed($data['failure_reason'] ?? 'Pago cancelado');

            Log::info("Pago Yape fallido via webhook", [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'reason' => $data['failure_reason'] ?? 'unknown',
            ]);

            return true;
        }

        return false;
    }

    /**
     * Validate Yape webhook signature
     * 
     * In production, this would validate the signature from Yape
     */
    protected function validateYapeWebhookSignature(array $data): bool
    {
        // In test mode, accept all webhooks
        if (env('YAPE_TEST_MODE', true)) {
            return true;
        }

        $expectedSignature = $data['signature'] ?? null;
        $webhookSecret = env('YAPE_WEBHOOK_SECRET');

        if (!$expectedSignature || !$webhookSecret) {
            return false;
        }

        // Create signature from webhook data
        $payload = json_encode([
            'yape_reference' => $data['yape_reference'] ?? '',
            'transaction_id' => $data['transaction_id'] ?? '',
            'amount' => $data['amount'] ?? '',
            'status' => $data['status'] ?? '',
        ]);

        $calculatedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($calculatedSignature, $expectedSignature);
    }

    /**
     * Get Yape QR data for display
     */
    public function getYapeQRData(Payment $payment): array
    {
        return [
            'reference' => $payment->yape_reference,
            'amount' => $payment->amount,
            'phone' => $payment->yape_phone ?? env('YAPE_PHONE_NUMBER'),
            'merchant_name' => env('STORE_NAME', 'SneakerHub'),
            'expires_at' => $payment->metadata['expires_at'] ?? now()->addHours(24)->toIso8601String(),
            'instructions' => [
                '1. Abre tu app de Yape',
                '2. Escanea el código QR o ingresa el número',
                '3. Ingresa el monto exacto: S/ ' . number_format($payment->amount, 2),
                '4. Usa la referencia: ' . $payment->yape_reference,
                '5. Confirma el pago',
            ],
        ];
    }

    /**
     * Check if payment has expired
     */
    public function isPaymentExpired(Payment $payment): bool
    {
        if ($payment->is_completed) {
            return false;
        }

        $expiresAt = $payment->metadata['expires_at'] ?? null;

        if ($expiresAt) {
            return now()->isAfter($expiresAt);
        }

        // Default expiry: 24 hours after creation
        return $payment->created_at->addHours(24)->isPast();
    }

    /**
     * Simulate Yape payment confirmation (for testing)
     */
    public function simulateYapeConfirmation(Payment $payment): bool
    {
        if (!env('YAPE_TEST_MODE', true)) {
            return false;
        }

        return $this->handleYapeWebhook([
            'yape_reference' => $payment->yape_reference,
            'transaction_id' => 'YAPE-TEST-' . time(),
            'amount' => $payment->amount,
            'status' => 'completed',
            'signature' => 'test_signature',
        ]);
    }
}
