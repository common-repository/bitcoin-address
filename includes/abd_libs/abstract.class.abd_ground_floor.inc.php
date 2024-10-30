<?php
/**
 * Another base class for abd_option and abd_meta_option
 * 
 * - Assigns and validates title and description variables
 * - Provides translation function.
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 * 
*/
abstract class abd_ground_floor extends abd_foundation {
	protected $title;
	protected $description;
		
	function __construct( $name, $title, $description ) {
		if ( $this->check_title_desc( $title ) && $this->check_title_desc( $description ) ) {
			$test = parent::__construct( $name );
			$this->title = $title;
			$this->description = $description;
		} else {
			throw new Exception( 'HTML code is not allowed in title or description argument' );
		}
	}
	
	protected function check_title_desc( $text ) {
	  	if ( preg_match( '/[<>]+/', $text ) ) { //disallow html tags
		  	return false;
	  	} else {
		  	return true;
	  	}
	}

	public function __get( $variable ) {
		if ( method_exists( $this, "get_$variable") ) {
			return call_user_func( array( $this, "get_$variable" ) );
		} elseif ( property_exists ( $this, $variable ) ) {
			return $this->$variable;
		}
		throw new Exception( 'Class property does not exist' );
	}
}