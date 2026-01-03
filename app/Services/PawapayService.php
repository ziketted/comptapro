<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PawapayService
{
    protected string $baseUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('services.pawapay.base_url', 'https://api.pawapay.cloud');
        $this->apiToken = config('services.pawapay.api_token');
    }

    /**
     * Initiate a deposit (subscription payment)
     */
    public function initiateDeposit(array $data): array
    {
        $payload = [
            'depositId' => $data['deposit_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'correspondent' => $data['correspondent'], // MTN, AIRTEL, etc.
            'payer' => [
                'type' => 'MSISDN',
                'address' => [
                    'value' => $data['phone_number']
                ]
            ],
            'customerTimestamp' => now()->toISOString(),
            'statementDescription' => $data['description'] ?? 'FinOp Manager Subscription'
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/deposits', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'deposit_id' => $data['deposit_id']
                ];
            }

            Log::error('Pawapay deposit initiation failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Payment initiation failed',
                'code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Pawapay API error', [
                'message' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'error' => 'Service temporarily unavailable',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Check deposit status
     */
    public function checkDepositStatus(string $depositId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get($this->baseUrl . '/deposits/' . $depositId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Status check failed'
            ];

        } catch (\Exception $e) {
            Log::error('Pawapay status check error', [
                'deposit_id' => $depositId,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Service temporarily unavailable'
            ];
        }
    }

    /**
     * Handle webhook callback
     */
    public function handleWebhook(array $payload): bool
    {
        try {
            $depositId = $payload['depositId'];
            $status = $payload['status'];

            Log::info('Pawapay webhook received', [
                'deposit_id' => $depositId,
                'status' => $status,
                'payload' => $payload
            ]);

            // Update subscription status based on payment status
            if ($status === 'COMPLETED') {
                $this->activateSubscription($depositId);
            } elseif ($status === 'FAILED') {
                $this->handleFailedPayment($depositId, $payload);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'payload' => $payload,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Activate subscription after successful payment
     */
    protected function activateSubscription(string $depositId): void
    {
        // Find organization by deposit ID and activate subscription
        // This would typically involve updating the organization's subscription status
        Log::info('Activating subscription for deposit: ' . $depositId);
    }

    /**
     * Handle failed payment
     */
    protected function handleFailedPayment(string $depositId, array $payload): void
    {
        Log::warning('Payment failed for deposit: ' . $depositId, $payload);
    }

    /**
     * Get available correspondents (mobile money operators)
     */
    public function getCorrespondents(): array
    {
        return [
            'MTN_MOMO_CD' => 'MTN Mobile Money (RDC)',
            'AIRTEL_MONEY_CD' => 'Airtel Money (RDC)',
            'ORANGE_MONEY_CD' => 'Orange Money (RDC)'
        ];
    }
}