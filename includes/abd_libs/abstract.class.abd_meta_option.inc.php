<?php
/** 
 * Custom Meta options class
 * 
 * Provides the foundation for custom meta options
 *
 * @package abd_libs
 * @subpackage abd_meta_option
 * @author Abdussamad Abdurrazzaq
 * 
 */
abstract class abd_meta_option extends abd_ground_floor {
	protected $type; //type of option. input, select etc.
	protected $default_value;
	protected $validation_callback;
	protected $value;
	protected $errors;
	
	function __construct( $name, $title, $type, $description, $default_value = NULL, $validation_callback= NULL ) {
		parent::__construct( $name, $title, $description );
		
		$this->type = $type;
		$this->default_value = $default_value;
		$this->validation_callback = $validation_callback;
		$this->errors = $this->load_errors();
		
		add_action( 'save_post', array( $this, 'save_option' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}
	
	// load saved error messages from database. This needs to be done because of wordpress' post, redirect, get
 	// system of form submission
	protected function load_errors () {
	  	$errors = get_option( $this->name . '_errors' );
		if ( $errors ) {
		  	return $errors;
		} else {
		  	return array( 'error' => array(), 'update' => array() );
		}
	}
	
	protected function is_empty_string_saved( $post_id ) {
	  	//get_post_meta returns empty string if no value exists AND also if the value is a 
		//user set empty string. So we have to diffrentiate between no value and user set 
		//empty string.
		$check = get_post_meta( $post_id, $this->name . '_empty_string', true );
		if ( $check == 'yes' ) {
			return true;
		} else {
		  	return false;
		}
	}
	
	//load option from database. Returns saved value or default value if no value is saved.
	protected function load_option( $post_id ) {
		$val = get_post_meta( $post_id, $this->name, true );
		$valid = false;
		if ( $val == '' ) { 
			//is it a user saved  empty string?
			$valid = $this->is_empty_string_saved( $post_id );
			
		} else {
			$valid = true;
		}
		
		if ( $valid ) {
		  	$this->value = $val;
		} else {
		  	$this->value = $this->default_value;
		}

	}
	
	protected function admin_message( $message, $type = 'error' ) {
		$this->errors[ $type ][] = $message;
		update_option( $this->name . '_errors' , $this->errors );
	}
	
	// Does the user have permission to be saving meta options?
	protected function permitted( $post_id ) {
	  	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      			return false;
		}
  		
		//don't save on post revisions. Prevents duplicate error messages too.
		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}
		
		if ( ! isset( $_POST[ $this->name . '_nonce_name'] ) ) {
			return false;			
		} else {
  			if ( !	wp_verify_nonce( $_POST[ $this->name . '_nonce_name'], $this->name . '_nonce_save_action' ) ) {
      				return false;
			}
		}
  		
		// Check permissions
  		if ( 'page' == $_POST['post_type'] ) 
  		{
    			if ( ! current_user_can( 'edit_page', $post_id ) ) {
	       			return false;
			}
  		}
  		else
  		{
    			if ( ! current_user_can( 'edit_post', $post_id ) ) {
        			return false;
			}
  		}
		
		return true;
	}
	
	protected function validation_failed_message() {
	  	$this->admin_message ( sprintf( __( 'Invalid value for option titled "%s".' , 'ABDLIBS_LANG'), $this->title ) );
	}

	protected function output_nonce() {
	  	wp_nonce_field( $this->name . '_nonce_save_action', $this->name . '_nonce_name' );
	}
	
	public function save_option( $post_id ) {
		$this->load_option( $post_id ); 
		
		if ( $this->permitted( $post_id ) ) {
			if ( array_key_exists( $this->name, $_POST ) )	{
				$val = is_array( $_POST[ $this->name ] ) ? $_POST[ $this->name ] : trim( $_POST[ $this->name ] );			  
			} else {
			  	$val = '';
			}

			if ( $this->validate( $val ) ) {
				if ( $val == '' ) { //valid value is also an empty string so we have to set a flag
				  	update_post_meta( $post_id, $this->name . '_empty_string', 'yes' );
				
			  	} else {
				  	update_post_meta( $post_id, $this->name . '_empty_string', 'no' );
					
			  	}
				
				update_post_meta( $post_id, $this->name, $val );
				$this->value = $val;
			} else {
			  	$this->validation_failed_message();
			}
		} 
	}
	
	public function display_option( $post_id ) {
		$this->load_option( $post_id );
		$this->output_nonce();	  
	}

	abstract protected function validate( $input );
	
	//display error messages and delete from db.
	public function admin_notice() {
		foreach ( $this->errors as $type => $message_array ) {
			foreach ( $message_array as $message ) {
				echo "	<div class='$type'>
       						<p>$message</p>
    					</div>";
			}
		}
		delete_option( $this->name . '_errors' );
	}

	public function get_value() {
		global $post;
		if( is_object( $post ) ) {
	  		$this->load_option ( $post->ID );
		}
		return $this->value;
	}
}