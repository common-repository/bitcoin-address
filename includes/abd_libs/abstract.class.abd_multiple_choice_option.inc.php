<?php
/**
 * Multiple choice options
 * 
 * Provides the base class for options that allow multiple selection.
 * Examples are check boxes and multiple select boxes.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 * 
 *
**/
abstract class abd_multiple_choice_option extends abd_option {
	protected $all_values;
	
	public function __construct( $name, $title, $type, $description, $section_name, array $all_values, $default_values = NULL, $validation_callback = NULL ) {
		
		$this->all_values= $all_values;

		if ( $this->check_value_exists ( $default_values ) ) {
			parent::__construct( $name, $title, $type, $description, $section_name, $default_values, $validation_callback );		  
		} else {
			throw new Exception( 'Invalid default values: Default values must be an array which is subset of the array of values that make up the all values argument.' );
		}
	}
	
	protected function check_value_exists( $values ) {
		if ( ! is_array( $values )  ) {
			if ( $values == NULL ) {
			  	return true;
			} else {
			  	return false;
			}
		}
		$all_keys = array_keys ( $this->all_values );
		$diff = array_diff ( $values, $all_keys ) ;
		
		if ( count( $diff ) > 0 ) {
		  	return false;
		} else {
		  	return true;
		}
	}	

	protected function is_selected( $value ) {
		if ( ! is_array( $this->value ) && $this->value == NULL ) {
			return false;
		}
	  	return in_array( $value, $this->value);
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
			  	throw new Exception ( 'Invalid callback function for ' . $this->title . '. Function or class method does not exist.' );
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