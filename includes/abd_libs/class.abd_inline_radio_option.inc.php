<?php
/**
 * Inline radio buttons option
 * 
 * Implement HTML radio buttons in such a way that they are displayed all on one line.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_inline_radio_option extends abd_single_choice_option {
	
	public function __construct( $name, $title, $description, $section_name, array $all_values, $default_value, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'inline_radio', $description, $section_name, $all_values, $default_value, $validation_callback );		  
	}
	
	public function display_option() {
		$count = 0;
		foreach ( $this->all_values as $value => $preview ) {
			$id = "{$this->name}_$count";
			echo "<label class='{$this->name}_label_class' for='$id'><input class='{$this->name}_class' name='{$this->name}' type='radio' id='$id' value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo "checked='yes'";		
			}
			echo " />$preview</label>";
			$count++;
		}
		echo "	<p class='description'>
				{$this->description}
			</p>
			";  
	}
}