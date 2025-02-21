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
        if (empty(_POST['sub_plan_id']) || empty(_POST['mem_email']) || empty(_POST['payment'])) {
            $this->fail('Missing required fields.');
            return FALSE;
        }
    
        $result = $this->obj->handleEnrollment(_POST);
    
        if (!$result) {
            $this->fail($result['message']);
            return FALSE;
        }
    
        $this->success('Enrollment completed successfully');
        return TRUE;
    }
    
}
