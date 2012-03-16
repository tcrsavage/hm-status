<?php 

/**
 * HMH_User class.
 */
class HMH_User {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @param bool $employment_start (default: false)
	 * @param bool $holidays_offset (default: false)
	 * @param bool $holidays_per_year (default: false)
	 * @return void
	 */
	function __construct( $id, $employment_start = false, $holidays_offset = false, $holidays_per_year = false ){
	
		$this->ID = $id;
		
		if ( $employment_start != false || $this->get_employment_start() === false )
			$this->set_employment_start( $employment_start );
			
		if ( $holidays_offset != false || $this->get_holidays_offset() === false )	
			$this->set_holidays_offset( $holidays_offset );
			
		if ( $holidays_per_year != false || $this->get_holidays_per_year() === false )
			$this->set_holidays_per_year( $holidays_per_year );	

		try{
			
			$this->grab_userdata();
			
		}catch ( Exception $e ){
			
			echo ( $e );
			exit;
		}
	}
	
	/**
	 * grab_userdata function.
	 * 
	 * @access public
	 * @return array
	 */
	function grab_userdata() {
	
		$data = get_userdata( $this->ID );
		
		if ( is_wp_error( $data ) || ! $data )
			throw new Exception ( 'Error finding user' );
		
		$this->data = $data->data;
			
		return $data->data;	
	}			

	/**
	 * set_employment_start function.
	 * 
	 * @access public
	 * @param int $date (default: 0)
	 * @return void
	 */
	function set_employment_start( $date = 0 ) {

		if ( $date === false ) {
			
			update_user_meta( $this->ID, 'hmh_employment_start', time() );
			return;
		}
	
		if ( ! $date = strtotime( $date ) )
			throw new Exception ( 'Date set error, please use standard format, i.e. 2011-02-20' ); 
	
		update_user_meta( $this->ID, 'hmh_employment_start', $date );
	}
	
	/**
	 * get_employment_start function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_employment_start() {
		
		return $this->employment_start = (int) get_user_meta( $this->ID, 'hmh_employment_start', true );
	}
	
	/**
	 * set_holidays_offset function.
	 * 
	 * @access public
	 * @param int $offset (default: 0)
	 * @return void
	 */
	function set_holidays_offset( $offset = 0 ) {
		
		if ( $offset === false ) {
			
			update_user_meta( $this->ID, 'hmh_holidays_offset', 0 );
			return;
		}
	
		if ( ! $offset = strtotime( $offset, 0 ) )
			throw new Exception ( 'Date set error, please use standard format, i.e. 5 days' ); 
	
		update_user_meta( $this->ID, 'hmh_holidays_offset', $offset );
	}
	
	/**
	 * get_holidays_offset function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_holidays_offset() {
	
		return $this->holiday_offset = (int) get_user_meta( $this->ID, 'hmh_holidays_offset', true );
	}
	
	/**
	 * set_holidays_per_year function.
	 * 
	 * @access public
	 * @param int $value (default: 0)
	 * @return void
	 */
	function set_holidays_per_year( $value = 0 ) {
	
		if ( $value === false )
			$value = '28 days';
		
		update_user_meta( $this->ID, 'hmh_holidays_per_year', strtotime( $value, 0 ) );
	}
	
	/**
	 * get_holidays_per_year function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_holidays_per_year() {
	
		return $this->holidays_per_year = (int) get_user_meta( $this->ID, 'hmh_holidays_per_year', true );
	}
	
	/**
	 * get_total_time_taken function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_total_time_taken() {
		
		return $this->total_taken = ( $this->get_holidays_offset() + (int) get_user_meta( $this->ID, 'hmh_running_total', true ) ); 
	}
	
	function get_time_elapsed() {
	
		return $this->time_elapsed = time() - $this->get_employment_start();
	}
	
	function get_total_holidays_earned() {
	
		return $this->total_holidays_earned = ( $this->get_time_elapsed() * ( $this->get_holidays_per_year() / strtotime( '1 year', 0 ) ) );	
	
	}
	
	/**
	 * get_time_holiday_avaliable function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_holiday_time_avaliable() {
	
		$holidays_earned = $this->get_total_holidays_earned();		
		
		return $this->holiday_time_avaliable = ( ( $holidays_earned - $this->get_total_time_taken() ) ); 
		
	}
	
	/**
	 * book_holiday function.
	 * 
	 * @access public
	 * @param mixed $start_date
	 * @param mixed $duration
	 * @return int
	 */
	function book_holiday ( $start_date, $duration, $description ) {

		if ( ! strtotime( $start_date ) || ! strtotime( $duration, 0 ) )
			throw new Exception ( 'Date set error, please use standard format, i.e. "2011-02-20" and "5 days"' ); 
	
		 $start_date_int = strtotime( $start_date );
		 $duration_int = strtotime( $duration, 0 );
		 
		$post = array(
		  'post_author' => $this->ID,
		  'post_content' => stripslashes( $description ),
		  'post_name' => sanitize_title(  $this->data->display_name  . '-' . $start_date . '-' . $duration ),
		  'post_title' => $this->data->display_name . ' - ' . $start_date . ' - ' . $duration ,
		  'post_type' => 'holiday',
		  'post_status' => 'publish'
		);  
		
		$post_id = wp_insert_post( $post );
		
		if ( is_wp_error( $post_id ) || ! $post_id )
			throw new Exception ( 'Error creating new post: ' . var_export( $post_id ) ); 
			
		update_post_meta( $post_id, 'hmh_holiday_start', $start_date_int );
		update_post_meta( $post_id, 'hmh_holiday_end', ( $start_date_int + $duration_int ) );
		update_post_meta( $post_id, 'hmh_holiday_duration', $duration_int );
		
		update_user_meta( $this->ID, 'hmh_running_total', ( (int) get_user_meta( $this->ID, 'hmh_running_total', true ) + $duration_int ) );

		return $post_id;	
	}


}