<?php

namespace App\Services;

use Illuminate\Support\Facades\Http; 
class EpsService
{
    protected $username;
    protected $password;
    protected $hashKey;
    protected $merchantId;
    protected $storeId;
    protected $isSandbox;

    public function __construct()
    {
        $this->username = env('EPS_USERNAME');
        $this->password = env('EPS_PASSWORD');
        $this->hashKey = env('EPS_HASH_KEY');
        $this->merchantId = env('EPS_MERCHANT_ID');
        $this->storeId = env('EPS_STORE_ID');
        $this->isSandbox = env('EPS_ENV', 'sandbox') === 'sandbox';
    }

    protected function generateHash($data, $secretKey)
    {
        return base64_encode(hash_hmac('sha512', utf8_encode($data), $secretKey, true));
    }

    public function getEpsToken()
    {
        $url = $this->isSandbox
            ? "https://sandboxpgapi.eps.com.bd/v1/Auth/GetToken"
            : "https://pgapi.eps.com.bd/v1/Auth/GetToken";

        $xHash = $this->generateHash($this->username, $this->hashKey);

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "x-hash" => $xHash
        ])->post($url, [
            "userName" => $this->username,
            "password" => $this->password
        ]);

        return $response->json();
    }

    public function initializePayment($token, $transactionId, $amount, $customerData = [])
    {
        $url = $this->isSandbox
            ? "https://sandboxpgapi.eps.com.bd/v1/EPSEngine/InitializeEPS"
            : "https://pgapi.eps.com.bd/v1/EPSEngine/InitializeEPS";

        $xHash = $this->generateHash($transactionId, $this->hashKey);

        $defaultCustomerData = [
            "customerName" => "John Doe",
            "customerEmail" => "john@example.com",
            "customerAddress" => "Uttara, Dhaka",
            "customerCity" => "Dhaka",
            "customerState" => "Dhaka",
            "customerPostcode" => "1230",
            "customerCountry" => "BD",
            "customerPhone" => "01700000000",
            "productName" => "Test Product",
            "productProfile" => "general",
            "productCategory" => "Demo"
        ];

        $paymentData = array_merge([
            "merchantId" => $this->merchantId,
            "storeId" => $this->storeId,
            "CustomerOrderId" => "Order" . rand(1000, 9999),
            "merchantTransactionId" => $transactionId,
            "transactionTypeId" => 1,
            "totalAmount" => $amount,
            "successUrl" => route('eps.callback', ['action' => 'status']),
            "failUrl" => route('eps.callback', ['action' => 'status']),
            "cancelUrl" => route('eps.callback', ['action' => 'status']),
        ], array_merge($defaultCustomerData, $customerData));

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "x-hash" => $xHash,
            "Authorization" => "Bearer $token"
        ])->post($url, $paymentData);

        return $response->json();
    }

    public function verifyTransaction($token, $transactionId)
    {
        $url = $this->isSandbox
            ? "https://sandboxpgapi.eps.com.bd/v1/EPSEngine/CheckMerchantTransactionStatus?merchantTransactionId=$transactionId"
            : "https://pgapi.eps.com.bd/v1/EPSEngine/CheckMerchantTransactionStatus?merchantTransactionId=$transactionId";

        $xHash = $this->generateHash($transactionId, $this->hashKey);

        $response = Http::withHeaders([
            "x-hash" => $xHash,
            "Authorization" => "Bearer $token"
        ])->get($url);

        return $response->json();
    }
}
