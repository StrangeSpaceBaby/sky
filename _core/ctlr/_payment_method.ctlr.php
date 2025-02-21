<?php

class _payment_method_ctlr extends _ctlr
{
	public function __construct()
	{
        // the ctlr name name is used to set
		// the log channel and to instantiate $this->obj
		parent::__construct( '_payment_method' );
	}

	 /**
     * Retrieves a list of all payment methods.
     *
     * @return array|bool
     */
    public function list( array $args = [] ) : array|bool
    {
        $_plans = $this->obj->get_all();
        if (FALSE === $_plans) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Payment methods fetched');
        return $_plans;
    }

	/**
     * Adds a new payment method.
     *
     * @return bool
     */
	public function save() : bool
    {
        $saved = $this->obj->save([
            'type' => _POST['type'],
            'token' => _POST['token']
        ]);
        if (!$saved) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Payment method saved');
        return TRUE;
    }

	/**
     * Deletes a payment method by its ID.
     *
     * @param int $payment_method_id 
     * @return bool
     */
    public function delete(string|int $payment_method_id) : bool
    {
        return parent::delete($payment_method_id);
    }

	/**
     * Sets a payment method as default.
     *
     * @param int $payment_method_id 
     * @return bool
     */
    public function set_default(int $payment_method_id) : bool
    {
        $set = $this->obj->set_default($payment_method_id);
        if (!$set) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Default payment method updated');
        return TRUE;
    }

	/**
     * Updates the billing address for a payment method.
     *
     * @param int $payment_method_id 
     * @param array $billing_address 
     * @return bool 
     */
    public function update_billing_address(int $payment_method_id, array $billing_address) : bool
    {
        $updated = $this->obj->update_billing_address($payment_method_id, $billing_address);
        if (!$updated) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Billing address updated');
        return TRUE;
    }
}
