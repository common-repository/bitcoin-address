<?php
namespace Abdussamad\WordPress\Plugins;

class list_table extends \abd_ground_floor {
	
	function __construct( $name, $title, $description, $db_table_name, $column_map,  $search_column = NULL, $default_sorted_column = NULL, $default_sorting_direction = 'ASC', $per_page = 5, $access_rights = 'manage_options', $parent_page = 'options-general.php' ) {
		parent::__construct( $name, $title, $description );
		
		if( ! is_array( $column_map ) ) {
			throw new \Exception( 'Column map must be an array' );
		} else {
			$this->column_map = $column_map;
		}
		
		$this->set_search_column( $search_column );
		$this->set_default_sorted_column( $default_sorted_column );
		
		if( ! in_array( $default_sorting_direction, array( 'ASC', 'DESC' ) ) ) {
			throw new \Exception( "default_sorting_direction be one of 'ASC' or 'DESC'" );
		}
		$this->default_sorting_direction = $default_sorting_direction;
			
		$this->db_table = $db_table_name;
		$this->access_rights = $access_rights;
		$this->parent_page = $parent_page;
		
		if( is_numeric( $per_page ) ) {
			$this->per_page = intval( $per_page );
		} else {
			throw new \Exception( 'per_page is not a number' );
		}
			
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
	}
	
	public function set_search_column( $name ) {
		$this->search_column = $this->check_column_args( $name, 'search_column' );
	}
	
	public function set_default_sorted_column( $name ) {
		$this->default_sorted_column = $this->check_column_args( $name, 'default_sorted_column' );
	}
	
	private function check_column_args( $arg, $name ) {
		$ret = $arg;
		if( $arg == null ) {
			$column_map_keys = array_keys( $this->column_map );
			$ret = $column_map_keys[ 0 ];
		} elseif( !array_key_exists( $arg, $this->column_map ) ) {
			throw new \Exception( "$name arg must be a key in column_map argument" );
		} 
		return $ret;
	}
	
	public function create_admin_page() {
		add_submenu_page( $this->parent_page, $this->title, $this->title, $this->access_rights, $this->name, array( $this,'display_page' ) );
	}
	
	public function display_page() {
		$url = admin_url( "$this->parent_page?page=$this->name" );
		$this->list_table_child = new list_table_child( 
														$this->db_table, 
														$this->column_map, 
														$this->search_column,
														$this->default_sorted_column, 
														$this->default_sorting_direction,
														$this->per_page,
														array( 
																$this->name . '_all_view' => "<a href='$url'>" . __( 'All', 'bitcoin-address' ) . '</a>'
															)
													);
		echo "<div class='wrap'><h2>$this->title</h2>"; 
		echo "<p class='description'>$this->description</p>";
		
		$this->list_table_child->prepare_items(); 
		$this->list_table_child->views();
		
		echo "<form method='get'>
				<input type='hidden' name='page' value='$this->name' />";
		$this->list_table_child->search_box( __( 'Search', 'bitcoin-address' ), $this->name . '_search' ); 
		echo "</form>";
		
		$this->list_table_child->display(); 
		echo '</div>'; 
	}
}
