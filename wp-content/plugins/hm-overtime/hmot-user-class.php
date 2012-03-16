<?php 

/**
 * HMOT_User class.
 */
class HMOT_User {

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
		
		$this->data->name = get_the_author_meta( 'first_name', $this->ID );
		
		$this->get_wage();
		$this->get_total_overtime();
		$this->get_total_payment();
		$this->get_pending_payment();
		$this->get_pending_overtime();
		$this->get_overtime_wage_timestamp();
			
		return $data->data;	
	}			

	/**
	 * set_wage function.
	 * 
	 * @access public
	 * @param int $date (default: 0)
	 * @return void
	 */
	function set_wage( $wage = 0 ) {

		if ( ! $wage )
			return;
		
		 $wage_sanitized = str_replace( array( '.', '-', ',', ' ' ), '', $_POST['hmot_wage'] );
	
		update_user_meta( $this->ID, 'hmot_wage', $date_sanitized );
	}
	
	/**
	 * get_wage function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_wage() {
		
		return $this->wage = (int) get_user_meta( $this->ID, 'hmot_wage', true );
	}	
	
	/**
	 * get_total_overtime_hours function.
	 * 
	 * @access public
	 * @return int
	 */
	function get_total_overtime() {
		
		return $this->total_taken = (int) get_user_meta( $this->ID, 'hmot_running_total', true ) ; 
	}
	
	function get_total_payment() {
		
		return $this->total_taken = (int) get_user_meta( $this->ID, 'hmot_running_total_payment', true ); 
	}
	
	function get_pending_payment() {
		
		return $this->total_taken = (int) get_user_meta( $this->ID, 'hmot_pending_payment', true ); 
	}
	
	function get_pending_overtime() {
		
		return $this->total_taken = (int) get_user_meta( $this->ID, 'hmot_pending_overtime', true ); 
	}
	
	function get_overtime_wage_timestamp() {
	
		return $this->hourly_overtime_wage = ( ( get_user_meta( $this->ID, 'hmot_wage', true ) * 2 ) / ( 52 * 40 * 3600 ) );
	}
		
	function add_to_user_meta( $meta_key, $addition ) {
	
		update_user_meta( $this->ID, $meta_key, ( (int) get_user_meta( $this->ID, $meta_key, true ) + (int) $addition ) );
	}
	
	function reset_pending_overtime() {
		
		update_user_meta( $this->ID, 'hmot_pending_payment', 0 );
		update_user_meta( $this->ID, 'hmot_pending_overtime', 0 );
		
		$this->get_pending_overtime();
		$this->get_pending_payment();
		
		return;
	}
	
	/**
	 * log_overtime function.
	 * 
	 * @access public
	 * @param mixed $start_date
	 * @param mixed $duration
	 * @return int
	 */
	function log_overtime ( $date, $duration, $description ) {
	
		 $date_int = strtotime( $date );
		 $duration_int = strtotime( $duration, 0 );
		 
		$post = array(
		  'post_author' => $this->ID,
		  'post_content' => stripslashes( $description ),
		  'post_name' => sanitize_title(  $this->data->display_name  . '-' . $date . '-' . $duration ),
		  'post_title' => $this->data->display_name . ' - ' . $date . ' - ' . $duration ,
		  'post_type' => 'overtime',
		  'post_status' => 'publish'
		);  
		
		$post_id = wp_insert_post( $post );
		
		update_post_meta( $post_id, 'hmot_date', $date_int );
		update_post_meta( $post_id, 'hmot_duration', $duration_int );
		
		$this->add_to_user_meta( 'hmot_running_total', $duration_int );
		$this->add_to_user_meta( 'hmot_running_total_payment', ( $duration_int * $this->get_overtime_wage_timestamp() ) );
	
		$this->add_to_user_meta( 'hmot_pending_payment', ( $duration_int * $this->get_overtime_wage_timestamp() ) );	
		$this->add_to_user_meta( 'hmot_pending_overtime', $duration_int );
	
		return $post_id;	
	}
	
	
	function resolve_overtime( $resolver_id ) {
		
		$title = get_userdata( $resolver_id )->display_name . ' resolved overtime for ' 
				 . $this->data->name . ' hours: ' . hmot_in_hours( $this->get_pending_overtime() ). ' payment: &pound;' 
				 . $this->get_pending_payment();
		
		$post = array(
		  'post_author' => $resolver_id,
		  'post_content' => $title,
		  'post_name' => sanitize_title( $title ),
		  'post_title' => $title ,
		  'post_type' => 'resolved-overtime',
		  'post_status' => 'publish'
		);
		
		$post_id = wp_insert_post( $post );
		
		update_post_meta( $post_id, 'hmot_resolved_for', $this->ID );
		update_post_meta( $post_id, 'hmot_resolved_by', $resolver_id );
		
		update_post_meta( $post_id, 'hmot_hours_resolved', $this->get_pending_overtime() );
		update_post_meta( $post_id, 'hmot_payment_resolved', $this->get_pending_payment() );
		
		update_post_meta( $post_id, 'hmot_total_hours_resolved', $this->get_total_overtime() );
		update_post_meta( $post_id, 'hmot_payment_resolved', $this->get_total_payment() );
		
		update_post_meta( $post_id, 'hmot_wage', $this->get_wage() );
		
		$this->reset_pending_overtime();
		
	}

}