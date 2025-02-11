<?php

class _webhook extends _obj
{
	public function __construct()
	{
		// the obj name name is used to set
		// the log channel and the table for
		// the obj to work with. Change this
		// if the obj works with another table
		parent::__construct( '_webhook' );
	}
}
