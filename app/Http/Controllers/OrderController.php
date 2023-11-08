<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SignatureUtils;

class OrderController extends Controller
{
    //
    public function __construct() { }

    public function createOrder(Request $request) {
        $body = json_decode($request->getContent(), true);
        if (!($body["amount"] && $body["description"] && $body["returnUrl"] && $body["cancelUrl"])){
            return response()->json([
                "error" => -1,
                "message" => "Invalid Parameter",
                "data" => null
            ]);
        }

        $body["orderCode"] = intval(substr(strval(microtime(true) * 10000), -6));

        $PAYOS_CLIENT_ID = env('PAYOS_CLIENT_ID');
        $PAYOS_API_KEY = env('PAYOS_API_KEY');
        $PAYOS_CHECKSUM_KEY = env('PAYOS_CHECKSUM_KEY');

        $paymentRequestSignature = SignatureUtils::createSignaturePaymentRequest($PAYOS_CHECKSUM_KEY, $body);
        $body["signature"] = $paymentRequestSignature;
        
        try {
            $response = Http::withHeaders([
                "x-client-id" => $PAYOS_CLIENT_ID,
                "x-api-key" => $PAYOS_API_KEY
            ])->post("https://api-merchant.payos.vn/v2/payment-requests", $body)->json();

            // Check response data integrity
            $responseDataSignature = SignatureUtils::createSignatureFromObj($PAYOS_CHECKSUM_KEY, $response["data"]);
            if ($responseDataSignature != $response["signature"]) {
                return response()->json([
                    "error" => -1,
                    "message" => "Signature not match",
                    "data" => null
                ]);
            }
            return response()->json([
                "error" => 0,
                "message" => "Ok",
                "data" => [
                    "checkoutUrl" => $response["data"]["checkoutUrl"]
                ]
            ]);

            // Alternative way to make http request
            // $paymentRequest = curl_init();
            // curl_setopt($paymentRequest, CURLOPT_URL, "https://api-merchant.payos.vn/v2/payment-requests");
            // curl_setopt($paymentRequest, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($paymentRequest, CURLOPT_HTTPHEADER, array(
            //         "Content-Type: application/json",
            //         "x-client-id: {$PAYOS_CLIENT_ID}",
            //         "x-api-key: {$PAYOS_API_KEY}"
            //     )
            // );
            
            // curl_setopt($paymentRequest, CURLOPT_POST, 1);
            // curl_setopt($paymentRequest, CURLOPT_POSTFIELDS, json_encode($body));
            // $response = curl_exec($paymentRequest);

            // #Ensure to close curl
            // curl_close($paymentRequest);
            // $response = json_decode($response, true);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                "error" => -1,
                "message" => "Error",
                "data" => null
            ]);
        }
    }
}