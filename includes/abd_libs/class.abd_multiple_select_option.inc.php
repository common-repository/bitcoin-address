<?php
/**
 * Multiple select option
 * 
 * Implement HTML select list with the option of making multiple selections.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_multiple_select_option extends abd_multiple_choice_option {
	
	public function __construct( $name, $title, $description, $section_name, array $all_values, $default_values = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'multiple_select', $description, $section_name, $all_values, $default_values, $validation_callback );		  
	}

	public function display_option() {
		echo "<select name='{$this->name}[]' multiple='yes' id='{$this->name}_id' class='$this->type {$this->name}_class'>"; 
		foreach ( $this->all_values as $value => $title ) {
			echo "<option value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo " selected='yes'";		
			}
			echo"> $title</option>";
			
		}

		echo "</select>
			<p class='description'>
				{$this->description}
			</p>
			";  
	}
}