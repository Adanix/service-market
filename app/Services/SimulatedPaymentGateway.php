<?php

namespace App\Services;

use App\Support\Reference;
use RuntimeException;

class SimulatedPaymentGateway
{
    public const DEMO_CARDS = [
        '4242424242424242' => [
            'brand' => 'visa',
            'success' => true,
            'failure_code' => null,
            'failure_message' => null,
        ],
        '5555555555554444' => [
            'brand' => 'mastercard',
            'success' => true,
            'failure_code' => null,
            'failure_message' => null,
        ],
        '4000000000000002' => [
            'brand' => 'visa',
            'success' => false,
            'failure_code' => 'card_declined',
            'failure_message' => 'This card was declined. Try another demo card.',
        ],
        '4000000000009995' => [
            'brand' => 'visa',
            'success' => false,
            'failure_code' => 'insufficient_funds',
            'failure_message' => 'This card has insufficient funds. Try another demo card.',
        ],
        '4000000000000069' => [
            'brand' => 'visa',
            'success' => false,
            'failure_code' => 'expired_card',
            'failure_message' => 'This card is expired. Try another demo card.',
        ],
        '4000000000000127' => [
            'brand' => 'visa',
            'success' => false,
            'failure_code' => 'incorrect_cvc',
            'failure_message' => 'The security code is incorrect. Try another demo card.',
        ],
    ];

    public function __construct()
    {
        $driver = config('payments.driver', 'simulated');
        if ($driver !== 'simulated') {
            throw new RuntimeException('Only the simulated payment driver is available in this demo.');
        }
    }

    /**
     * @return array{
     *     success: bool,
     *     brand: string,
     *     last4: string,
     *     gateway_reference: ?string,
     *     failure_code: ?string,
     *     failure_message: ?string
     * }
     */
    public function authorize(string $cardNumber, string $cardExpiry, string $cardCvc): array
    {
        $cleanNumber = preg_replace('/\s+/', '', $cardNumber);
        $last4 = substr($cleanNumber, -4);

        if (array_key_exists($cleanNumber, self::DEMO_CARDS)) {
            $info = self::DEMO_CARDS[$cleanNumber];
            return [
                'success' => $info['success'],
                'brand' => $info['brand'],
                'last4' => $last4,
                'gateway_reference' => $info['success'] ? Reference::gateway() : null,
                'failure_code' => $info['failure_code'],
                'failure_message' => $info['failure_message'],
            ];
        }

        // Any other 16-digit number is declined with card_declined
        $brand = str_starts_with($cleanNumber, '5') ? 'mastercard' : 'visa';

        return [
            'success' => false,
            'brand' => $brand,
            'last4' => $last4,
            'gateway_reference' => null,
            'failure_code' => 'card_declined',
            'failure_message' => 'This card was declined. Try another demo card.',
        ];
    }
}
