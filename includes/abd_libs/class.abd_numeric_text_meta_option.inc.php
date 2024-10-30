<?php
/**
 * Number textbox meta option
 * 
 * Implement HTML textbox restricted to numeric input.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_numeric_text_meta_option extends abd_text_meta_option {
	protected $min_number; //minimum value
	protected $max_number; //maximum value
	protected $whole_number; //true or false 
	
	public function __construct( $name, $title, $description, $min_number, $max_number, $default_value, $whole_number = true ) {
		$this->min_number = $min_number;
		$this->max_number = $max_number;
		$this->whole_number = $whole_number;
			
		if ( $this->validate( $default_value ) && $min_number <= $max_number ) {
		 	 parent::__construct( $name, $title, $description, $default_value);
		} else {
		  	throw new Exception ( sprintf( "Invalid default, min, or max values for numeric text box titled '%s'", $this->title ) );
		}
	}
	
	public function validate( $val) {
	  	if ( is_numeric( $val ) && $val >= $this->min_number && $val <= $this->max_number ) {
			if ( $this->whole_number && ! preg_match( '/^\d+$/', $val ) ) {
				return false;
			} else {
			  	return true;
			}
		} else {
		  	return false;
		}
	}
}