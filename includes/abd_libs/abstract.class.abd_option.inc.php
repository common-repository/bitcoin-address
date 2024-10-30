<?php
/**
 * Base options class
 * 
 * Provides the base class for all admin options.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/

abstract class abd_option extends abd_ground_floor {
	protected $type;
	protected $default_value;
	protected $validation_callback;
	protected $value;
	protected $section_name;
	protected $custom_error_message;
	
	function __construct( $name, $title, $type, $description, $section_name, $default_value = NULL, $validation_callback= NULL ) {
		parent::__construct( $name, $title, $description );
		$this->type                 = $type;
		$this->default_value        = $default_value;
		$this->validation_callback  = $validation_callback;
		$this->section_name         = $section_name;
		$this->custom_error_message = '';
		$this->load_option();
		$this->hook_register_option();
	}
	
	private function load_option() {
		$val = get_option( $this->name );
		if ( $val !== false ) {
			$this->value = $val;
		} else {
			$this->value = $this->default_value;
		}
	}
	
	protected function admin_message( $message, $type = 'error' ) {
		if( function_exists( 'add_settings_error' ) ) {
			add_settings_error( $this->section_name,  $this->section_name.'_error', $message, $type );
		}
	}
	
	protected function validation_failed_message() {
		$this->admin_message ( sprintf( __( 'Invalid value for option titled "%s".' , 'ABDLIBS_LANG'), $this->title ) . ' ' . $this->custom_error_message );
	}
	
	public function custom_error_message( $message ){
	  	$this->custom_error_message = $message;
	}
	
	public function register_option() {
		register_setting( $this->section_name, $this->name, array( $this, 'validate' ) ); 
		add_settings_field( $this->name, $this->title, array( $this, 'display_option' ), $this->section_name, $this->section_name );
	}
	
	public function unhook_register_option() {
		remove_action( 'admin_init', array( $this, 'register_option' ) );		
	}
	
	public function hook_register_option() {
		add_action( 'admin_init', array( $this, 'register_option' ) );		
	}
	
	abstract public function display_option();
	abstract public function validate( $input );
	
	public function set_value( $val ) {
	  	$clean = $this->validate( $val );
		if( $clean == $val ) {
		  	$this->value = $val;
			update_option( $this->name, $this->value );
			return true;
		} else {
		 	return false;
		}
	}

	public function __set( $variable, $value ) {
	  	if ( method_exists( $this, "set_$variable") ) {
			return call_user_func( array( $this, "set_$variable" ), $value );
		} else {
			throw new Exception( 'Class property does not exist or cannot be modified' );
		}
	}
}