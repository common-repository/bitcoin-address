<?php
/**
 * Single choice meta options
 * 
 * Provides the base class for custom meta options that allow only a single selection
 * Examples are radio buttons and single select lists.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
abstract class abd_single_choice_meta_option extends abd_meta_option {
	protected $all_values;
	
	public function __construct( $name, $title, $type, $description, array $all_values, $default_value, $validation_callback = NULL ) {
		
		$this->all_values= $all_values;

		if ( $this->check_value_exists ( $default_value ) ) {
			parent::__construct( $name, $title, $type, $description, $default_value, $validation_callback );		  
		} else {
			throw new Exception( 'Invalid default value: Default value must be a subset of the array of values that make up the all values argument.' );
		}
	}
	
	protected function check_value_exists( $default_value ) {
		if ( ! array_key_exists( $default_value, $this->all_values ) ) {
		  	return false;
		} else {
		  	return true;
		}
	}	

	protected function is_selected( $value ) {
	  	return $value == $this->value;
		
	}
		
	public function validate( $input ) {
		if ( $this->validation_callback != NULL ) {
			if ( is_callable( $this->validation_callback ) ) {
		 		return call_user_func( $this->validation_callback, $input, $this->value );
			} else {
				throw new Exception ( sprintf( 'Invalid callback function for %s . Function or class method does not exist.', $this->title ) );
			}
		} elseif ( $this->check_value_exists ( $input ) ) {
		  	return true;

		} else {
			return false;
		}
	}
	
	public function value_to_title ( $value ) {
		if ( array_key_exists( $value, $this->all_values ) ) {
			return $this->all_values [$value];
		}
		return false;	  
	}

}