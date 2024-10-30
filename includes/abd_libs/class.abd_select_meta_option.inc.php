<?php
/**
 * Select list meta option
 * 
 * Implement HTML select list as a part of a meta box.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_select_meta_option extends abd_single_choice_meta_option {
	
	public function __construct( $name, $title, $description, array $all_values, $default_value, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'select', $description, $all_values, $default_value, $validation_callback );		  
	}
	 	
	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		echo '<br /><br />';
		echo "<p><label for='{$this->name}'>{$this->title}</label> ";
		echo "<select name='{$this->name}'>";
		foreach ( $this->all_values as $value => $title ) {
			echo "<option value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo "selected='yes'";
			}
			echo ">$title</option>";  
		
		}
		echo "</select>";
		echo "<span class='description'>{$this->description}</span></p>";
	}
}