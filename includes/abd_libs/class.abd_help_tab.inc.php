<?php
/**
 * Admin help tab class
 *
 * Adds help to the admin options page
 *
 * @package abd_libss
 * @author Abdussamad Abdurrazzaq
 *
 **/
class abd_help_tab extends abd_foundation {
  	private $page_name;
	private $content;
	private $title;
	private $callback;
		
	function __construct( $page_name, $name, $title, $content, $callback= NULL ) {
	  	parent::__construct( $name );
		$this->title       = $title;
		$this->page_name   = $page_name;
		$this->content     = $content;
		$this->callback    = $callback;
		add_action( 'abd_libs_options_page_admin_init', array( $this, 'init' ), 10, 2 ); 
	}
		
	public function init( $page_name, $parent_page ) {
		if( $page_name == $this->page_name ) {
			$hook = get_plugin_page_hook( $page_name, $parent_page );
	  		add_action( "load-$hook", array( $this, 'display' ) );
		}
	}

	public function display() {
	  	$scr = get_current_screen() ;
		$tab_arr = array( 
			'id'       => $this->name,
			'title'    => $this->title,
		);
		if ( $this->callback != NULL ) {
			if ( is_callable( $this->callback ) ) {
				$tab_arr[ 'callback' ] = $this->callback;
			} else {
			  	throw new Exception ( sprintf( "Invalid content callback for help tab titled '%s'", $this->title ) );
			}

		} else {
		  	$tab_arr[ 'content' ]  = $this->content;
		}
		if ( method_exists( $scr, 'add_help_tab' ) ) { //help tab only after wp v3.3
			$scr->add_help_tab( $tab_arr );
		}
	}

	public function __get ( $var ) {
	  	if ( method_exists( $this, 'get_' . $var ) ) {
		  	return call_user_func( array( $this,  'get' . $var ) );
	  	} elseif ( property_exists( $this, $var ) ) {
			  	return $this->$variable;
		}
		
		throw new Exception( 'Class property does not exist' );
	}
	
	public function set_content ( $content ) {
	  	$this->content = $content;
	}
	
}