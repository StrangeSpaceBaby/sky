<?php

class _co_sub_metric_ctlr extends _ctlr
{
	public function __construct()
	{
		// the ctlr name name is used to set
		// the log channel and to instantiate $this->obj
		parent::__construct( '_co_sub_metric' );
	}

	/**
     * Fetches the history of a subscription's metrics.
     *
     * @param int $subscription_id 
     * @return array|bool 
     */
    public function history(int $subscription_id) : array|bool
    {
        $history = $this->obj->history($subscription_id);
        if (!$history) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Metric history fetched');
        return $history;
    }

    /**
     * Fetches the overage details of a subscription.
     *
     * @param int $subscription_id 
     * @return array|bool 
     */
    public function overage(int $subscription_id) : array|bool
    {
        $overage = $this->obj->overage($subscription_id);
        if (!$overage) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Overage details fetched');
        return $overage;
    }

	/**
     * Fetches all one-time metrics for a subscription.
     *
     * @param int $subscription_id 
     * @return array|bool 
     */
    public function list_onetime(int $subscription_id) : array|bool
    {
        $onetime = $this->obj->list_onetime($subscription_id);
        if (!$onetime) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('One-time metrics fetched');
        return $onetime;
    }

	/**
     * Processes a purchase for a subscription.
     *
     * @param array $purchase_details 
     * @return bool 
     */
    public function purchase(array $purchase_details) : bool
    {
        $purchased = $this->obj->purchase($purchase_details);
        if (!$purchased) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Purchase processed successfully');
        return TRUE;
    }
}
