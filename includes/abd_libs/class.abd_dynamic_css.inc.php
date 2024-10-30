<?php
/**
 * Dynamic stylesheet
 * 
 * Adds a dynamic stylesheet to Wordpress pages.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
**/
class abd_dynamic_css extends abd_foundation {
  	protected $name;
	protected $code;
	protected $options_page_name;
	protected $css_file;
	protected $enqueue_if_callback; //callback function that is checked before enqueuing CSS. CSS is only enqueued if callback returns TRUE
	
	function __construct( $name, $code, $options_page_name, $enqueue_if_callback = NULL) {
	  	parent::__construct( $name );
		$this->code = $code;
		$this->options_page_name = $options_page_name;
		$this->set_css_file();
		$this->enqueue_if_callback = $enqueue_if_callback;
		add_action( 'abd_libs_options_page_settings_updated', array( $this, 'update_css_file' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_filter( 'query_vars', array( $this, 'register_css_query_var' ) );
		add_action( 'template_redirect', array( $this, 'output_css' ) );
	}

	private function set_css_file() {
	  	$wp_up = wp_upload_dir();
		if( ! $wp_up[ 'error' ] ) {
			$this->css_file = array( 
							'uploads_dir' => $wp_up[ 'basedir' ],
							'dir'         => $wp_up[ 'basedir' ] . '/abd_libs_dynamic_css/',
							'file'        => $this->name . '.css',
							'url'         => $wp_up[ 'baseurl' ] . "/abd_libs_dynamic_css/{$this->name}.css",
						);
		} else {
		  	$this->css_file = false;
		}
	}	

	private function write_css_file() {
		if( ! is_writable( $this->css_file[ 'dir' ] ) ) {
			return;
		}
	  	$handle = fopen( $this->css_file[ 'dir' ] . $this->css_file[ 'file' ], 'w' );
		if( $handle ) {
		  	fwrite( $handle, $this->code );
		}
	}

	public function enqueue_css() {
		if( $this->enqueue_if_callback != NULL && is_callable( $this->enqueue_if_callback ) ) {
			if( ! call_user_func( $this->enqueue_if_callback ) ) { 
				return;
			}
		}
	  	if( $this->css_file && file_exists( $this->css_file[ 'dir' ] . $this->css_file[ 'file' ] ) ) {
			wp_enqueue_style( $this->name, $this->css_file[ 'url' ]  );
	  	} else {
		  	wp_enqueue_style( $this->name, site_url() . '/?' . $this->name . '=yes' );
	  	}
	}

	public function update_css_file( $page ) {
		if( $page == $this->options_page_name && $this->css_file ) {
			if( file_exists( $this->css_file[ 'dir' ] ) ) {
				$this->write_css_file();
			} else {
				if( file_exists( $this->css_file[ 'uploads_dir' ] ) && is_writable( $this->css_file[ 'uploads_dir' ] ) ) {
				  	mkdir( $this->css_file[ 'dir' ] );
					$this->write_css_file();
				}
			}
		} 
	}

	public function register_css_query_var( $query_arr ) {
	  	if ( ! array_key_exists( $this->name, $query_arr ) ) {
		  	$query_arr[] = $this->name;
	  	}
		return $query_arr;
	}
	
	public function output_css() {
	  	$check = get_query_var( $this->name );
		if ( $check == 'yes' ) {
			header('Content-type: text/css');
			echo $this->code;
			exit;
		}
	}
	
	public function set_code( $code ) {
	  	$this->code = $code;
	}
	
	public function get_code() {
	  	return $this->code;
	}
}