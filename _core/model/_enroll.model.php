<?php

class _enroll_model extends _model
{
	public array $cols;
	public array $select_cols;
	public array $full_join;

	public function __construct()
	{
		parent::__construct();
		
		$this->log_chan( '_enroll_model' );

		$this->cols = [
			"_enroll_id" => "intunsigned",
			"fk__sub_plan_id" => "int",
			"fk__mem_id" => "int",
			"mem_fname" => "varchar",
			"mem_lname" => "varchar",
			"mem_email" => "varchar",
			"mem_phone" => "varchar",
			"billing_street" => "varchar",
			"billing_street2" => "varchar",
			"billing_city" => "varchar",
			"billing_state" => "varchar",
			"billing_postal" => "varchar",
			"cc_number" => "varchar",
			"cvv" => "varchar",
			"expiration" => "varchar",
			"_enroll_new" => "timestamp",
			"_enroll_edit" => "timestamp",
			"_enroll_del" => "timestamp",
			"_enroll_arch" => "timestamp",
			"_enroll_active" => "tinyint",
			"_enroll_ulid" => "varchar"
		];

		$this->select_cols = [
			"_enroll_id" => "intunsigned",
			"_enroll.fk__sub_plan_id" => "int",
			"_enroll.fk__mem_id" => "int",
			"mem_fname" => "varchar",
			"mem_lname" => "varchar",
			"mem_email" => "varchar",
			"mem_phone" => "varchar",
			"billing_street" => "varchar",
			"billing_street2" => "varchar",
			"billing_city" => "varchar",
			"billing_state" => "varchar",
			"billing_postal" => "varchar",
			"cc_number" => "varchar",
			"cvv" => "varchar",
			"expiration" => "varchar",
			"_enroll_new" => "timestamp",
			"_enroll_edit" => "timestamp",
			"_enroll_active" => "tinyint",
			"_enroll_ulid" => "varchar"
		];

		require_once( MODEL_CORE . '_sub_plan_model.obj.php' );
		$o__sub_plan_model = new _sub_plan_model();
		if( $o__sub_plan_model->select_cols() )
		{
			$this->select_cols = array_merge( $this->select_cols, $o__sub_plan_model->select_cols( 'array' ) );
		}

		require_once( MODEL_CORE . '_mem_model.obj.php' );
		$o__mem_model = new _mem_model();
		if( $o__mem_model->select_cols() )
		{
			$this->select_cols = array_merge( $this->select_cols, $o__mem_model->select_cols( 'array' ) );
		}


		$this->full_join = [
			'fk__sub_plan_id' =>
			[
				'table' => '_sub_plan',
				'join_as' => '_sub_plan'
			],
			'fk__mem_id' =>
			[
				'table' => '_mem',
				'join_as' => '_mem'
			]
		];
	}
}
