<?php

class _enroll_ctlr extends _ctlr
{

    public function __construct()
    {
        parent::__construct('_enroll');
    }

    /**
     * Handles enrollment of a new member with payment via Stripe.
     *
     * @return bool 
     */
    public function save(): bool
    {
        $data = [
            'sub_plan_id' => _POST['sub_plan_id'] ?? null,
            'mem_fname' => _POST['mem_fname'] ?? null,
            'mem_lname' => _POST['mem_lname'] ?? null,
            'mem_email' => _POST['mem_email'] ?? null,
            'mem_phone' => _POST['mem_phone'] ?? null,
            'billing_address' => _POST['billing_address'] ?? [],
            'payment' => _POST['payment'] ?? []
        ];

        if (!$data['plan_id'] || !$data['mem_email'] || empty($data['payment'])) {
            $this->fail('Missing required fields.');
            return false;
        }

        $result = $this->obj->handleEnrollment($data);

        if (!$result['success']) {
            $this->fail($result['message']);
            return false;
        }

        $this->success('Enrollment completed successfully');
        return true;
    }
}
