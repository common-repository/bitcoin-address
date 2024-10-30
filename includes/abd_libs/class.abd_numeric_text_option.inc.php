<?php
/**
 * Number textbox option
 * 
 * Implement HTML textbox restricted to numeric input.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_numeric_text_option extends abd_text_option {
	protected $min_number; //minimum value
	protected $max_number; //maximum value
	protected $whole_number; //true or false 
	
	public function __construct( $name, $title, $description, $section_name, $min_number, $max_number, $default_value, $whole_number = true ) {

		$this->min_number = $min_number;
		$this->max_number = $max_number;
		$this->whole_number = $whole_number;
			
		if ( $this->check_value( $default_value ) && $min_number <= $max_number ) {
		 	 parent::__construct( $name, $title, $description, $section_name, $default_value);
		} else {
		  	throw new Exception ( sprintf( "Invalid default, min, or max values for numeric text box titled '%s'", $this->title ) );
		}
	}
	
	protected function check_value( $val) {
		if ( is_numeric( $val ) && $val >= $this->min_number && $val <= $this->max_number ) {
			if ( $this->whole_number && ! preg_match( '/^[-+]?\d+$/', $val ) ) { //no decimal points or thousands separators. digits only.
				return false;
			} else {
			  	return true;
			}
		} else {
			return false;
		}
	}
	
	protected function validation_failed_message() {
		if ( $this->whole_number ) {
			$this->admin_message( sprintf( __( "Invalid value for option titled '%s'. Value must be a whole number between %d and %d inclusive." , 'ABDLIBS_LANG'), $this->title, $this->min_number, $this->max_number ) );
		} else {
			$this->admin_message( sprintf( __( "Invalid value for option titled '%s'. Value must be between %f and %f inclusive.", 'ABDLIBS_LANG'), $this->title, $this->min_number, $this->max_number ) );
		}
	}
	
	public function validate( $input ) {
		$in_clean = trim( $input );
	
		if ( $this->check_value( $in_clean) ) {
			$this->value = $in_clean;
		} else {
		  	$this->validation_failed_message();
		}
			
		return $this->value ;
	}
}