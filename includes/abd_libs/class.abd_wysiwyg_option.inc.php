<?php
/**
 * WYSIWYG Textarea option
 * 
 * Implements a WYSIWYG textarea using the new wp_editor function in wordpress 3.3+
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_wysiwyg_option extends abd_textarea_option {
	
	public function __construct( $name, $title, $description, $section_name, $default_value = NULL, $regular_expression = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, $description, $section_name, $default_value, $regular_expression, $validation_callback );
		$this->type = 'wysiwyg' ;
				
	}
		
	protected function check_name( $name ) {
		//begins with a letter and includes numbers letters and underscores only
		if (  preg_match( '/^[a-z]+$/', $name ) ) {
			return true;
		} else {
			throw new Exception ( 'Invalid name argument. The name of the wysiwyg option can only consist of lowercase letters.' );
		}
	}

	public function display_option() {
		if ( function_exists( 'wp_editor' ) ) { //wp 3.3 and above
			wp_editor( $this->value, $this->name, array( 'textarea_rows' => 10 ) );
			echo	"<span class='description'>
					{$this->description}
				</span>";
		} else {
		  	parent::display_option();
		}
	}
}