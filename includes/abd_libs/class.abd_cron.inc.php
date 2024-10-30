<?php
class abd_cron extends abd_foundation {
    const SCHEDULE_NAME = "abd-%d-seconds";
    const HOOK_NAME   = "abd-%s-%d-seconds-hook";

    private $data;
    
    function __construct( $name, $file ) {
        parent::__construct( $name );
        
        $this->data = array();
        
        $this->hook( $file );
    }
  
    protected function check_name( $name ) {
        //begins with a letter and includes numbers, lowercase letters and hyphens only
        if ( preg_match( '/^[a-z-0-9]+$/', $name ) ) {
            return true;
        } else {
            throw new Exception ( 'Name parameter contains invalid characters. Name can contain only lower case letters, hyphens and numbers.' );
        }
    }
    
    private function hook( $file ) {
        register_activation_hook( $file, array( $this, 'add_hooks_to_intervals' ) );
        register_deactivation_hook( $file, array( $this, 'remove_hooks_from_intervals' ) );
        
        add_filter( 'cron_schedules', array( $this, '_add_intervals' ) );
        
        add_action( 'init', array( $this, '_add_functions_to_hooks' ) );
    }
    
    public function add_interval( $interval ) {
        if( ! is_numeric( $interval ) || $interval < 1 ) {
            throw new Exception( 'Interval needs to be number greater than or equal to one.' );
        }
        
        $interval = intval( $interval );
        
        if( ! isset( $this->data[ $interval ] ) ) {
            $this->data[ $interval ] = array();
        }
        return $this;
    }
    
    public function add_job( $interval, $function ) {
              
        if( !is_callable( $function ) ) {
            throw new Exception( 'Callback function is not callable' );
        }
        
        $interval = intval( $interval );
        
        $this->add_interval( $interval );
        
        if( ! in_array( $function, $this->data[ $interval ] ) ) {
            $this->data[ $interval ][] = $function;
        }
        
        return $this;
    }
    
    public function remove_job( $interval, $function ) {
        if( isset( $this->data[ $interval ] ) && in_array( $function, $this->data[ $interval ] ) ) {
            unset( $this->data[ $interval ][ $function ] );
            remove_action( $hook_name, $function );
       } else {
            trigger_error( 'Function is not scheduled', E_USER_NOTICE );
       }
       
       return $this;
    }
    
    public function add_hooks_to_intervals() {
        foreach( $this->data as $interval => $sub_array ) {
            $schedule_name = $this->get_schedule_name( $interval );
            $hook_name = $this->get_hook_name( $interval );
            wp_schedule_event( time(), $schedule_name, $hook_name );
        }
    }
    
    public function remove_hooks_from_intervals() {
        foreach( $this->data as $interval => $sub_array ) {
            $hook_name = $this->get_hook_name( $interval );
            wp_clear_scheduled_hook( $hook_name );
        }
    }
   
    public function _add_intervals( $schedules ) {
        foreach( $this->data as $interval => $sub_array ) {
            $schedule_name = $this->get_schedule_name( $interval );
            if( !isset( $schedules[ $schedule_name ] ) ) {
                $schedules[ $schedule_name ] = array( 
                                                        'interval' => $interval,
                                                        'display' => sprintf(__(  'Every %d seconds' ), $interval )
                                                    );
            }
        }
        return $schedules;
    }
    
    
    public function _add_functions_to_hooks() {
        foreach( $this->data as $interval => $sub_array ) {
            $hook_name = $this->get_hook_name( $interval );
            foreach( $sub_array as $function ) {
                add_action( $hook_name, $function );
            }
        }
    }    
    
    
    private function get_hook_name( $interval ) {
        return sprintf( self::HOOK_NAME, $this->name, $interval );
    }
    
    private function get_schedule_name( $interval ) {
        return sprintf( self::SCHEDULE_NAME, $interval );
    }
    
}
