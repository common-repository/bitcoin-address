<?php
/**
 * Multiple select meta option
 * 
 * Implement HTML select list with the option of making multiple selections.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_multiple_select_meta_option extends abd_multiple_choice_meta_option {
	
	public function __construct( $name, $title, $description, array $all_values, $default_values = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'multiple_select', $description, $all_values, $default_values, $validation_callback );		  
	}
	
	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		echo "<select name='{$this->name}[]' multiple='yes'>"; 
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