<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreateBatchTransferRequest;
use App\Http\Requests\Payment\CreateTransferRequest;
use App\Http\Requests\Payment\GetListTransferParamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PayOS\PayOS;

class TransferController extends Controller
{
    private PayOS $client;

    public function __construct()
    {
        $this->client = new PayOS(
            clientId: env('PAYOS_PAYOUT_CLIENT_ID'),
            apiKey: env('PAYOS_PAYOUT_API_KEY'),
            checksumKey: env('PAYOS_PAYOUT_CHECKSUM_KEY'),
            logger: Log::channel()
        );
    }

    public function create(CreateTransferRequest $request)
    {
        $data = $request->validated();
        $data['referenceId'] = uuid_create();
        try {
            $payout = $this->client->payouts->create($data);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $payout
            ]);
        } catch (\Throwable $th) {
            Log::error("Create payout failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Create payout failed',
                'data' => null
            ], 500);
        }
    }

    public function createBatch(CreateBatchTransferRequest $request)
    {
        $data = $request->validated();
        $data['referenceId'] = uuid_create();
        foreach ($data['payouts'] as $index => $payoutItem) {
            $payoutItem['referenceId'] = "{$data['referenceId']}_{$index}";
            $data['payouts'][$index] = $payoutItem;
        }
        try {
            $payout = $this->client->payouts->batch->create($data);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $payout,
            ]);
        } catch (\Throwable $th) {
            Log::error("Create batch payout failed: {$th->getMessage()}");
            return response()->json([
                'error' => 1,
                'message' => 'Create batch failed',
                'data' => null,
            ], 500);
        }
    }

    public function get(string $id)
    {
        try {
            $payout = $this->client->payouts->get($id);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $payout,
            ]);
        } catch (\Throwable $th) {
            Log::error("Get payout failed: {$th->getMessage()}");
            return response()->json([
                'error' => 0,
                'message' => 'Get payout failed',
                'data' => null,
            ]);
        }
    }

    public function list(GetListTransferParamRequest $request)
    {
        $data = $request->validated();
        $data['limit'] = isset($data['limit']) ? $data['limit'] : 50;
        try {
            $payouts = $this->client->payouts->list($data)->toArray();
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $payouts,
            ]);
        } catch (\Throwable $th) {
            Log::error("Get list payout failed: {$th->getMessage()}");
            return response()->json([
                'error' => 0,
                'message' => 'Get list payout failed',
                'data' => null,
            ]);
        }
    }

    public function getBalance()
    {
        try {
            $accountInfo = $this->client->payoutsAccount->balance();
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $accountInfo,
            ]);
        } catch (\Throwable $th) {
            Log::error("Get payout account balance failed: {$th->getMessage()}");
            return response()->json([
                'error' => 0,
                'message' => 'Get payout account balance failed',
                'data' => null,
            ]);
        }
    }

    public function estimateCredit(CreateBatchTransferRequest $request)
    {
        $data = $request->validated();
        $data['referenceId'] = uuid_create();
        foreach ($data['payouts'] as $index => $payoutItem) {
            $payoutItem['referenceId'] = "{$data['referenceId']}_{$index}";
            $data['payouts'][$index] = $payoutItem;
        }
        try {
            $result = $this->client->payouts->estimateCredit($data);
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            Log::error("Estimate credit failed: {$th->getMessage()}");
            return response()->json([
                'error' => 0,
                'message' => 'Estimate credit failed',
                'data' => null,
            ]);
        }
    }
}
