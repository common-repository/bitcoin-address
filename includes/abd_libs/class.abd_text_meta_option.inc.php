<?php
/**
 * Text box meta option
 * 
 * Implement HTML text box as a part of a meta box.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_text_meta_option extends abd_meta_option {
	private $_regular_expression;
	
	public function __construct( $name, $title, $description, $default_value = NULL, $regular_expression = NULL, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'text', $description, $default_value, $validation_callback );
		$this->_regular_expression = $regular_expression;
	}
	
	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		
		$val = esc_attr( $this->value );
		echo "<p><label for='{$this->name}'>{$this->title}</label>	<input id='{$this->name}' name='{$this->name}' type='{$this->type}' value='$val' />
			
				<span class='description'>
					{$this->description}
				</span>
			</p>
			";
	}
	
	public function validate( $input ) {
		if ( $this->_regular_expression != NULL ) {
			$in_clean = trim( $input );
			if ( preg_match( $this->_regular_expression, $in_clean ) ) {
				return true;
			} else {
			  	return false;
			}
			
		} elseif ( $this->validation_callback != NULL ) {
			if ( is_callable( $this->validation_callback ) ) {
				return call_user_func( $this->validation_callback, $input );
			} else {
			  	throw new Exception ( sprintf( 'Invalid callback function for %s . Function or class method does not exist.', $this->title ) );
			}
		} 
		return false;
	}
}