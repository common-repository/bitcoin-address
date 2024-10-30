<?php
namespace Abdussamad\WordPress\Plugins;

class list_table_child extends \Abdussamad\WordPress\Plugins\WP_List_Table {
	
	function __construct( $db_table_name, $column_map, $search_column, $default_sorted_column, $default_sorting_direction, $per_page, $views = array() ) {
		$this->db_table = $db_table_name;
		$this->column_map = $column_map;
		$this->search_column = $search_column;
		$this->default_sorted_column = $default_sorted_column;
		$this->default_sorting_direction = $default_sorting_direction;
		$this->per_page = $per_page;
		$this->views = $views;
		parent::__construct();
	}
		
	public function get_columns() {
		return $this->column_map;
	}
	
	public function get_sortable_columns() {
		$sortable_columns = array();
		foreach( $this->column_map as $db_column => $title ) {
			$sortable_columns[ $db_column ] = array( $db_column );
			$sortable_columns[ $db_column ][] = $this->default_sorting_direction == 'ASC';
		}
		return $sortable_columns;
	}
	
	public function prepare_items() {
		global $wpdb;
		$per_page = $this->per_page;
		$current_page = $this->get_pagenum() - 1;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$start = $current_page * $per_page;
		
		$search_arg = false;
		if( isset( $_GET[ 's' ] ) && !is_array( $_GET[ 's' ] ) && is_string( $_GET[ 's' ] ) ) {
			$search_arg = like_escape( esc_sql( $_GET[ 's' ] ) );
		}
		$search_sql = '';
		if( $search_arg !== false ) {
			$search_sql = " WHERE $this->search_column LIKE '%$search_arg%' ";
		}
		
		$total_items_query = "SELECT count(*) FROM $this->db_table $search_sql";
		$total_items = $wpdb->get_var( $total_items_query );
		
		$db_columns = array_keys( $this->column_map );
		
		$order_by = $this->default_sorted_column;
		if( isset( $_GET[ 'orderby' ] ) && in_array( $_GET[ 'orderby' ], $db_columns ) ) {
			$order_by = $_GET[ 'orderby' ];
		}
		$order = $this->default_sorting_direction;
		if( isset( $_GET[ 'order' ] ) && in_array(  $_GET[ 'order' ], array( 'asc', 'desc' ) ) ) {
			$order = $_GET[ 'order' ];
		}
		
		$columns_sql = implode( ',', $db_columns );
		
		$data_query = "SELECT $columns_sql FROM $this->db_table $search_sql ORDER BY $order_by $order LIMIT $start, $per_page";
		$data = $wpdb->get_results( $data_query, ARRAY_A );
		
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$this->set_pagination_args(
									array(
											'total_items' => $total_items,
											'per_page'    => $per_page
										) 
									);
		$this->items = $data;		
	}
	
	function column_default( $item, $column_name ) {
		if( array_key_exists( $column_name, $this->column_map ) ) {
			return wp_kses( $item[ $column_name ], array() );
		}
	}	
	
	public function get_views() {
		return $this->views;
	}
}
