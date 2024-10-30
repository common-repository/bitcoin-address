<?php
/**
 * Radio buttons option
 * 
 * Implement HTML radio buttons as a part of an admin page.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_radio_option extends abd_single_choice_option {
	
	public function __construct( $name, $title, $description, $section_name, array $all_values, $default_value, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'radio', $description, $section_name, $all_values, $default_value, $validation_callback );		  
	}
	
 	public function display_option() {
		$count = 0;
		foreach ( $this->all_values as $value => $title ) {
			$id = "{$this->name}_$count";
			echo "	<p><input name='{$this->name}' type='{$this->type}' id='$id' value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo " checked='yes' ";		
			}
			echo " /> <label for='$id'>$title</label></p>";
			$count++;
		}
		echo "	<p class='description'>
				{$this->description}
			</p>
			";  
	}
}