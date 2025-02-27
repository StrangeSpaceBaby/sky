<?php

/**
 *	_mem is short for member.  Rather than use user or the longer 'person', I opted for _mem short for member for users.
 */

/**
 * For retrieving specific _mem values entirely or by key
 * 
 * @TODO me array is never hydrated so this ctlr is useless
 * @deprecated
 */
class _mem_ctlr extends _ctlr
{
	private array $me;

	public function __construct()
	{
		parent::__construct( '_mem' );

		$this->me = array();
	}

	public function get_all() : bool|array
	{
		// Rather than a simple list,
		// this will get a fully joined list
		$_mems = $this->obj->get_all();
		if( FALSE === $_mems )
		{
			$this->fail( $this->obj->get_error_msg() );
			return FALSE;
		}

		$this->success( '_mems fetched' );
		return $_mems;
	}

	/**
	 *	_mem() returns all or specific information about the authenticated _mem
	 *	@param   string	$key	the _mem column name
	 *	@return  array|string	The value of the _mem column or table for the logged in user depending on supplied key
	 */
	public function _mem( string $key = NULL ) : string|array
	{
		if( !$key )
		{
			return $this->me;
		}

		return $this->me[$key];
	}
}
