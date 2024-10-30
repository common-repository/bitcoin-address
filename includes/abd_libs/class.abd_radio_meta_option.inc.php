<?php
/**
 * Radio buttons meta option
 * 
 * Implement HTML radio buttons as a part of a meta box.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_radio_meta_option extends abd_single_choice_meta_option {
	
	public function __construct( $name, $title, $description, array $all_values, $default_value, $validation_callback = NULL ) {
		
		parent::__construct( $name, $title, 'radio', $description, $all_values, $default_value, $validation_callback );		  
	}
	
 	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		echo "<br /><br />" . $this->title;
		$count = 0;
 		foreach ( $this->all_values as $value => $title ) {
			$id = "{$this->name}_$count";
			echo "	<p><input name='{$this->name}' type='{$this->type}' id='$id' value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo "checked='yes'";		
			}
			echo" /> <label for='$id'>$title</label></p>";
			$count++;
		}
		echo "	<span class='description'>
				{$this->description}
			</span>
			";  
	}
}