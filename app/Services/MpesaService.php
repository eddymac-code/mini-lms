<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MpesaService
{
    private string $baseUrl;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout(30);
    }

    public function accessToken(): string
    {
        return Cache::remember(
            'mpesa_access_token',
            now()->addMinutes(50),
            function () {
                $response = Http::withBasicAuth(
                    config('services.mpesa.consumer_key'),
                    config('services.mpesa.consumer_secret')
                )
                    ->acceptJson()
                    ->timeout(30)
                    ->get($this->baseUrl . '/oauth/v1/generate', [
                        'grant_type' => 'client_credentials',
                    ]);

                if ($response->failed()) {
                    throw new RuntimeException(
                        'M-Pesa authentication failed: ' . $response->body()
                    );
                }

                $token = $response->json('access_token');

                if (! $token) {
                    throw new RuntimeException(
                        'M-Pesa did not return an access token.'
                    );
                }

                return $token;
            }
        );
    }

    public function stkPush(
        string $phone,
        float $amount,
        string $reference,
        string $description = 'Loan repayment'
    ): array {
        $timestamp = now()->format('YmdHis');

        $password = base64_encode(
            config('services.mpesa.shortcode')
            . config('services.mpesa.passkey')
            . $timestamp
        );

        $response = $this->client()
            ->withToken($this->accessToken())
            ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => config('services.mpesa.shortcode'),
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => config(
                    'services.mpesa.transaction_type',
                    'CustomerPayBillOnline'
                ),
                'Amount' => (int) ceil($amount),
                'PartyA' => $phone,
                'PartyB' => config('services.mpesa.shortcode'),
                'PhoneNumber' => $phone,
                'CallBackURL' => config('services.mpesa.stk_callback_url'),
                'AccountReference' => Str::limit($reference, 12, ''),
                'TransactionDesc' => Str::limit($description, 13, ''),
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'STK Push request failed: ' . $response->body()
            );
        }

        $data = $response->json();

        if (($data['ResponseCode'] ?? null) !== '0') {
            throw new RuntimeException(
                $data['ResponseDescription']
                    ?? $data['errorMessage']
                    ?? 'STK Push was rejected.'
            );
        }

        return $data;
    }

    public function b2c(
        string $phone,
        float $amount,
        string $originatorConversationId,
        string $remarks = 'Loan disbursement'
    ): array {
        $response = $this->client()
            ->withToken($this->accessToken())
            ->post($this->baseUrl . '/mpesa/b2c/v3/paymentrequest', [
                'OriginatorConversationID' => $originatorConversationId,
                'InitiatorName' => config('services.mpesa.initiator_name'),
                'SecurityCredential' => config(
                    'services.mpesa.security_credential'
                ),
                'CommandID' => 'BusinessPayment',
                'Amount' => (int) ceil($amount),
                'PartyA' => config('services.mpesa.b2c_shortcode'),
                'PartyB' => $phone,
                'Remarks' => Str::limit($remarks, 100),
                'QueueTimeOutURL' => config('services.mpesa.b2c_timeout_url'),
                'ResultURL' => config('services.mpesa.b2c_result_url'),
                'Occasion' => 'Loan disbursement',
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'B2C request failed: ' . $response->body()
            );
        }

        $data = $response->json();

        if (isset($data['errorCode'])) {
            throw new RuntimeException(
                $data['errorMessage'] ?? 'B2C request was rejected.'
            );
        }

        return $data;
    }
}
