<?php
/**
 * Creates an options page
 *
 * Registers an options page together with an entry in the wordpress admin area menu.
 * Groups sections and options.
 *
 * @package abd_libs
 * @author Abdussamad Abdurrazzaq
 *
 */
class abd_options_page extends abd_ground_floor {
	protected $menu_title;
	protected $access_rights;
	protected $parent_page;
	protected $help_tabs; //array of abd_help_tab objects
	protected $stylesheet;
	protected $sections; //array of section objects

	function __construct( $name, $title, $description, $menu_title, $access_rights = 'manage_options', $parent_page = 'options-general.php' ) {
		parent::__construct( $name, $title, $description );
		$this->menu_title    = $menu_title;
		$this->access_rights = $access_rights;
		$this->parent_page   = $parent_page;
		$this->sections = array();

		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
		$this->help_tabs = array();
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}
	
	private function output_nonce( $section_name ) {
	  	if( array_key_exists( $section_name, $this->sections ) ) {
		  	settings_fields( $section_name );
	  	} else {
		  	return false;
	  	}
	}
	
	private	function display_sections_tabs() {
			if( isset( $_GET[ 'section' ] ) ) {
				$current_tab = $_GET[ 'section' ];
			} else {
				$section_keys = array_keys( $this->sections ) ;
				$current_tab = array_shift( $section_keys );
			}
    		screen_icon();
		?>
		<h2 class="nav-tab-wrapper">
			<?php
    			foreach ( $this->sections as $tab_key => $section_obj ) {
        			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
        			?>
					<a class="nav-tab <?php echo $active ?>" href="?page=<?php echo $this->name ?>&section=<?php echo $tab_key ?>"><?php echo $section_obj->get_title() ?></a>
				<?php
    			}
    		?>
		</h2>
		<?php
	}

	//allow html characters in page title and description
	protected function check_title_desc( $desc ) {
	  	return true;
	}
		
	public function admin_init() {
		$hook = get_plugin_page_hook( $this->name, $this->parent_page );
		add_action( "admin_print_styles-$hook", array( $this, 'add_admin_css' ) );
		add_action( "admin_print_scripts-$hook", array( $this, 'settings_updated_hook' ) );
		do_action( 'abd_libs_options_page_admin_init', $this->name, $this->parent_page );
	}
	
	public function settings_updated_hook() {
	  	if( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] ) {
			do_action( 'abd_libs_options_page_settings_updated', $this->name );	  
	  	}
	}

	public function add_admin_css() {
	  	if ( isset( $this->stylesheet ) ) {
		  	wp_enqueue_style( $this->name . '_admin_css', $this->stylesheet );
	  	}
	}

	public function add_style( $stylesheet ) {
		if ( abd_libs_url_validate( $stylesheet ) ) {
	  		$this->stylesheet = $stylesheet;
		} else {
			trigger_error( "abd_libs_options_page: Stylesheet URL for admin page $this->name does not validate", E_USER_WARNING );
		}
	}

	public function create_admin_page() {
		add_submenu_page( $this->parent_page, $this->title, $this->menu_title, $this->access_rights, $this->name, array( $this,'display_page' ) );
	}
		
	public function add_section( $name, $title, $description ) {
		return $this->sections[ $name ] = new abd_section( $name, $title, $description, $this->name ) ;;
	}
	
	public function get_option( $option_name ) {
		$option = false;
		foreach( $this->sections as $section ) {
			$option = $section->get_option( $option_name );
			if ( $option !== false ) { //option found.
				break;
			}
		}
		return $option;
	}	

	public function display_page() {
		if( isset( $_GET[ 'section'] ) && array_key_exists( $_GET['section'], $this->sections ) ) {
		  	$section = $_GET[ 'section'] ;
		} else {
			$section_keys = array_keys( $this->sections ) ;
			$section = array_shift( $section_keys );
		}
		?>
			<div class='wrap'>
				<h2>
					<?php echo $this->title ?>
				</h2>
				<p>
					<?php echo $this->description ?>
				</p>
				<?php $this->display_sections_tabs() ?>
				<form action='options.php' method='post'>
					<?php 	$this->output_nonce( $section );
						do_settings_sections( $section);
					?>
					<br />
					<input name="Submit" type="submit" class="button-primary" value="<?php _e( 'Save Changes' )?>" />
				</form>
			</div>
		<?php
	}

	public function get_section( $section_name ) {
	  	if ( array_key_exists( $section_name, $this->sections ) ) {
			return $this->sections[ $section_name ];
		} else {
		  	return false;
		}
	}

	public function add_help_tab ( $name, $title, $content, $callback = NULL ) {
	  	$this->help_tabs [ $name ] = new abd_help_tab( $this->name, $name, $title, $content, $callback );
	}

	public function get_parent_page() {
	  	return $this->parent_page;
	}
}