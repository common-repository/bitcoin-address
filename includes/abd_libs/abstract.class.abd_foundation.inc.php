<?php
/** 
 * Foundation class for abd_option and abd_meta_option classes.
 *
 * Assigns and validates "name" class variable. 
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 * 
**/
abstract class abd_foundation {
	protected $name;
	
	function __construct( $name ) {
		if ( $this->check_name( $name ) ) {
			$this->name = $name;
		}
	}

	protected function check_name( $name ) {
		//begins with a letter and includes numbers letters and underscores only
		if ( preg_match( '/^[a-z][a-z_0-9]+$/', $name ) ) {
			return true;
		} else {
			throw new Exception ( 'Name parameter contains invalid characters. Name must begin with a letter and contain only letters, numbers and underscores.' );
		}
	}
}