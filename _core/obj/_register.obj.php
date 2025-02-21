<?php

class _register extends _obj
{
	public function __construct()
	{
		parent::__construct( '_register' );
	}

	public function register__co(array $vars): array|bool
	{
		p('register__co');
		$username = $vars['_mem_login'];
		$o__co = new _co();
		$o__mem = new _mem();

		/**
		 * Check if _mem with username already exists.
		 * Just because there is a _mem row, doesn't
		 * mean that they own a _co. A _mem can only 
		 * own one _co
		 */

		$_mem_id = $o__mem->is_duplicate_email($username) ?: (new _mem_auth())->check_login_exists($username);
		if ($_mem_id === TRUE) {
			$this->fail('could_not_check_for_unique__co_owner');
			return FALSE;
		}
	
		if ($_mem_id) {
			$_co = $o__co->get_by_owner__mem_id($_mem_id);
			if ($_co === FALSE) {
				$this->fail($o__co->get_error_msg());
				return FALSE;
			}
			if ($_co) {
				$this->fail('_mem_already_owns__co');
				return FALSE;
			}
		}
	
		$_mem = $_mem_id ? $o__mem->get_by_id($_mem_id) : $o__mem->new_mem($vars);
		if (!$_mem) {
			$this->fail('could_not_create_new__mem');
			return FALSE;
		}
	
		$vars['fk__mem_id'] = $_mem['_mem_id'];
		$vars['_co_domain'] = $vars['_co_domain'] ?: strtolower($this->generateUniqueDomain($username));
		$vars['_co_name'] = $vars['_co_name'] ?: 'New company for ' . _POST['_mem_fname'];
		$vars['_co_ulid'] = $this->generate_ulid();
	
		$co = $o__co->new__co($vars);
		if (!$co) {
			$this->fail('co_could_not_be_created');
			return FALSE;
		}
	
		return $this->handleNewCompanyRegistration($co, $_mem, $vars);
	}
	
	private function generateUniqueDomain(string $username): string
	{
		$now = new DateTime();
		$numeric_part = preg_replace('/\D/', '', $username . $now->format('Uu'));
		return $this->alphaID((int)$numeric_part);
	}
	
	private function handleNewCompanyRegistration(array $co, array $_mem, array $vars): array|bool
	{
		$co_id = $co['_co_id'];
		if (!$co_id) {
			$this->fail($this->get_error_msg());
			return FALSE;
		}
	
		$vars['fk__co_id'] = $co_id;
		$vars['_mem_reset_type'] = 'email_verify';
	
		$_mem_reset = new _mem_reset();
		$reset = $_mem_reset->create_reset([
			'fk__mem_id' => $_mem['_mem_id'],
			'_mem_reset_type' => 'email_verify',
			'fk__co_id' => $co_id
		]);
		if (!$reset) {
			$this->fail('_mem_reset_could_not_be_created');
			return FALSE;
		}
	
		return $this->sendRegistrationEmail($vars, $reset);
	}
	
	private function sendRegistrationEmail(array $vars, array $reset): array
	{
		global $_tpl;
		$_setting = new _setting();
		$app_domain = $_setting->get_by_col(['_setting_key' => 'app_domain'])['_setting_value'];
		$product_name = $_setting->get_by_col(['_setting_key' => 'product_name'])['_setting_value'];
	
		$_tpl->assign('recipient_name', $vars['_mem_name']);
		$_tpl->assign('email_token', $reset['_mem_reset_token']);
		$vars['_co_domain'] .= '.' . $app_domain;
		$_tpl->assign('subscriber_domain', $vars['_co_domain']);
	
		$_comm = new _comm();
		$_comm->email([
			'recipient' => $vars['_mem_login'],
			'subject' => 'Welcome to ' . $product_name . '!',
			'template' => 'email/_new_co'
		]);
	
		$this->success('_co_registered');
		return $vars;
	}
}
