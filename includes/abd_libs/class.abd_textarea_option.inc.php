<?php
/**
 * Textarea  option
 * 
 * Implement HTML textarea as a part of an admin page.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_textarea_option extends abd_text_option {
	
	public function __construct( $name, $title, $description, $section_name, $default_value = NULL, $regular_expression = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, $description, $section_name, $default_value, $regular_expression, $validation_callback );
		$this->type = 'textarea';	
	}
	
	public function display_option() {
		$val = esc_textarea( $this->value );
		echo "	<textarea name='{$this->name}' id='{$this->name}' rows='5' cols='80'>$val</textarea>
			<br />
			<span class='description'>
				{$this->description}
			</span>
			";
	}
}