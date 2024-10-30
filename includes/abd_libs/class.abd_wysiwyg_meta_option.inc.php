<?php
/**
 * WYSIWYG Textarea meta option
 * 
 * Implements a WYSIWYG textarea as a part of a meta box using the new wp_editor function in wordpress 3.3+
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_wysiwyg_meta_option extends abd_textarea_meta_option {
	
	public function __construct( $name, $title, $description, $default_value = NULL, $regular_expression = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, $description, $default_value, $regular_expression, $validation_callback );
		$this->type = 'wysiwyg' ;
				
	}
		
	protected function check_name( $name ) {
		//begins with a letter and includes numbers letters and underscores only
		if (  preg_match( '/^[a-z]+$/', $name ) ) {
			return true;
		} else {
			throw new Exception ( 'Invalid name argument. The name of the wysiwyg meta option can only consist of lowercase letters.' );
		}
	}

	public function display_option( $post_id ) {
		if ( function_exists( 'wp_editor' ) ) { //wp 3.3 and above
			$this->load_option( $post_id );
			$this->output_nonce();
			wp_editor( $this->value, $this->name, array( 'textarea_rows' => 10 ) );
		
		} else {
		  	parent::display_option( $post_id );
		}
	}
}