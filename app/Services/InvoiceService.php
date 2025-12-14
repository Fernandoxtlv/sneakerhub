<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate invoice PDF for order
     */
    public function generate(Order $order, string $type = 'boleta'): Invoice
    {
        // Create invoice record
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'type' => $type,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'total' => $order->total,
            'issued_at' => now(),
        ]);

        // Generate PDF
        $pdfPath = $this->generatePdf($order, $invoice);

        $invoice->pdf_path = $pdfPath;
        $invoice->save();

        return $invoice;
    }

    /**
     * Generate PDF file
     */
    protected function generatePdf(Order $order, Invoice $invoice): string
    {
        $data = $this->prepareInvoiceData($order, $invoice);

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $pdf->setPaper('a4', 'portrait');

        // Create directory if not exists
        $directory = 'invoices/' . date('Y/m');
        Storage::disk('public')->makeDirectory($directory);

        // Save PDF
        $filename = $invoice->invoice_number . '.pdf';
        $path = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Prepare invoice data for PDF
     */
    protected function prepareInvoiceData(Order $order, Invoice $invoice): array
    {
        $storeConfig = config('sneakerhub.store');

        return [
            'invoice' => $invoice,
            'order' => $order,
            'items' => $order->items,
            'store' => [
                'name' => $storeConfig['name'],
                'ruc' => $storeConfig['ruc'],
                'address' => $storeConfig['address'],
                'phone' => $storeConfig['phone'],
                'email' => $storeConfig['email'],
            ],
            'customer' => [
                'name' => $order->shipping_address['name'] ?? $order->user?->name ?? 'Cliente',
                'address' => $order->shipping_address['address'] ?? '',
                'city' => $order->shipping_address['city'] ?? '',
                'phone' => $order->shipping_address['phone'] ?? '',
            ],
            'payment_method' => $order->payment_method_label,
            'currency_symbol' => config('sneakerhub.currency.symbol', 'S/'),
            'tax_name' => config('sneakerhub.tax_name', 'IGV'),
        ];
    }

    /**
     * Get or generate invoice for order
     */
    public function getOrGenerate(Order $order, string $type = 'boleta'): Invoice
    {
        $invoice = $order->invoice;

        if (!$invoice) {
            $invoice = $this->generate($order, $type);
        }

        return $invoice;
    }

    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice)
    {
        if (!$invoice->pdf_path) {
            throw new \Exception('Invoice PDF not found');
        }

        $path = Storage::disk('public')->path($invoice->pdf_path);

        if (!file_exists($path)) {
            // Regenerate PDF
            $invoice = $this->generate($invoice->order, $invoice->type);
            $path = Storage::disk('public')->path($invoice->pdf_path);
        }

        return response()->download($path, $invoice->invoice_number . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Get PDF content
     */
    public function getPdfContent(Invoice $invoice): string
    {
        if (!$invoice->pdf_path) {
            $invoice = $this->generate($invoice->order, $invoice->type);
        }

        return Storage::disk('public')->get($invoice->pdf_path);
    }
    /**
     * Regenerate PDF for an existing invoice
     */
    public function regeneratePdf(Invoice $invoice): void
    {
        $this->generatePdf($invoice->order, $invoice);
    }
}
