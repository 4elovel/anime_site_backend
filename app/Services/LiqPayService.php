<?php

namespace AnimeSite\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiqPayService
{
    /**
     * @var string
     */
    private string $publicKey;

    /**
     * @var string
     */
    private string $privateKey;

    /**
     * @var string
     */
    private string $apiUrl;

    /**
     * LiqPayService constructor.
     */
    public function __construct()
    {
        $this->publicKey = config('services.liqpay.public_key');
        $this->privateKey = config('services.liqpay.private_key');
        $this->apiUrl = config('services.liqpay.api_url', 'https://www.liqpay.ua/api/');
    }

    /**
     * Створити платіж.
     *
     * @param array $params
     * @return array
     */
    public function createPayment(array $params): array
    {
        $defaultParams = [
            'version' => 3,
            'public_key' => $this->publicKey,
            'action' => 'pay',
            'sandbox' => config('app.env') !== 'production',
        ];

        $params = array_merge($defaultParams, $params);

        $data = base64_encode(json_encode($params));
        $signature = $this->generateSignature($data);

        return [
            'data' => $data,
            'signature' => $signature,
            'checkout_url' => 'https://www.liqpay.ua/api/3/checkout?data=' . $data . '&signature=' . $signature,
        ];
    }

    /**
     * Перевірити статус платежу.
     *
     * @param string $orderId
     * @return string|null
     */
    public function checkStatus(string $orderId): ?string
    {
        try {
            $params = [
                'version' => 3,
                'public_key' => $this->publicKey,
                'action' => 'status',
                'order_id' => $orderId,
            ];

            $data = base64_encode(json_encode($params));
            $signature = $this->generateSignature($data);

            $response = Http::post($this->apiUrl . '3/checkout', [
                'data' => $data,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['status'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Error checking LiqPay payment status: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
        }

        return null;
    }

    /**
     * Скасувати платіж.
     *
     * @param string $orderId
     * @return array|null
     */
    public function cancelPayment(string $orderId): ?array
    {
        try {
            $params = [
                'version' => 3,
                'public_key' => $this->publicKey,
                'action' => 'cancel',
                'order_id' => $orderId,
            ];

            $data = base64_encode(json_encode($params));
            $signature = $this->generateSignature($data);

            $response = Http::post($this->apiUrl . '3/checkout', [
                'data' => $data,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Error cancelling LiqPay payment: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
        }

        return null;
    }

    /**
     * Повернути кошти за платіж.
     *
     * @param string $orderId
     * @param float $amount
     * @param string $comment
     * @return array|null
     */
    public function refundPayment(string $orderId, float $amount, string $comment): ?array
    {
        try {
            $params = [
                'version' => 3,
                'public_key' => $this->publicKey,
                'action' => 'refund',
                'order_id' => $orderId,
                'amount' => $amount,
                'comment' => $comment,
            ];

            $data = base64_encode(json_encode($params));
            $signature = $this->generateSignature($data);

            $response = Http::post($this->apiUrl . '3/checkout', [
                'data' => $data,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Error refunding LiqPay payment: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'amount' => $amount,
            ]);
        }

        return null;
    }

    /**
     * Декодувати та перевірити дані колбеку.
     *
     * @param string $data
     * @param string $signature
     * @return array
     * @throws \Exception
     */
    public function decodeAndVerifyData(string $data, string $signature): array
    {
        $decodedData = json_decode(base64_decode($data), true);
        
        if (!$decodedData) {
            throw new \Exception('Invalid data format');
        }
        
        $generatedSignature = $this->generateSignature($data);
        
        if ($signature !== $generatedSignature) {
            throw new \Exception('Invalid signature');
        }
        
        return $decodedData;
    }

    /**
     * Створити дані для колбеку.
     *
     * @param string $orderId
     * @param string $status
     * @return array
     */
    public function createCallbackData(string $orderId, string $status): array
    {
        $params = [
            'version' => 3,
            'public_key' => $this->publicKey,
            'action' => 'pay',
            'order_id' => $orderId,
            'status' => $status,
        ];

        $data = base64_encode(json_encode($params));
        $signature = $this->generateSignature($data);

        return [
            'data' => $data,
            'signature' => $signature,
        ];
    }

    /**
     * Згенерувати підпис для даних.
     *
     * @param string $data
     * @return string
     */
    private function generateSignature(string $data): string
    {
        return base64_encode(sha1($this->privateKey . $data . $this->privateKey, true));
    }
}
