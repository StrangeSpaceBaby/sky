<?php

use GoCardlessPro\Client;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Token;

class _enroll extends _obj
{
    private $gcClient;

    public function __construct()
    {
        parent::__construct('enroll_handler');
        Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
        $this->gcClient = new Client([
            'access_token' => getenv('GOCARDLESS_ACCESS_TOKEN'),
            'environment' => getenv('GOCARDLESS_ENV') ?: 'sandbox', 
        ]);
    }

    public function handleEnrollment(array $data): array
    {
        $plan = (new _sub_plan())->get_by_id($data['plan_id']);
        if (!$plan) {
            return ['success' => false, 'message' => 'Invalid subscription plan'];
        }

        $member = (new _mem())->get_by_col(['_mem_email' => $data['email']]);
        if (!$member) {
            return ['success' => false, 'message' => 'Member not found'];
        }

        $company = (new _co())->get_by_col(['fk__mem_id' => $member['_mem_email']]);
        $companyVendor = (new _co_vendor())->get_by_col(['fk__co_id' => $company['_co_id']]);
        $vendor = (new _vendor())->get_by_col(['_vendor_id' => $companyVendor['fk__vendor_id']]);

        $amount = intval($plan['_sub_plan_mth_price'] * 100);

        switch (strtolower($vendor['_vendor_name'])) {
            case 'stripe':
                $paymentResult = $this->processStripePayment($data['payment'], $data['email'], $amount);
                break;
            case 'gocardless':
                $paymentResult = $this->processGoCardlessPayment($data['payment'], $data['email'], $amount);
                break;
            default:
                return ['success' => false, 'message' => 'Unsupported payment vendor'];
        }

        if (!$paymentResult['success']) {
            return ['success' => false, 'message' => 'Payment failed: ' . $paymentResult['message']];
        }

        $saved = $this->saveEnrollment($data, $paymentResult['transaction_id'], $amount);
        if (!$saved) {
            return ['success' => false, 'message' => 'Failed to save enrollment details'];
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
                    'cvc' => $payment['cvv'],
                ],
            ]);

            $charge = Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token->id,
                'description' => 'Enrollment Payment - ' . $email,
            ]);

            return ['success' => true, 'transaction_id' => $charge->id];
        } catch (\Stripe\Exception\CardException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
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
        try {
            $customer = $this->gcClient->customers()->create([
                "params" => [
                    "given_name" => $payment['mem_fname'],
                    "family_name" => $payment['mem_lname'],
                    "email" => $email,
                    "country_code" => "GB", // Extract this from _country table
                    "address_line1" => $payment['billing'],
                    "city" => $payment['city'],
                    "postal_code" => $payment['postal'],
                ]
            ]);
    
            $customerBankAccount = $this->gcClient->customerBankAccounts()->create([
                "params" => [
                    "account_holder_name" => $payment['mem_fname'] . " " . $payment['mem_lname'],
                    "account_number" => $payment['account_number'], 
                    "branch_code" => $payment['sort_code'], // Required for UK BACS payments
                    "country_code" => "GB",
                    "links" => [
                        "customer" => $customer->id
                    ]
                ]
            ]);
    
            $mandate = $this->gcClient->mandates()->create([
                "params" => [
                    "scheme" => "bacs", 
                    "links" => [
                        "customer_bank_account" => $customerBankAccount->id,
                    ]
                ]
            ]);
    
            $paymentResponse = $this->gcClient->payments()->create([
                "params" => [
                    "amount" => $amount,
                    "currency" => "GBP",
                    "links" => [
                        "mandate" => $mandate->id,
                    ],
                    "description" => "Enrollment Payment - " . $email,
                ]
            ]);
    
            return ['success' => true, 'transaction_id' => $paymentResponse->id];
        } catch (\GoCardlessPro\Core\Exception\ApiException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()];
        }
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
        try {
            $enroll = new _enroll();
    
            $enrollData = [
                'fk__sub_plan_id' => $data['plan_id'],
                'fk__mem_id' => (new _mem())->get_by_col(['_mem_email' => $data['email']])['_mem_id'],
                'mem_fname' => $data['mem_fname'],
                'mem_lname' => $data['mem_lname'],
                'mem_email' => $data['email'],
                'mem_phone' => $data['mem_phone'],
                'billing_street' => $data['billing_street'],
                'billing_street2' => $data['billing_street2'] ?? null,
                'billing_city' => $data['billing_city'],
                'billing_state' => $data['billing_state'],
                'billing_postal' => $data['billing_postal'],
                'cc_number' => $data['payment']['cc_number'],
                'cvv' => $data['payment']['cvv'],
                'expiration' => $data['payment']['expiration'],
                '_enroll_active' => true,
                'transaction_id' => $transaction_id,
            ];
            $enrollId = $enroll->save($enrollData);
    
            if (!$enrollId) {
                throw new Exception('Failed to insert enrollment data');
            }
    
            $paymentHistory = new _payment_history();
    
            $paymentHistoryData = [
                'fk__mem_id' => $enrollData['fk__mem_id'],
                'fk__sub_plan_id' => $data['plan_id'],
                'fk__enroll_id' => $enrollId,
                '_payment_history_type' => 'subscription',
                '_payment_history_date_start' => date('Y-m-d'),
                '_payment_history_date_end' => date('Y-m-d', strtotime('+1 month')), //Here the period must be set according to the chosen subscription
            ];
            $paymentHistoryId = $paymentHistory->save($paymentHistoryData);
    
            if (!$paymentHistoryId) {
                throw new Exception('Failed to insert payment history');
            }
    
            return true;
        } catch (Exception $e) {
            error_log('Enrollment save failed: ' . $e->getMessage());
            return false;
        }
    }

}
