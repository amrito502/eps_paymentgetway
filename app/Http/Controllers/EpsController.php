<?php

namespace App\Http\Controllers;

use App\Services\EpsService;
use Illuminate\Http\Request;

class EpsController extends Controller
{
    protected $epsService;

    public function __construct(EpsService $epsService)
    {
        $this->epsService = $epsService;
    }

    public function showPaymentPage()
    {
        return view('eps.payment');
    }

    public function initiatePayment(Request $request)
    {
        $tokenRes = $this->epsService->getEpsToken();

        if (empty($tokenRes['token'])) {
            return back()->with('error', 'Could not retrieve EPS token.');
        }

        $transactionId = "TXN" . time();
        session(['transactionId' => $transactionId]);

        $amount = 200.50; // Default amount, you can make this dynamic
        $initRes = $this->epsService->initializePayment($tokenRes['token'], $transactionId, $amount);

        if (empty($initRes['RedirectURL'])) {
            return back()->with('error', 'Could not initialize payment.');
        }

        return redirect()->away($initRes['RedirectURL']);
    }

    public function handleCallback(Request $request)
    {
        $action = $request->query('action', '');
        $transactionId = session('transactionId', $request->query('MerchantTransactionId', ''));

        if (!$transactionId) {
            return view('eps.status', [
                'status' => 'error',
                'message' => 'No transaction ID received from session or query parameters.',
                'transactionId' => ''
            ]);
        }

        $status = strtoupper($request->query('Status', ''));

        if (empty($status)) {
            $tokenRes = $this->epsService->getEpsToken();

            if (empty($tokenRes['token'])) {
                return view('eps.status', [
                    'status' => 'error',
                    'message' => 'Could not retrieve EPS token.',
                    'transactionId' => $transactionId
                ]);
            }

            $verify = $this->epsService->verifyTransaction($tokenRes['token'], $transactionId);

            if (isset($verify['transactionStatus'])) {
                $status = strtoupper($verify['transactionStatus']);
            } elseif (isset($verify['status'])) {
                $status = strtoupper($verify['status']);
            } elseif (isset($verify['data']['transactionStatus'])) {
                $status = strtoupper($verify['data']['transactionStatus']);
            } elseif (isset($verify['data']['status'])) {
                $status = strtoupper($verify['data']['status']);
            } else {
                $status = 'UNKNOWN';
            }
        }

        session()->forget('transactionId');

        return view('eps.status', [
            'status' => $status,
            'transactionId' => $transactionId
        ]);
    }
}
