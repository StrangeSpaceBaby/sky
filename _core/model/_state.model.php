<?php

/**
 *	_state_model auto-generated
 */

class _state_model extends _model
{
	public array $cols;
	public array $select_cols;
	public array $full_join;


	public function __construct()
	{
		parent::__construct();

		$this->cols = [
			"_state_id" => "int",
			"_state_new" => "timestamp",
			"_state_edit" => "timestamp",
			"_state_arch" => "timestamp",
			"_state_del" => "timestamp",
			"_state_active" => "tinyint",
			"_state_name" => "char",
			"_state_abbrev" => "char",
			"fk__country_id" => "int",
			"_state_display_order" => "smallint",

		];

		$this->select_cols = [
			"_state_id" => "int",
			"_state_new" => "timestamp",
			"_state_edit" => "timestamp",
			"_state_active" => "tinyint",
			"_state_name" => "char",
			"_state_abbrev" => "char",
			"_state.fk__country_id" => "int",
			"_state_display_order" => "smallint",

		];

		$this->full_join = [
			'fk__country_id' =>
			[
				'table' => '_country',
				'join_as' => '_country'
			],

		];
	}
}
