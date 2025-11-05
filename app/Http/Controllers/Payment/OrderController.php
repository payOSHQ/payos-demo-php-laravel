<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CancelOrderRequest;
use App\Http\Requests\Payment\CreateOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use PayOS\Models\V2\PaymentRequests\InvoiceRequest;
use PayOS\Models\V2\PaymentRequests\PaymentLinkItem;
use PayOS\Models\V2\PaymentRequests\TaxPercentage;
use PayOS\PayOS;

class OrderController extends Controller
{
    private PayOS $client;

    public function __construct()
    {
        $this->client = new PayOS(
            clientId: env('PAYOS_CLIENT_ID'),
            apiKey: env('PAYOS_API_KEY'),
            checksumKey: env('PAYOS_CHECKSUM_KEY'),
            logger: Log::channel(),
        );
    }

    public function create(CreateOrderRequest $request)
    {
        $data = $request->validated();
        $data['orderCode'] = time();

        try {
            $response = $this->client->paymentRequests->create($data);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $response
            ]);
        } catch (\Throwable $th) {
            Log::error("Create order failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Create order failed',
                'data' => null,
            ], 500);
        }
    }

    public function get(string|int $id)
    {
        try {
            $paymentLink = $this->client->paymentRequests->get($id);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $paymentLink
            ]);
        } catch (\Throwable $th) {
            Log::error("Get order failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Get order failed',
                'data' => null,
            ], 500);
        }
    }

    public function cancel(string|int $id, CancelOrderRequest $request)
    {
        $cancellationReason = $request->validated()['cancellationReason'] ?: null;
        try {
            $paymentLink = $this->client->paymentRequests->cancel($id, $cancellationReason);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $paymentLink
            ]);
        } catch (\Throwable $th) {
            Log::error("Cancel order failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Cancel order failed',
                'data' => null
            ], 500);
        }
    }

    public function getInvoices(string|int $id)
    {
        try {
            $invoices = $this->client->paymentRequests->invoices->get($id);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $invoices,
            ]);
        } catch (\Throwable $th) {
            Log::error("Get invoices failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Get invoices failed',
                'data' => null
            ], 500);
        }
    }

    public function downloadInvoice(string|int $id, string $invoiceId)
    {
        try {
            $invoice = $this->client->paymentRequests->invoices->download($invoiceId, $id);
            return response($invoice->data, 200, [
                'content-type' => $invoice->contentType,
                'content-disposition' => "attachment; filename=\"{$invoice->filename}\"",
                'content-length' => $invoice->size,
            ]);
        } catch (\Throwable $th) {
            Log::error("Download invoice failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Download invoice failed',
                'data' => null
            ], 500);
        }
    }
}
