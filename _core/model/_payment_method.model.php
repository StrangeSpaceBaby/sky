<?php

class _payment_method_model extends _model
{
	public array $cols;
	public array $select_cols;
	public array $full_join;

	public function __construct()
	{
		parent::__construct();
		
		$this->log_chan( '_payment_method_model' );

		$this->cols = [
						"_payment_method_id" => "intunsigned",
			"_payment_method_new" => "timestamp",
			"_payment_method_edit" => "timestamp",
			"_payment_method_del" => "timestamp",
			"_payment_method_arch" => "timestamp",
			"_payment_method_active" => "tinyint",
			"_payment_method_type" => "varchar",
			"_payment_method_token" => "varchar",
			"_payment_method_default" => "tinyint",
			"_payment_method_billing_address" => "json"
		];

		$this->select_cols = [
						"_payment_method_id" => "intunsigned",
			"_payment_method_new" => "timestamp",
			"_payment_method_edit" => "timestamp",
			"_payment_method_active" => "tinyint",
			"_payment_method_type" => "varchar",
			"_payment_method_token" => "varchar",
			"_payment_method_default" => "tinyint",
			"_payment_method_billing_address" => "json"
		];

		

		$this->full_join = [
			
		];
	}
}
