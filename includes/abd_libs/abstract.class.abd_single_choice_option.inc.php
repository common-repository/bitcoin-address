<?php
/**
 * Single choice options
 * 
 * Provides the base class for admin options that allow only a single selection
 * Examples are radio buttons and single select lists.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/

abstract class abd_single_choice_option extends abd_option {
	protected $all_values;
	
	public function __construct( $name, $title, $type, $description, $section_name, array $all_values, $default_value, $validation_callback = NULL ) {
		$this->all_values= $all_values;

		if ( $this->check_value_exists ( $default_value ) ) {
			parent::__construct( $name, $title, $type, $description, $section_name, $default_value, $validation_callback );		  
		} else {
			throw new Exception ( 'Invalid default value: Default value must be a subset of the array of values that make up the all values argument.' );
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
				if( call_user_func( $this->validation_callback, $input, $this->value ) ){
				  	$this->value = $input;
				} else {
			  		$this->validation_failed_message();
				}
			} else {
			  	throw new Exception ( sprintf( 'Invalid callback function for %s . Function or class method does not exist.', $this->title ) );
			}
		} elseif ( $this->check_value_exists ( $input ) ) {
		  	$this->value = $input;
		} else {
			$this->validation_failed_message();
		}
		return $this->value ;
	}
	
	public function value_to_title ( $value ) {
		if ( array_key_exists( $value, $this->all_values ) ) {
			return $this->all_values [$value];
		}
		return false;	  
	}

}