<?php

class _webhook_ctlr extends _ctlr
{
	public function __construct()
	{
		// the ctlr name name is used to set
		// the log channel and to instantiate $this->obj
		parent::__construct( '_webhook' );
	}

	/**
     * Handles inbound billing webhook.
     *
     * @param array $vendor_payload 
     * @return bool 
     */
	public function inbound_billing(array $vendor_payload) : bool
    {
        $processed = $this->obj->inbound_billing($vendor_payload);
        if (!$processed) {
            $this->fail($this->obj->get_error_msg());
            return FALSE;
        }

        $this->success('Webhook processed');
        return TRUE;
    }
}
