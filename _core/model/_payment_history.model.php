<?php

class _payment_history_model extends _model
{
	public array $cols;
	public array $select_cols;
	public array $full_join;

	public function __construct()
	{
		parent::__construct();
		
		$this->log_chan( '_payment_history_model' );

		$this->cols = [
			"_payment_history_id" => "intunsigned",
			"_payment_history_new" => "timestamp",
			"_payment_history_edit" => "timestamp",
			"_payment_history_del" => "timestamp",
			"_payment_history_arch" => "timestamp",
			"_payment_history_active" => "tinyint",
			"fk__mem_id" => "int",
			"fk__sub_plan_id" => "int",
			"fk__enroll_id" => "int",
			"_payment_history_type" => "enumallsubscriptionpurchases",
			"_payment_history_date_start" => "date",
			"_payment_history_date_end" => "date"
		];

		$this->select_cols = [
			"_payment_history_id" => "intunsigned",
			"_payment_history_new" => "timestamp",
			"_payment_history_edit" => "timestamp",
			"_payment_history_active" => "tinyint",
			"_payment_history.fk__mem_id" => "int",
			"_payment_history.fk__sub_plan_id" => "int",
			"_payment_history.fk__enroll_id" => "int",
			"_payment_history_type" => "enumallsubscriptionpurchases",
			"_payment_history_date_start" => "date",
			"_payment_history_date_end" => "date"
		];

		require_once( MODEL_CORE . '_mem.obj.php' );
		$o__mem_model = new _mem_model();
		if( $o__mem_model->select_cols() )
		{
			$this->select_cols = array_merge( $this->select_cols, $o__mem_model->select_cols( 'array' ) );
		}

		require_once( MODEL_CORE . '_sub_plan.obj.php' );
		$o__sub_plan_id_model = new _sub_plan_model();
		if( $o__sub_plan_id_model->select_cols() )
		{
			$this->select_cols = array_merge( $this->select_cols, $o__sub_plan_id_model->select_cols( 'array' ) );
		}

		require_once( MODEL_CORE . '_enroll.obj.php' );
		$o__enroll_model = new _enroll_model();
		if( $o__enroll_model->select_cols() )
		{
			$this->select_cols = array_merge( $this->select_cols, $o__enroll_model->select_cols( 'array' ) );
		}


		$this->full_join = [
			
		];
	}
}
