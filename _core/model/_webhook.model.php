<?php

class _webhook_model extends _model
{
	public array $cols;
	public array $select_cols;
	public array $full_join;

	public function __construct()
	{
		parent::__construct();
		
		$this->log_chan( '_webhook_model' );

		$this->cols = [
			"_webhook_id" => "intunsigned",
			"_webhook_new" => "timestamp",
			"_webhook_edit" => "timestamp",
			"_webhook_del" => "timestamp",
			"_webhook_arch" => "timestamp",
			"_webhook_active" => "tinyint",
			"fk__vendor_id" => "int",
			"_webhook_type" => "enuminboundbilling",
			"_webhook_payload" => "json"
		];

		$this->select_cols = [
			"_webhook_id" => "intunsigned",
			"_webhook_new" => "timestamp",
			"_webhook_edit" => "timestamp",
			"_webhook_active" => "tinyint",
			"_webhook.fk__vendor_id" => "int",
			"_webhook_type" => "enuminboundbilling",
			"_webhook_payload" => "json"
		];

		require_once( MODEL_CORE . '_vendor_model.obj.php' );
		$o__vendor_model = new _vendor_model();
		if( $o__vendor_model->select_cols() )
		{
			$this->select_cols = array_merge( $this->select_cols, $o__vendor_model->select_cols( 'array' ) );
		}



		$this->full_join = [
						'fk__vendor_id' =>
			[
				'table' => '_vendor',
				'join_as' => '_vendor'
			]
		];
	}
}
