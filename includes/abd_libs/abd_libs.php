<?php
/**
 * abd_libs auto loader
 * 
 * - Registers auto loader for abd_libs classes
 * - Loads language files. 
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 * 
*/

if( ! class_exists( 'abd_libs' ) ) {
  class abd_libs {
	  const VERSION = '1.3.9';
	  function __construct() {
		  load_plugin_textdomain( 'ABDLIBS_LANG', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		  spl_autoload_register( array( $this, 'autoload' ) );
		  $this->save_version();
	  }

	  private function save_version() {
		  update_option( 'abd_libs_version', self::VERSION );
	  }

	  private function autoload( $obj_name ) {
		  $dir =  plugin_dir_path( __FILE__ ) ;
		  //only deal with classes with the correct prefix. Disallow problematic characters like periods.
		  if ( preg_match( '/^abd_[_a-z0-9]+$/', $obj_name ) ) {
			  $class_name = "class.$obj_name.inc.php";
			  $abstract_class_name = "abstract.$class_name";
			  if ( file_exists( $dir . $class_name ) ) {
				  include( $dir . $class_name );
			  } elseif ( file_exists( $dir . $abstract_class_name ) ) {
				  include( $dir . $abstract_class_name );
			  } 
		  }	
	  }
  }

  new abd_libs();
  
  function abd_libs_url_validate( $url ) {
		if( empty( $url ) ) {
			return false;
		}

		if( version_compare( PHP_VERSION, '5.3.3' ) < 0 ) {
			$oldhost = parse_url( $url, PHP_URL_HOST );
			if( $oldhost && strpos( $oldhost, '-' ) !== false  ) {
				$old_host_position = strpos( $url, $oldhost );
				$newhost = str_replace( '-', '0', $oldhost );
				$url = substr_replace( $url, $newhost, $old_host_position, strlen( $oldhost ) );
			}
			
		}
		return filter_var( $url,  FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED );
  }
}
