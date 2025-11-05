<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\OrderWebhookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PayOS\PayOS;

class WebhookController extends Controller
{
    private PayOS $client;

    public function __construct()
    {
        $this->client = new PayOS(
            clientId: env('PAYOS_CLIENT_ID'),
            apiKey: env('PAYOS_API_KEY'),
            checksumKey: env('PAYOS_CHECKSUM_KEY'),
            logger: Log::channel()
        );
    }

    public function verify(OrderWebhookRequest $request)
    {
        $data = $request->validated();

        try {
            $result = $this->client->webhooks->verify($data);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            Log::error("Verify webhook failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => "Verify webhook failed: {$th->getMessage()}",
                'data' => null,
            ], 500);
        }
    }
}
