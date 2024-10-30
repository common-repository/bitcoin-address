<?php
/**
 * Slider option
 * 
 * Implement jQuery slider.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_slider_option extends abd_numeric_text_option {
	protected $step;
	protected $page_name;
	public function __construct( $name, $title, $description, $page_name, $section_name, $min_number, $max_number, $default_value, $whole_number = true, $step = 1 ) {
		parent::__construct( $name, $title, $description, $section_name, $min_number, $max_number, $default_value, $whole_number );
		$this->page_name = $page_name;
		$this->step = $step;
		add_action( 'abd_libs_options_page_admin_init', array( $this, 'enqueue_init' ), 10, 2 );
	}
		
	public function enqueue_init( $page_name, $parent_page ) {
		if ( $page_name == $this->page_name ) {
			$hook = get_plugin_page_hook( $page_name, $parent_page );
	  		add_action( "admin_print_scripts-$hook", array( $this, 'enqueue_js_and_css' ) );
		}
	}
	
	public function enqueue_js_and_css() {
		wp_enqueue_script( 'jquery-ui-core-custom', plugins_url( 'js/jquery-ui-1.8.21.custom.min.js', __FILE__ ) );
		wp_enqueue_style( 'jquery-ui-core-custom', plugins_url( 'css/jquery-ui-1.8.21.custom.css', __FILE__ ) );
	}

	public function display_option() {
		
	  	echo "	<div id='{$this->name}_slider'></div><input name='{$this->name}' class='{$this->name}_class' id='{$this->name}_id' type='{$this->type}' value='{$this->value}' />
			<p class='description'>
				{$this->description}
			</p>
			<script type='text/javascript'>
			jQuery( '#{$this->name}_slider' ).slider({
				value: {$this->value},
				min: {$this->min_number},
				max: {$this->max_number},
				step: {$this->step},
				slide: function( event, ui ) {
					jQuery( '#{$this->name}_id' ).val( ui.value );
				}
			});
			</script>
			";
	}

}