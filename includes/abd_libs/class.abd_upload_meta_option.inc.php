<?php
/**
 * Upload meta option
 * 
 * Shows a file upload field in the edit screen.
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_upload_meta_option extends abd_meta_option {
		
	public function __construct( $name, $title, $description, $validation_callback = NULL ) {
		parent::__construct( $name, $title, 'upload', $description, NULL, $validation_callback );
	}
	
	public function display_option( $post_id ) {
		parent::display_option( $post_id );

		echo "<p><label for='{$this->name}'>{$this->title}</label>	<input id='{$this->name}' name='{$this->name}' type='text' value='{$this->value}' />
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
						tb_show('', 'media-upload.php?type=image&amp;post_id='+ jQuery('#post_ID').val() +'&amp;TB_iframe=true');
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
				return call_user_func( $this->validation_callback, $in_clean, $this->value );
			} else {
			  	throw new Exception ( sprintf( 'Invalid callback function for %s . Function or class method does not exist.', $this->title ) );
			}
		} elseif ( $in_clean == '' ) {
		  	return true;
		} else {
			return abd_libs_url_validate( $in_clean );
		}
		return false;
	}
}