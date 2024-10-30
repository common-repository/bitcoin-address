<?php
/**
 * Inline radio buttons meta option
 * 
 * Implement HTML radio buttons that are displayed all on one line.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_inline_radio_meta_option extends abd_single_choice_meta_option {
	
	public function __construct( $name, $title, $description, array $all_values, $default_value, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'inline_radio', $description, $all_values, $default_value, $validation_callback );		  
	}
	
 	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		echo "<br /><br /><p>{$this->title}</p>";
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