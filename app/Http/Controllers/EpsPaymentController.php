<?php

// app/Http/Controllers/EpsPaymentController.php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EpsTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class EpsPaymentController extends Controller
{
    private $storeId = 'd44e705f-9e3a-41de-98b1-1674631637da';
    private $merchantId = '29e86e70-0ac6-45eb-ba04-9fcb0aaed12a';
    private $username = 'Epsdemo@gmail.com';
    private $password = 'Epsdemo258@';
    private $hashKey = 'FHZxyzeps56789gfhg678ygu876o=';

    public function initiatePayment()
    {
        $invoice = 'EPS-' . time();
        $amount = 100.00;

        $transaction = EpsTransaction::create([
            'invoice_id' => $invoice,
            'amount' => $amount,
        ]);

        $payload = [
            "merchantId" => $this->merchantId,
            "storeId" => $this->storeId,
            "amount" => $amount,
            "currency" => "BDT",
            "orderId" => $invoice,
            "successUrl" => route('eps.success'),
            "failUrl" => route('eps.fail'),
            "cancelUrl" => route('eps.fail'),
            "clientIp" => request()->ip(),
            "requestType" => "CREATE_INVOICE",
            "username" => $this->username,
            "password" => $this->password,
        ];

        $xHash = hash("sha256", json_encode($payload) . $this->hashKey);

      try {
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "x-hash"       => $xHash
        ])->post("https://sandbox.eps.com.bd/eps-transaction-api/api/initiate", $payload);

        // যদি EPS API সফলভাবে রেসপন্স দেয়
        if ($response->successful()) {
            $res = $response->json();

            // এখানে আপনি চান তো DB তে লগ রাখতে পারেন
            return response()->json([
                'status' => 'success',
                'data'   => $res
            ]);
        } else {
            // EPS API রেসপন্সে কোনো সমস্যা
            return response()->json([
                'status' => 'error',
                'message' => 'EPS API responded with error.',
                'response_code' => $response->status(),
                'response_body' => $response->body(),
            ]);
        }

    } catch (ConnectionException $e) {
        // EPS সার্ভার এ সংযোগ স্থাপন করতে ব্যর্থ
        return response()->json([
            'status' => 'error',
            'message' => 'EPS Sandbox server is unreachable.',
            'error' => $e->getMessage(),
        ]);
    }


        if (isset($res['invoiceUrl'])) {
            return redirect($res['invoiceUrl']);
        } else {
            return response()->json(['error' => 'Payment failed to initialize.']);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $trx = EpsTransaction::where('invoice_id', $request->orderId)->first();
        $trx->update([
            'status' => 'success',
            'response' => $request->all(),
        ]);

        return view('eps.success', compact('trx'));
    }

    public function paymentFail(Request $request)
    {
        $trx = EpsTransaction::where('invoice_id', $request->orderId)->first();
        $trx->update([
            'status' => 'failed',
            'response' => $request->all(),
        ]);

        return view('eps.fail', compact('trx'));
    }
}
