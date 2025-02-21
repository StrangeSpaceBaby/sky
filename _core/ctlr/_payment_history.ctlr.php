<?php

class _payment_history_ctlr extends _ctlr
{
	public function __construct()
	{
        // the ctlr name name is used to set
		// the log channel and to instantiate $this->obj
		parent::__construct( '_payment_history' );
	}

	/**
     * Fetches all payment history within a given date range.
     *
     * @param array $date_range 
     * @return array|bool
     */
    public function all(array $date_range = []) : array|bool
    {
        $history = $this->obj->all($date_range);
        if (!$history) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Payment history fetched');
        return $history;
    }

	/**
     * Fetches all subscription payments within a given date range.
     *
     * @param array $date_range
     * @return array|bool 
     */
    public function all_subscriptions(array $date_range = []) : array|bool
    {
        $subscriptions = $this->obj->all_subscriptions($date_range);
        if (!$subscriptions) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Subscription payment history fetched');
        return $subscriptions;
    }

	/**
     * Fetches all purchase payments within a given date range.
     *
     * @param array $date_range 
     * @return array|bool 
     */
    public function all_purchases(array $date_range = []) : array|bool
    {
        $purchases = $this->obj->all_purchases($date_range);
        if (!$purchases) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Purchase payment history fetched');
        return $purchases;
    }
}
