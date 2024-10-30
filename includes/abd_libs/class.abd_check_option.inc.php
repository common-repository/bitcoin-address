<?php
/**
 * Check boxes option
 * 
 * Implement HTML check boxes options.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_check_option extends abd_multiple_choice_option {
	
	public function __construct( $name, $title, $description, $section_name, array $all_values, $default_values = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'checkbox', $description, $section_name, $all_values, $default_values, $validation_callback );		  
	}
	
	public function display_option() {
		$count = 0;
		foreach ( $this->all_values as $value => $title ) {
			$id = $this->name . "_$count";
			echo "	<p><input name='{$this->name}[]' type='{$this->type}' value='$value' id='$id'";
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