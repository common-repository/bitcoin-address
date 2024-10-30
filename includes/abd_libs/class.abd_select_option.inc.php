<?php
/**
 * Select list option
 * 
 * Implement HTML select list as a part of an admin page
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 * 
 */
class abd_select_option extends abd_single_choice_option {
	
	public function __construct( $name, $title, $description, $section_name, array $all_values, $default_value, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'select', $description, $section_name, $all_values, $default_value, $validation_callback );		  
	}
 	
	public function display_option() {
		echo "<select name='{$this->name}'>";
		foreach ( $this->all_values as $value => $title ) {
			echo "<option value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo "selected='yes'";
			}
			echo ">$title</option>";  
		
		}
		echo "</select>";
		echo "<p class='description'>{$this->description}</p>";
	}
}