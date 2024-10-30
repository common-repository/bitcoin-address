<?php
namespace Abdussamad\WordPress\Plugins;
/*
Plugin Name: Bitcoin Address
Description: Provides a way for your site's visitors to get a fresh bitcoin address each time they want to send you money.
Author: Abdussamad
Author URI: http://abdussamad.com
Version: 0.8.1
Text Domain: bitcoin-address
License: GPLv3
*/

/*      Copyright 2014  Abdussamad Abdurrazzaq

        This program is free software: you can redistribute it and/or modify
        it under the terms of the GNU General Public License version 3.0 as 
        published by the Free Software Foundation.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class BitcoinAddress {
	const VERSION = '0.8.1';
	const NAME = 'bitcoin_address';
	
	private $options_page;
	private $options;
	private $table_name ;
	private $version_option_name;
	private $list_table;
	private $include_path; 
	
	function __construct() {
		$this->set_variables();
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		$this->upgrade();
		$this->save_version();
		$this->include_files();
		$this->create_options();
		$this->create_log_page();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_shortcode( 'bitcoin_address', array( $this, 'handle_shortcode' ) );
		add_shortcode( 'btc_address', array( $this, 'handle_shortcode' ) );
		add_filter( 'query_vars', array( $this, 'register_qr_code_query_var' ) );
		add_action( 'template_redirect', array( $this, 'output_qr_code' ) );
		add_action( 'plugins_loaded', array( $this, 'load_language' ) );
		add_action( 'admin_notices', array( $this, 'post_install_message' ) );
	}
	
	private function set_variables() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . self::NAME . '_log';
		$this->version_option_name = self::NAME . '_version';
		$this->include_path = plugin_dir_path( __FILE__ ) . '/includes/';
	}	
	
	public function install() {
		$this->db_setup();
		$this->save_version();
		set_transient( self::NAME . '_activated', 1, 60 );
	}
	
	public function post_install_message() {
		$opt = self::NAME . '_activated';
		if( get_transient( $opt ) ) {
	    		?>
		<div class="update-nag">
		<p>
		<?php 
			printf( 
					__( 
						'Thank you for installing the Bitcoin Address plugin. Please enter your Electrum Master Public Key in the <a href="%s">plugin settings area</a>.' , 
						'bitcoin-address' 
					), 
					'options-general.php?page=' . self::NAME . '_options_page'
				) ?>
		</p>
    	</div>
		<?php
		delete_transient( $opt );
		}
	}
	
	private function upgrade() {
		if( get_option( $this->version_option_name ) != self::VERSION ) {
			$this->db_setup();
		}
	}
	
	private function db_setup() {
		global $wpdb;
		
		$charset_collate = '';
		
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}
		
		
		$sql = "CREATE TABLE $this->table_name (
				id int NOT NULL AUTO_INCREMENT,
				user_agent VARCHAR(255),
				ip_address VARCHAR(50),
				referrer VARCHAR(1024),
				request_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				address VARCHAR(35) NOT NULL,
				address_index int NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	private function save_version() {
		update_option( $this->version_option_name, self::VERSION );
	}
		
	private function include_files() {
		$files = array( 'libs/ElectrumHelper.php', 'abd_libs/abd_libs.php', 'wp_list_table.php', 'list_table_child.php', 'list_table.php' );
		foreach( $files as $file ) {
			include $this->include_path . $file;
		}
	}
	
	private function create_options() {
		if( !class_exists( 'abd_libs' ) ) {
			return ;
		}
		
		$options_page_description = sprintf( 
											__( 
												'Options for the Bitcoin Address plugin. If you need help please see the guide <a href="%s">here</a>' , 
												'bitcoin-address' 
											),
											'https://wordpress.org/plugins/bitcoin-address/'
									);
		
		$this->options_page = new \abd_options_page( 
													self::NAME . '_options_page', 
													__( 'Bitcoin Address' , 'bitcoin-address'), 
													$options_page_description,
													__( 'Bitcoin Address' , 'bitcoin-address')
												);
		$main_section = $this->options_page->add_section( 
															self::NAME . '_main_section', 
															__( 'Main' , 'bitcoin-address'),
															__( 'Main Options' , 'bitcoin-address')
															);
		$this->options[ 'txt_mpk' ] = $main_section->add_textarea_option( 
																		'txt_' . self::NAME . '_mpk',
																		__( 'Electrum MPK' , 'bitcoin-address'),
																		__( 'Electrum Master Public Key' , 'bitcoin-address'),
																		$this->default_mpk(),
																		null,													
																		array( $this, "validate_mpk" )
																		);
		$this->options[ 'txt_index' ] = $main_section->add_numeric_text_option( 
																				'txt_' . self::NAME . '_index',
																				__( 'Next Address Index' , 'bitcoin-address'),
																				__( 'Index of the next address that is displayed.' , 'bitcoin-address'),
																				0,
																				pow( 2, 32 ) / 2,
																				0,
																				true
																				);
	}
	
	
	public function validate_mpk( $input, $old_value ) {
		$ret = true;
		$custom_error = '';
		$version = $this->get_mpk_version( $input );
		if( $input == $this->default_mpk() ) {
			$custom_error = __( "Please enter your MPK not the default MPK", 'bitcoin-address' );
		} elseif( $version == 1 && !preg_match( '/^[a-f0-9]{128,128}$/', $input ) ) {
			$custom_error = __( "A version 1 MPK can only contain 128 hexadecimal digits. Please make sure you only enter hexadecimal numbers, i.e. lowercase letters a-f and numbers 0-9, and the total number of hexdigits is equal to 128.", 'bitcoin-address' );
		//valid base58 chars 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz
		} elseif( $version == 2 && !preg_match( "/^xpub[1-9A-HJ-NP-Za-km-z]+$/", $input ) ) {
			$custom_error = __( "A version 2 MPK must begin with 'xpub' and can only contain base58 digits - 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz.", 'bitcoin-address' );
		} else {
			try {
				ElectrumHelper::mpk_to_bc_address( $input, 0, $version );
			} catch( \ErrorException $e) {
				$custom_error = $e->getMessage() ;
			}
		}
		if( $custom_error ) {
			$ret = false;
			$this->options[ 'txt_mpk' ]->custom_error_message( $custom_error );
		}		
		return $ret;
	}
	
	private function get_mpk_version( $mpk ) {
		return $mpk[ 0 ] == 'x' ? 2 : 1 ;
	}
	
	private function create_log_page() {
		$this->list_table = new list_table( 
											self::NAME . '_log_page', 
											__( 'Bitcoin Address Log' , 'bitcoin-address'), 
											__( 'Log of addresses that were handed out' , 'bitcoin-address'),
											$this->table_name,
											array( 
													'request_date'  => __( 'Date' , 'bitcoin-address'),
													'ip_address'    => __( 'IP Address' , 'bitcoin-address'),
													'address'       => __( 'Address' , 'bitcoin-address'),
													'address_index' => __( 'Address Index' , 'bitcoin-address')
													),
											'address',
											'request_date',
											'DESC',
											10
										);
	}
	
	public function get_address() {
		global $wpdb;
		$ret = false;
		
		if( isset( $this->options[ 'txt_mpk' ] ) && $this->options[ 'txt_mpk' ]->value != $this->default_mpk() ) {
				$temp = '';
				try {
					$temp = ElectrumHelper::mpk_to_bc_address( $this->options[ 'txt_mpk' ]->value, $this->options[ 'txt_index']->value, $this->get_mpk_version(  $this->options[ 'txt_mpk' ]->value ) );
				} catch ( \ErrorException $e ) {
					trigger_error ( 'BitcoinAddress class failed to generate an address from the MPK. Error is ' . $e->getMessage() );
					return false;
				}
				if( strlen( $temp ) >= 26 ) {
					$useragent = strval( $_SERVER[ 'HTTP_USER_AGENT' ] ) ;
					$ip = strval( $_SERVER[ 'REMOTE_ADDR' ] );
					$referrer = strval( $_SERVER[ 'HTTP_REFERER' ] );
					$insert_success = $wpdb->insert( 
													$this->table_name, 
													array( 
															'user_agent' => $useragent,
															'ip_address' => $ip, 
															'referrer' => $referrer, 
															'request_date' => current_time( 'mysql' ),
															'address' => $temp,
															'address_index' =>  $this->options[ 'txt_index']->value
													) 
										);
					if( $insert_success ) {
						$ret = $temp;
						$this->options[ 'txt_index']->set_value( ++$this->options[ 'txt_index']->value );
					} else {
						trigger_error( 'BitcoinAddress class could not insert newly generated address into database', E_USER_WARNING );
					}
				}
		}
		return $ret;
	}
	
	public function handle_shortcode( $atts ) {
		static $count = 0;
		$allowed_types = array( 'box', 'field', 'link' );
		extract( shortcode_atts( 	array(
											'type' => 'field',
											'qr_code'   => 'disabled'
									), $atts 
							) 
		);
		
		if( !in_array( $type, $allowed_types ) ) {
			$type = $allowed_types[ 0 ];
		}
		
		$name = self::NAME;
		$id = $name . "_$count";
		
		$form_id = "frm_$id";
		$form_class = $form_name = "frm_$name";
		
		$button_class = $button_name = "cmd_$name";
	
		$count_name = "txt_count_$name";
		$type_name = "txt_type_$name";
		
		$display_qr_code = strtolower( $qr_code ) == 'enabled' ? 'yes' : 'no' ;
		$qr_code_name = "txt_qr_code_$name";
		
		$output_div_class = "output_$name";
		$output_div_id = "output_$id";
		
		$ret = '';
		
		$button_label = __( 'Get Address' , 'bitcoin-address');
		if( isset( $_POST[ $button_name ] ) && isset( $_POST[ $count_name ] ) && isset( $_POST[ $type_name ] ) ) {
			if( $_POST[ $count_name ] == $count ) {
				$ret = 	"<div class='$output_div_class' id='$output_div_id'>" . $this->process_form() . '</div>';
			}
		} else { 
			$nonce_field = wp_nonce_field( $name . '_nonce_submit_action_' . $count, $name . '_nonce_name_' . $count, true, false );
			$ret = "
					<form name='$form_name' id='$form_id' action='#$output_div_id' method='post' class='$form_class' data-count='$count' data-type='$type'>
						<input type='hidden' name='$count_name' value='$count' />
						<input type='hidden' name='$type_name' value='$type' />
						<input type='hidden' name='$qr_code_name' value='$display_qr_code' />
						$nonce_field
						<input type='submit' name='$button_name' value='$button_label' class='$button_class'/>
					</form>
					";
		}
		
		$count ++;
		return $ret;
	}
	
	
	private function process_form() {
		$ret = '';
		
		if( $this->permitted() ) {
			$name = self::NAME ;
			$type = $_POST[ "txt_type_$name" ];
			$count = $_POST[ "txt_count_$name" ];
			$display_qr_code = $_POST[ "txt_qr_code_$name" ] == 'yes';
			
			$id = $name . "_$count";
			
			$addr = $this->get_address();	
			
			$qr_code_class = $name . "_qr_code";
			$qr_code_id = $id . "_qr_code";
			$qr_code_link_class = $qr_code_class . '_link';
			$qr_code_link_id = $qr_code_id . '_link';
			$qr_code_html = '';
			$qr_code_url = get_site_url() . '/?';
			$qr_code_url .= http_build_query( 
												array( 
														'bitcoin_address' => $addr,
														'_wpnonce' => wp_create_nonce( self::NAME . '_' . $addr )
													),
												'',
												'&amp;'
											);
					
			$txt_class = $txt_name = $name . "_field";
			$txt_id = $id . "_field";
			
			$label_value = __( 'Bitcoin Address'  , 'bitcoin-address');
			$label_class = $name . "_field_label";
		
			$link_class = $name . "_link";
			$link_id = $id . "_link";
				
			if( $addr ) {
				if( $display_qr_code ) {
					$qr_code_html = "<a id='$qr_code_link_id' class='$qr_code_link_class' href='bitcoin:$addr' title='Bitcoin Address'>
								<img src='$qr_code_url' class='$qr_code_class' id='$qr_code_id' />
							</a>";
				}
				switch( $type ) {
					case 'link':
						$ret = "<label for='$link_id' class='$label_class'>$label_value</label>
								$qr_code_html
								<a id='$link_id' class='$link_class' href='bitcoin:$addr' title='Bitcoin Address'>$addr</a>";
						break;
					case 'field':
					case 'box':
					default:
						$this->enqueue_js();
						$ret = "<label for='$txt_id' class='$label_class'>$label_value</label>
								$qr_code_html
								<input name='$txt_name' id='$txt_id' class='$txt_class' type='text' value='$addr' />";
						break;
				}
			}
		} else {
			$ret = __( "Sorry, you don't have permission for this action. Go back to the last page, reload it and try again" , 'bitcoin-address');
		}
		
		return $ret;
	}
	
	//verify nonces.
	private function permitted() {
		$count_name = "txt_count_" . self::NAME;

		if( isset( $_POST[ $count_name ] ) ) {
			$form_count = $_POST[ $count_name ] ;
			if( is_numeric( $form_count ) && preg_match( '/^\d+$/', $form_count ) ) {
				if ( isset( $_POST[ self::NAME . '_nonce_name_' . $form_count ] ) ) {
	  				return	wp_verify_nonce( $_POST[ self::NAME . '_nonce_name_' . $form_count ], self::NAME . '_nonce_submit_action_' . $form_count );
				}
			}
		}
		return false;
	}
	
	public function register_qr_code_query_var( $query_arr ) {
	  	if ( ! array_key_exists( self::NAME, $query_arr ) ) {
		  	$query_arr[] = self::NAME;
	  	}
		return $query_arr;
	}	
	
	public function output_qr_code() {
	  	$addr = get_query_var( self::NAME );
		if ( $addr && preg_match( '/^1[1-9a-km-zA-HJ-NP-Z]{25,39}$/', $addr ) ) {
			$permitted = isset( $_GET[ '_wpnonce' ] ) && wp_verify_nonce( $_GET[ '_wpnonce' ], self::NAME . '_' . $addr );
			if( $permitted ) {
				include $this->include_path . 'phpqrcode.php';
				QRcode::png( $addr, false, QR_ECLEVEL_L, 10 );
				exit;
			}
		}
	}
	
	public function load_language() {
		load_plugin_textdomain( 'bitcoin-address', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	private function enqueue_js() {
		wp_enqueue_script( 'btc_addr', plugins_url( 'js/select_all.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}
	
	public function enqueue_css() {
		wp_enqueue_style( 'btc_addr', plugins_url( 'css/style.css', __FILE__ ), false, self::VERSION );
	}
	
	private function default_mpk() {
		return str_repeat( '0', 128 );
	}
}

$AbdBitcoinAddress = new BitcoinAddress();
