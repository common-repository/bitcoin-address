<?php
/**
 * Slider meta option
 * 
 * Implement jQuery slider option.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 */
class abd_slider_meta_option extends abd_numeric_text_meta_option {
	private $step;
	
	public function __construct( $name, $title, $description, $min_number, $max_number, $default_value, $whole_number = true, $step = 1 ) {
		parent::__construct( $name, $title, $description, $min_number, $max_number, $default_value, $whole_number );
		$this->step = $step;	
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
		wp_enqueue_script( 'jquery-ui-core-custom', plugins_url( 'js/jquery-ui-1.8.21.custom.min.js', __FILE__ ) );
	}
	
	public function add_css() {
	  	wp_enqueue_style( 'jquery-ui-core-custom', plugins_url( 'css/jquery-ui-1.8.21.custom.css', __FILE__ ) );
	}

	public function display_option( $post_id ) {
		$this->load_option( $post_id );
		$this->output_nonce();	  
	  	echo "	<p>
				<label for='{$this->name}_id'>{$this->title}</label>
				<div id='{$this->name}_slider'></div>
				<input name='{$this->name}' class='{$this->name}_class' id='{$this->name}_id' type='{$this->type}' value='{$this->value}' />
				<span class='description'>
					{$this->description}
				</span>
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