<?php
/**
 * File upload option
 * 
 * Implement a file upload field.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_upload_option extends abd_option {
	protected $page_name;
	public function __construct( $name, $title, $description, $page_name, $section_name, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'upload', $description, $section_name, NULL, $validation_callback );
		$this->page_name = $page_name;
		add_action( 'abd_libs_options_page_admin_init', array( $this, 'hook' ), 10, 2 );
	}
	
	public function hook( $page, $parent_page ) {
	  	if( $page == $this->page_name ) {
		  	$hook = get_plugin_page_hook( $page, $parent_page );
	  		add_action( "admin_print_scripts-$hook", array( $this, 'add_js' ) );
			add_action( "admin_print_scripts-$hook", array( $this, 'add_css' ) );
	  	}
	}

	public function add_js() {
	  	wp_enqueue_script( 'thickbox' );
	}

	public function add_css() {
	  	wp_enqueue_style( 'thickbox' );
	}

	public function display_option() {
		echo "<p><input id='{$this->name}' name='{$this->name}' type='text' value='{$this->value}' />
			<input type='button' id='btn_upload_$this->name' value='" . __( 'Upload', 'ABDLIBS_LANG' ) . "' />
				<span class='description'>
					{$this->description}
				</span>
			</p>
			<script type='text/javascript'>
				jQuery(document).ready(function() {
					var formfield;
					jQuery('#btn_upload_$this->name').click(function() {
						formfield = jQuery('#{$this->name}').attr('name');
						tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
 						return false;
						}
					);
					window.original_send_to_editor = window.send_to_editor;
    					window.send_to_editor = function(html) {
						if (formfield) {
							imgurl = jQuery(html).attr('href');
        						jQuery('#{$this->name}').val(imgurl);
							tb_remove();
       							formfield = '';
						} else {
							window.original_send_to_editor(html);
						}
					};
					}
				);
			</script>
			";
	}
	
	public function validate( $input ) {
		$in_clean = trim( $input );
		if ( $this->validation_callback != NULL ) {
			if ( is_callable( $this->validation_callback ) ) {
				if( call_user_func( $this->validation_callback, $in_clean, $this->value ) ){
				  	$this->value = $in_clean;
				} else {
			  		$this->validation_failed_message();
				}
			} else {
			  	throw new Exception ( sprintf( 'Invalid callback function for %s . Function or class method does not exist.', $this->title ) );
			}
		} elseif (  $in_clean == '' ) {
		  	$this->value = $in_clean;
		} elseif( abd_libs_url_validate( $in_clean ) ) {
			$this->value = $in_clean;
		} else {
		  	$this->validation_failed_message();
		}
		return $this->value ;
	}
}