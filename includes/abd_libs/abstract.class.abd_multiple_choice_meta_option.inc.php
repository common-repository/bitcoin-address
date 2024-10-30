<?php
/**
 * Multiple choice meta options
 * 
 * Provides the base class for custom meta options that allow multiple selection.
 * Examples are check boxes and multiple select boxes.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 * 
 */
abstract class abd_multiple_choice_meta_option extends abd_meta_option {
	protected $all_values;
	
	public function __construct( $name, $title, $type, $description, array $all_values, $default_values = NULL, $validation_callback = NULL ) {
		
		$this->all_values= $all_values;

		if ( $this->check_value_exists ( $default_values ) ) {
			parent::__construct( $name, $title, $type, $description, $default_values, $validation_callback );		  
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

	public function get_value() {
		// multiple choice options always return an array even if the stored value is an empty string.	
		return (array) parent::get_value(); 
	}
}