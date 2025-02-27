<?php

/**
 *	_co_pref_model auto-generated
 */

class _co_pref_model extends _model
{
public array $cols;
	public array $select_cols;
	public array $full_join;


	public function __construct()
	{
		parent::__construct();

		$this->cols = [
			"_co_pref_id" => "int",
			"_co_pref_new" => "timestamp",
			"_co_pref_edit" => "timestamp",
			"_co_pref_del" => "timestamp",
			"_co_pref_arch" => "timestamp",
			"_co_pref_active" => "tinyint",
			"fk__co_id" => "int",
			"_co_pref_key" => "varchar",
			"_co_pref_val" => "longtext",

		];

		$this->select_cols = [
			"_co_pref_id" => "int",
			"_co_pref_new" => "timestamp",
			"_co_pref_edit" => "timestamp",
			"_co_pref_active" => "tinyint",
			"fk__co_id" => "int",
			"_co_pref_key" => "varchar",
			"_co_pref_val" => "longtext",

		];

		$this->full_join = [

		];
	}
}
