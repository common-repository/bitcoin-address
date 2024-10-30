<?php
/**
 * Date picker meta option
 * 
 * Implement jQuery date picker as a meta box option.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_date_picker_meta_option extends abd_meta_option {

	public function __construct( $name, $title, $description, $default_value ) {
		if( $this->check_value( $default_value ) ) {
			parent::__construct( $name, $title, 'datepicker', $description, $default_value );
			$this->hook();
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
	  	$this->admin_message ( sprintf( __( 'Invalid value for option titled "%s". Date must be in Year-Month-Day format with 4 digits for the year and 2 each for the month and day.' , 'ABDLIBS_LANG' ), $this->title ) );
	}

	public function hook() {
	  	$hooks = array( 'post-new.php', 'post.php' );
		foreach( $hooks as $hook ) {
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

	public function display_option( $post_id ) {
		parent::display_option( $post_id );
		echo "	<script type='text/javascript'>
			<!--
			jQuery(document).ready(function() {
				jQuery('#{$this->name}').datepicker({dateFormat: 'yy-mm-dd' });
			});
			//-->
			</script>
			<p>
				<label for='{$this->name}'>$this->title</label>
				<input name='{$this->name}' type='text' id='{$this->name}' value='{$this->value}' />
				<span class='description'>
					{$this->description}
				</span>
			</p>
			";
	}
	
	public function validate( $input ) {
		$in_clean = trim( $input );
		return $this->check_value( $in_clean );
	}
}