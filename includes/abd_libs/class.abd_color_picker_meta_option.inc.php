<?php
/**
 * Color picker meta option
 * 
 * Implement farbtastic color picker as a meta box option.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_color_picker_meta_option extends abd_meta_option {
	public function __construct( $name, $title, $description, $default_value ) {
		parent::__construct( $name, $title, 'colorpicker', $description, $default_value );
		$this->hook();
	}
	
	public function hook() {
	  	$hooks = array( 'post-new.php', 'post.php' );
		foreach( $hooks as $hook ) {
	  		add_action( "admin_print_scripts-$hook", array( $this, 'add_js' ) );
			add_action( "admin_print_styles-$hook", array( $this, 'add_css' ) );
		}
	}

	public function add_js() {
		wp_enqueue_script( 'farbtastic' );
	}	
	
	public function add_css() {
	  	wp_enqueue_style( 'farbtastic' );
	}

	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		echo "	<script type='text/javascript'>
			<!--
			jQuery(document).ready(function() {
				jQuery('#{$this->name}_cp').farbtastic('#{$this->name}');
				jQuery('#{$this->name}_cp').hide();
				jQuery('#{$this->name}').focusin(function() {
					jQuery('#{$this->name}_cp').slideToggle();
				}
				);
				jQuery('#{$this->name}').focusout(function() {
					jQuery('#{$this->name}_cp').slideToggle();
				}
				);
			});
			//-->
			</script>";
		echo "<p><label for='{$this->name}'>$this->title</label>
			<input name='{$this->name}' type='text' id='{$this->name}' value='{$this->value}' />
			<span class='description'>
				{$this->description}
			</span>
			<div id='{$this->name}_cp'></div>
			</p>
			";
	}
	
	public function validate( $input ) {
		$in_clean = trim( $input );
		return preg_match( '/^#[0-9a-f]{3,6}$/i', $in_clean ); 
	}
}