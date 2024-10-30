<?php
/**
 * Colour picker option
 * 
 * Implements farbtastic colour picker option
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_color_picker_option extends abd_option {
	protected $page_name;
	public function __construct( $name, $title, $description, $page_name, $section_name, $default_value ) {
		parent::__construct( $name, $title, 'colorpicker', $description, $section_name, $default_value );
		$this->page_name = $page_name;
		add_action( 'abd_libs_options_page_admin_init', array( $this, 'init' ), 10, 2 );
	}
	
	public function init( $page_name, $parent_page ) {
		if( $page_name == $this->page_name ) {
			$hook = get_plugin_page_hook( $page_name, $parent_page );
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
	
	public function display_option() {
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
			</script>
			<input name='{$this->name}' type='text' id='{$this->name}' value='{$this->value}' />
			<span class='description'>
				{$this->description}
			</span>
			<div id='{$this->name}_cp'></div>
			";
	}
	
	public function validate( $input ) {
		$in_clean = trim( $input );
		if ( preg_match( '/^#[0-9a-f]{3,6}$/i', $in_clean ) ) {
			$this->value = $in_clean;
		} else {
		  	$this->validation_failed_message();
		}
		return $this->value ;
	}
} 