<?php

class _co_sub_plan_ctlr extends _ctlr
{
	public function __construct()
	{
		// the ctlr name name is used to set
		// the log channel and to instantiate $this->obj
		parent::__construct( '_co_sub_plan' );
	}

	/**
     * Cancels a subscription by its ID.
     *
     * @param int $subscription_id 
     * @return bool 
     */
	public function cancel(int $subscription_id) : bool
    {
        $canceled = $this->obj->cancel($subscription_id);
        if (!$canceled) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Subscription canceled');
        return TRUE;
    }

	/**
     * Reactivates a subscription by its ID.
     *
     * @param int $subscription_id 
     * @return bool
     */
    public function reactivate(int $subscription_id) : bool
    {
        $reactivated = $this->obj->reactivate($subscription_id);
        if (!$reactivated) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Subscription reactivated');
        return TRUE;
    }

	/**
     * Fetches details of a subscription.
     *
     * @param int $subscription_id 
     * @return array|bool 
     */
    public function details(int $subscription_id) : array|bool
    {
        $details = $this->obj->details($subscription_id);
        if (FALSE === $details) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Subscription details fetched');
        return $details;
    }

	/**
     * Estimates the billing impact of a subscription plan change.
     *
     * @param array $params 
     * @return array|bool
     */
    public function estimated_new_billing(array $params) : array|bool
    {
        $estimated = $this->obj->estimated_new_billing($params);
        if (FALSE === $estimated) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Estimated new billing calculated');
        return $estimated;
    }

	/**
     * Changes the subscription plan.
     *
     * @param array $params 
     * @return bool
     */
    public function change_plan(array $params) : bool
    {
        $changed = $this->obj->change_plan($params);
        if (!$changed) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Plan changed successfully');
        return TRUE;
    }
}
