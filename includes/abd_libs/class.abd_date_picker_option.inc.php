<?php
/**
 * Date picker option
 * 
 * Implements jQuery date picker option
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_date_picker_option extends abd_option {
	protected $page_name;
	public function __construct( $name, $title, $description, $page_name, $section_name, $default_value ) {
		if( $this->check_value( $default_value ) ) {
			parent::__construct( $name, $title, 'datepicker', $description, $section_name, $default_value );
			$this->page_name = $page_name;
			add_action( 'abd_libs_options_page_admin_init', array( $this, 'init' ), 10, 2 );
		} else {
		  	throw new Exception ( sprintf( 'Invalid default date for date picker option titled "%s". Date must be in Year-Month-Day format with 4 digits for the year and 2 each for the month and day.', $title ) ) ;
		}
	}
	
	private function check_value( $val ) {
		if( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/i', $val ) ) {
		  	$parts = explode( '-', $val ) ;
			if( checkdate( $parts[ 1 ], $parts[ 2 ], $parts[ 0 ] ) ) {
				return true;
			} 
	  	} 
		return false;
	}

	protected function validation_failed_message() {
		$this->admin_message ( sprintf( __( 'Invalid value for option titled "%s". Date must be in Year-Month-Day format with 4 digits for the year and 2 each for the month and day.' , 'ABDLIBS_LANG'), $this->title ) . ' ' . $this->custom_error_message );
	}

	public function init( $page_name, $parent_page ) {
		if( $page_name == $this->page_name ) {
			$hook = get_plugin_page_hook( $page_name, $parent_page );
	  		add_action( "admin_print_scripts-$hook", array( $this, 'add_js' ) );
			add_action( "admin_print_styles-$hook", array( $this, 'add_css' ) );
		}
	}

	public function add_js() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}	
	
	public function add_css() {
		wp_enqueue_style( 'jquery-ui-core', plugins_url( 'css/jquery-ui-1.8.21.custom.css', __FILE__ ) );
	}
	
	public function display_option() {
		echo "	<script type='text/javascript'>
			<!--
			jQuery(document).ready(function() {
				jQuery('#{$this->name}').datepicker({dateFormat: 'yy-mm-dd' });
			});
			//-->
			</script>
			<input name='{$this->name}' type='text' id='{$this->name}' value='{$this->value}' />
			<span class='description'>
				{$this->description}
			</span>
			";
	}
	
	public function validate( $input ) {
		$in_clean = trim( $input );
		if ( $this->check_value( $in_clean ) ) {
			$this->value = $in_clean;
		} else {
		  	$this->validation_failed_message();
		}
		return $this->value ;
	}
} 