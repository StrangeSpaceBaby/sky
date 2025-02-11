<?php

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Token;

class _enroll extends _obj
{
    public function __construct()
    {
        parent::__construct('_enroll');
        Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
    }

    /**
     * Handles enrollment and payment process.
     *
     * @param array $data
     * @return array
     */
    public function handleEnrollment(array $data): array
    {
        $sub_plan = new _sub_plan();
        $plan_data = $sub_plan->get_by_id($data['plan_id']);
        if (!$plan_data) {
            return ['success' => false, 'message' => 'Invalid subscription plan'];
        }

        $amount = intval(floatval($plan_data['_sub_plan_mth_price']) * 100);

        $payment_vendor = $this->getPaymentVendor(); 

        if ($payment_vendor === 'stripe') {
            $payment_result = $this->processStripePayment($data['payment'], $data['mem_email'], $amount);
        } elseif ($payment_vendor === 'gocardless') {
            $payment_result = $this->processGoCardlessPayment($data['payment'], $data['mem_email'], $amount);
        } else {
            return ['success' => false, 'message' => 'Invalid payment vendor'];
        }

        if (!$payment_result['success']) {
            return ['success' => false, 'message' => 'Payment failed: ' . $payment_result['message']];
        }

        $saved = $this->saveEnrollment($data, $payment_result['transaction_id']);
        if (!$saved) {
            return ['success' => false, 'message' => 'Failed to save enrollment'];
        }

        return ['success' => true];
    }

    /**
     * Processes payment using Stripe.
     *
     * @param array $payment
     * @param string $email
     * @param int $amount
     * @return array
     */
    private function processStripePayment(array $payment, string $email, int $amount): array
    {
        try {
            $token = Token::create([
                'card' => [
                    'number' => $payment['cc_number'],
                    'exp_month' => explode('/', $payment['expiration'])[0],
                    'exp_year' => explode('/', $payment['expiration'])[1],
                    'cvc' => $payment['cvv']
                ]
            ]);

            $charge = Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token->id, // Use generated token
                'description' => 'Enrollment Payment for ' . $email
            ]);

            return ['success' => true, 'transaction_id' => $charge->id];
        } catch (\Stripe\Exception\CardException $e) {
            return ['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()];
        }
    }

    /**
     * Processes payment using GoCardless.
     *
     * @param array $payment
     * @param string $email
     * @param int $amount
     * @return array
     */
    private function processGoCardlessPayment(array $payment, string $email, int $amount): array
    {
        // TODO: Implement GoCardless payment API
        return ['success' => false, 'message' => 'GoCardless integration not yet implemented'];
    }

    /**
     * Saves enrollment and payment details to the database.
     *
     * @param array $data
     * @param string $transaction_id
     * @return bool
     */
    private function saveEnrollment(array $data, string $transaction_id): bool
    {
        $paymentData = [
            'mem_email' => $data['mem_email'],
            'sub_plan_id' => $data['plan_id'],
            'transaction_id' => $transaction_id,
            'amount' => intval(floatval($data['_sub_plan_mth_price']) * 100),
            'payment_status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // TODO: Save to database
        return true;
    }

    /**
     * Retrieves the payment vendor setting from the admin panel.
     *
     * @return string
     */
    private function getPaymentVendor(): string
    {
        // TODO: Fetch from settings table in database
        return 'stripe'; // Default to Stripe for now
    }
}
