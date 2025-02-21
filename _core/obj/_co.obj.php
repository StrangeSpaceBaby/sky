<?php

/**
 *	_co (short for company)
 *	The _co class handles scoping the entire application to the correct _co account as well as _co limits, etc.
 */

class _co extends _obj
{
	protected array $_co = [];
	protected int $_co_id = 0;

	public function __construct()
	{
		parent::__construct( '_co' );
		$this->log_lvl( 'error' );

		$this->scope();
	}

	public function get_all() : array|bool
	{
		$sth = $this->query('
			SELECT
			    _co_id,
				_co_name,
				_co_domain,
				DATE_FORMAT( _co_new, "%Y-%m-%d %H:%i" ) AS _co_new,
				_co_active,
				_co_setup,
				_co_configured,
				_mem_fname,
				_mem_lname,
				_sub_plan_name,
				_sub_plan_id
			FROM _co
			LEFT JOIN _sub_plan ON fk__sub_plan_id = _sub_plan_id
			JOIN _mem ON fk__mem_id = _mem_id
		');

		if( FALSE === $sth )
		{
			$this->fail( $this->get_error_msg() );
			return FALSE;
		}

		$_cos = [];
		while( $_co = $sth->fetch() )
		{
			$_cos[$_co['_co_id']] = $_co;
		}

		$this->success( '_cos fetched' );
		return $_cos;
	}

	public function get_by_owner__mem_id( int $_mem_id ) : array|bool
	{
		// Has to be raw query because of checking across all _co
		$query = "SELECT * FROM _co WHERE fk__mem_id = ?";
		$sth = $this->query( $query, [ $_mem_id ]);
		$_co = $sth->fetch();
		if( FALSE === $_co )
		{
			$this->fail( 'could_not_check__co_by_owner__mem_id' );
			return FALSE;
		}

		$this->success( '_co_fetched_by_owner__mem_id' );
		return $_co;
	}

	/**
	 * Checks that subdomain is a valid one and saves it to the current co
	 *
	 * @param string $new_subdomain
	 * @return string|boolean the subdomain or FALSE on error
	 */
	public function save_subdomain( string $new_subdomain ) : string|bool
	{
		if( $new_subdomain = $this->valid_subdomain( $new_subdomain ) )
		{
			$saved = $this->save([ '_co_id' => $this->_co_id, '_co_domain' => $new_subdomain ]);
			if( FALSE === $saved )
			{
				$this->fail( $this->get_error_msg() );
				return FALSE;
			}

			$this->success( 'subdomain_saved' );
			return $new_subdomain;
		}

		$this->fail( $this->get_error_msg() );
		return FALSE;
	}

	/**
	 * Creates new _co, a _mem should have already been created before calling this
	 *
	 * @param array $vars
	 * @return array|boolean
	 */
	public function new__co( array $vars ) : array|bool
	{
		if( !$this->valid_subdomain( $vars['_co_domain'] ) )
		{
			$this->fail( 'invalid_subdomain' );
			return FALSE;
		}

		unset( $vars['_co_id'] ); // For safety this won't override an existing company
		$saved = $this->save( $vars );
		if( FALSE === $saved )
		{
			$this->fail( '_co_could_not_be_created' );
			return FALSE;
		}

		$this->success( '_co_created' );
		return $this->get_by_id( $saved );
	}

	/**
	 * Called on first login to setup up _co.
	 * Once setup, it marks _co_setup = 1
	 *
	 * @return void
	 */
	public function setup__co()
	{
		//
	}

	/**
	 * Returns FALSE if subdomain is one of reserved words or is already taken.
	 *
	 * @TODO should also check for illegal characters
	 * @param string $subdomain
	 * @return string|boolean
	 */
	public function valid_subdomain( string $subdomain ) : string|bool
	{
		$reserved = [
			'mail',
			'security',
			'hq',
			'admin',
			'in',
			'main',
			'official',
			'www',
			'register',
			'signup'
		];

		if( in_array( $subdomain, $reserved ) )
		{
			$this->fail( 'subdomain_reserved' );
			return FALSE;
		}

		// @TODO Needs to be raw query to search across all _co's
		$query = "SELECT _co_id FROM _co WHERE _co_domain = ?";
		$sth = $this->query( $query, [ $subdomain ]);
		if( FALSE === $sth )
		{
			$this->fail( 'invalid_subdomain' );
			return FALSE;
		}

		$taken = $sth->fetch();
		if( $taken['_co_id'] )
		{
			$this->fail( 'subdomain_exists' );
			return FALSE;
		}

		$this->success( 'subdomain_not_taken' );
		return strtolower( $subdomain );
	}

	/**
	 * _co returns the value for a supplied key or the entire _co row if no key is supplied.
	 *
	 * @param	string	$key the column name from the _co table
	 * @return	mixed The value of the supplied key or the entire _co row
	 */
	public function _co( ?string $key = '', bool $force_refresh = FALSE ) : mixed
	{
		if( !$this->_co || $force_refresh )
		{
			// Just in case the value exists in the database but hasn't been updated to the session yet.
			// This will reselect the sub row based on the HTTP_HOST.
			$this->scope();
		}

		if( !$key )
		{
			return $this->_co;
		}

		return $this->_co[$key]; // This value may still not exist
	}

	/**
	 *	scope() determines the subscriber scope using the HTTP_HOST value. Although HTTP_HOST can be spoofed, this allows for vhosts.
	 *	Since it will be checked against the database anyway, this is safe. Also, UseCanonicalName is On.
	 *
	 * @return object|boolean
	 */
	public function scope() : object|bool
	{
		if( SUBDOMAIN_SCOPE ) // Set in init.json
		{
			$url = explode( ".", $_SERVER['HTTP_HOST'] );

			$domain = array_pop( $url ); // tld
			$domain = array_pop( $url ) . '.' . $domain; // domain name
			$co = implode( ".", $url );
		}
		else
		{
			$uri = array_filter( explode( "/", $_REQUEST['request'] ) );
			$co = array_shift( $uri );
		}

		// Because the _co_id doesn't exist prior to instantiation, we have to call this directly, rather than calling get_by_col
		$q = "SELECT * FROM {$this->table} WHERE `{$this->table}`.`_co_domain` = ?";
		$sth = $this->query( $q, [ $co ]);

		$co = $sth->fetch();
		if( !$co['_co_id'] )
		{
			return FALSE;
		}

		// These will be available to other objects and classes now
		$_SESSION['co'] = $co;
		$this->_co = $co;
		$this->_co_id = $co['_co_id'];
		$this->_co_ulid = $co['_co_ulid'];

		return $this;
	}
}
