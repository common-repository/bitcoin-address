<?php
/**
 * Textarea meta option
 * 
 * Implement HTML textarea as a part of a meta box.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_textarea_meta_option extends abd_text_meta_option {
	
	public function __construct( $name, $title, $description, $default_value = NULL, $regular_expression = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, $description, $default_value, $regular_expression, $validation_callback );
		$this->type = 'textarea' ;
				
	}
		
	public function display_option( $post_id ) {
		$this->load_option( $post_id );
		$this->output_nonce();
		$val = esc_textarea( $this->value );
		
		echo "<br /><span class='description'>
					{$this->description}
				</span><br /><label for='{$this->name}'>{$this->title}</label><br />
		<textarea rows='6' cols='75' name='{$this->name}' id='{$this->name}'>$val</textarea>
			";
	}
}