<?php
/**
 * Admin options page sections.
 * 
 * Sections in an admin options. A section belongs to a single page. A section has multiple options
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_section extends abd_ground_floor {
	protected $page_name;
	protected $options;  //array of option objects
	
	function __construct( $name, $title, $description, $page ) {
		parent::__construct( $name, $title, $description );
		$this->page_name  = $page;
		$this->options = array();
		add_action( 'admin_init', array( $this, 'register_section' ) );
	}
	
	public function register_section() {
		add_settings_section( $this->name, $this->title, array( $this, 'display_section_description' ), $this->name );
	}
	
	public function display_section_description() {
		echo "<p id='{$this->name}_id'>{$this->description}</p>";
	}
	
	public function get_option( $option_name ) {
		if ( array_key_exists( $option_name, $this->options ) ) {
			return $this->options [$option_name];
		} else {
			return false;
		}
	}

	public function get_title() {
	  	return $this->title;
	}
	
	public function add_option_before( $name_of_next_option, $type, array $args ) {
		if( array_key_exists( $name_of_next_option, $this->options ) ) {
			
			$method = "add_{$type}_option";
			$new_option = call_user_func_array( array( $this, $method ), $args );
			$new_option_name = $new_option->name;
			$new_option_array = array(  $new_option_name => $new_option );
			$this->options = $this->insert_in_middle( $this->options, $name_of_next_option, $new_option_array );
			$this->resync_options_order();
			//print_r( $this->options );
			return $new_option;
		} else {
			throw new Exception ( 'Next option does not exist' );
		}		
	}
	
	//inserts an element in the middle of an associative array.
	private function insert_in_middle( $arr, $key, $new_element ) {
		$key_location = array_search( $key, array_keys( $arr ));
		$from_key = array_slice( $arr, $key_location );
		$to_key = $key_location ? array_slice( $arr, 0, $key_location ) : array();
		$final_arr = array_merge( $to_key, $new_element, $from_key );
		return $final_arr;
	}
	
	public function remove_option( $name ) {
		if( key_exists( $name, $this->options ) ) {
			$this->options[ $name ]->unhook_register_option();
			unset( $this->options[ $name ] ) ;
			return true;
		} else {
			throw new exception ( "Option $name does not exist in this section" );
		}
	}
	
	private function resync_options_order() {
		foreach( $this->options as $option ) {
			$option->unhook_register_option();
			$option->hook_register_option();
		}
	}

	public function add_date_picker_option( $name, $title, $description, $default_value ) {
	  	return $this->options[ $name ] = new abd_date_picker_option( $name, $title, $description, $this->page_name, $this->name, $default_value ) ;
	}
	
	public function add_color_picker_option( $name, $title, $description, $default_value ) {
	  	$this->options [ $name ] = new abd_color_picker_option( $name, $title, $description, $this->page_name, $this->name, $default_value ) ;
		return $this->options [ $name ] ;
	}

	public function add_yesno_radio_option ( $name, $title, $description, $default_value = 'y', $validation_callback = NULL ) {
	  	$this->options[ $name ] = new abd_radio_option( $name, $title, $description, $this->name, array( 'y' => __( 'Yes', 'ABDLIBS_LANG' ), 'n' => __( 'No', 'ABDLIBS_LANG' ) ), $default_value, $validation_callback );
	  	return $this->options[ $name ];
	}

	public function add_slider_option( $name, $title, $description, $min, $max, $default_value, $whole_number = true, $step = 1 ) {
	  	$this->options [ $name ] = new abd_slider_option( $name, $title, $description, $this->page_name, $this->name, $min, $max, $default_value, $whole_number, $step ) ;
		return $this->options [ $name ] ;
	}

	public function add_numeric_text_option( $name, $title, $description, $min, $max, $default_value, $whole_number = true ) {
	  	$this->options [ $name ] = new abd_numeric_text_option( $name, $title, $description, $this->name, $min, $max, $default_value, $whole_number ) ;
		return $this->options [ $name ] ;
	}

	public function add_upload_option( $name, $title, $description, $validation_callback = NULL ) {
	  	return $this->options[ $name ] = new abd_upload_option( $name, $title, $description, $this->page_name, $this->name, $validation_callback );
	}

	public function add_option( $type, $args ) {
		if ( count( $args ) < 3 || count( $args ) > 6 ) {
			throw new Exception ( 'Invalid arguments' );
		}
		@list( $name, $title, $description, $default_value, $regular_expression, $validation_callback ) = $args;
		$object_name = "abd_{$type}_option";
		if ( ! class_exists( $object_name ) ) {
			throw new Exception( 'Class does not exist' );
		} else {
	  		$this->options [ $name ] = new $object_name( $name, $title, $description, $this->name, $default_value, $regular_expression, $validation_callback ) ;
			return $this->options [ $name ];
		}
	}
	
	public function __call( $name, $args ) {
	  	if ( method_exists( $this, $name ) ) {
		  	return call_user_func_array( array( $this, $name ), $args );
	  	} else {
			$map = array(
					'add_option' => array( 'radio', 'check', 'select', 'multiple_select',
								'text', 'textarea', 'wysiwyg', 'image_select', 
								'inline_radio', 'inline_check' )
					);
			$function_name = "add_%s_option";
			foreach ( $map as $real_function_name => $fake_func_array ) {
				foreach( $fake_func_array as $fake_func ) {
					$fake_function_name = sprintf( $function_name, $fake_func ) ;
					if( $name == $fake_function_name ) {
				  		return call_user_func( array( $this, $real_function_name ), $fake_func, $args );
					}
				}
			}
			
	  	}
		throw new Exception( 'Invalid method' );
	}
}