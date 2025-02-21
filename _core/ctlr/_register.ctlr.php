<?php

/**
 *	sub_ctlr handles everything related to subscribers.
 */

class _register_ctlr extends _ctlr
{
	public function __construct()
	{
		parent::__construct( '_register' );
		$this->log_chan( '_register' )->log_lvl( 'error' );
	}

	/**
	 * Should check if the desired subdomain is available
	 *
	 * @deprecated 
	 * @TODO fix this or remove it to the obj checks
	 * @param string $subdomain
	 * @return boolean TRUE if subdomain is available, FALSE on error
	 */
	public function check_subdomain( string $subdomain ) : bool
	{
		$taken = $this->obj->valid_subdomain( $subdomain );
		if( TRUE !== $taken )
		{
			$this->fail( $this->obj->get_error_msg() );
			return FALSE;
		}

		$this->success( 'subdomain_available' );
		return TRUE;
	}

	/**
	 * This method makes check for uniqueness of subdomain
	 * and owner _mem and calls $this->obj->register__co().
	 *
	 * @return array|boolean array of registration details or FALSE on error
	 */
	public function register__co() : array|bool
	{
		p(_POST);
	
		if (empty(_POST['tos_agree'])) {
			$this->fail('tos_agreement_needed');
			return FALSE;
		}
	
		if (empty(_POST['pp_agree'])) {
			$this->fail('pp_agreement_needed');
			return FALSE;
		}
	
		$username = filter_var(_POST['_mem_login'], FILTER_VALIDATE_EMAIL);
		$username_verify = filter_var(_POST['_mem_login_verify'], FILTER_VALIDATE_EMAIL);
	
		if (!$username) {
			$this->fail('not_a_valid_email');
			return FALSE;
		}
	
		if ($username !== $username_verify) {
			$this->fail('emails_do_not_match');
			return FALSE;
		}
	
		$co_vars = _POST;
		$co_vars['_mem_email'] = $co_vars['_mem_email'] ?? $username;
	
		$saved = $this->obj->register__co($co_vars);
	
		if (!$saved) {
			$this->fail($this->obj->get_error_msg());
			return FALSE;
		}
	
		$this->success('_co_registration_successful');
		return $saved;
	}
}
