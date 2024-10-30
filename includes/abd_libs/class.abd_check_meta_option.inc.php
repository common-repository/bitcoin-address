<?php
/**
 * Check boxes meta option
 * 
 * Implements check boxes meta options.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_check_meta_option extends abd_multiple_choice_meta_option {
	
	public function __construct( $name, $title, $description, array $all_values, $default_values = NULL, $validation_callback = NULL ) {
		
		parent::__construct( $name, $title, 'checkbox', $description, $all_values, $default_values, $validation_callback );		  
	}
	
	
	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		
		echo "<br /><br />{$this->title} <br />";
		foreach ( $this->all_values as $value => $title ) {
			echo "	<p><input name='{$this->name}[]' type='{$this->type}' value='$value'";
			if ( $this->is_selected( $value ) ) {
				echo "checked='yes'";		
			}
			echo "> $title</input></p>";
		}
		echo "	<span class='description'>
				{$this->description}
			</span>
			";  
	}
	
}