<?php
/**
 * Custom Meta box class
 * 
 * Shows a custom meta box on Wordpress post/page edit screens. 
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_meta_box extends abd_ground_floor {
	protected $post_types;
	protected $options;

  	function __construct( $name, $title, $description, array $post_types = array( 'post', 'page' ) ) {
		parent::__construct( $name, $title, $description );
		$this->post_types = $post_types;
		add_action( 'add_meta_boxes', array( $this, 'init' ) );
	}
	
	public function init() {
		foreach( $this->post_types as $type ) {
	  		add_meta_box( $this->name, $this->title, array( $this, 'display_meta_box' ), $type );
		}
			
	}

	public function display_meta_box( $post ) {
		echo '<p>' . $this->description . '</p>' ;
		if ( is_array( $this->options ) && count( $this->options ) > 0 ) {
		  
			foreach( $this->options as $option ) {
			  	$option->display_option( $post->ID ) ;
			}
		}
	}
	
	public function add_option( $type, $args ) {
		if ( count( $args ) < 3 || count( $args ) > 6 ) {
			throw new Exception ( 'Invalid arguments' );
		}
		@list( $name, $title, $description, $default_value, $regular_expression, $validation_callback ) = $args;
		$object_name = "abd_{$type}_meta_option";
		if ( ! class_exists( $object_name ) ) {
			throw new Exception( 'Class does not exist' );
		} else {
	  	$this->options [ $name ] = new $object_name( $name, $title, $description, $default_value, $regular_expression, $validation_callback ) ;
		return $this->options [ $name ];
		}
	}
	
	public function add_date_picker_option( $name, $title, $description, $default_value ) {
	  	return $this->options[ $name ] = new abd_date_picker_meta_option( $name, $title, $description, $default_value );
	}
	
	public function add_color_picker_option( $name, $title, $description, $default_value ) {
	  	return $this->options[ $name ] = new abd_color_picker_meta_option( $name, $title, $description, $default_value );
	}
	
	public function add_numeric_text_option( $name, $title, $description, $min, $max, $default_value, $whole_number = true ) {
	  	$this->options [ $name ] = new abd_numeric_text_meta_option( $name, $title, $description, $min, $max, $default_value, $whole_number ) ;
		return $this->options [ $name ] ;
	}

	public function add_slider_option( $name, $title, $description, $min, $max, $default_value, $whole_number = true, $step = 1 ) {
	  	return $this->options [ $name ] = new abd_slider_meta_option( $name, $title, $description, $min, $max, $default_value, $whole_number, $step ) ;
	}

	public function add_upload_option( $name, $title, $description, $validation_callback = NULL ){
	  	$this->options [ $name ] = new abd_upload_meta_option( $name, $title, $description, $validation_callback ) ;
		return $this->options [ $name ];
	}

	public function __call( $name, $args ) {
	  	if ( method_exists( $this, $name ) ) {
		  	return call_user_func_array( array( $this, $name ), $args );
	  	} else {
			$map = array(
					'add_option' => array( 'radio', 'check', 'select', 'multiple_select',
								 'text', 'textarea', 'wysiwyg', 'inline_radio', 'upload' )
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

	public function get_option( $name ) {
	  	if ( array_key_exists( $name, $this->options ) ) {
		  	return $this->options[ $name ];
	  	} else {
		  	return false;
	  	}
	}

	public function get_all_options() {
	  	return $this->options;
	}
}