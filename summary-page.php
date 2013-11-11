<?php

/**
* Plugin Name: Summary Page
* Plugin URI: https://github.com/guoquan/summary-page
* Description: List and/or include sub-pages (child pages).
* Version: 0.0.1
* Author: Guo Quan
* Author URI: http://guoquan.org
*/


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
// define once
if ( !class_exists( 'SummaryPage' ) ) :

// 
class SummaryPage
{
	public static $IS_SUMMARY_KEY = 'is_summary';

	// Singleton 
	private static $instance;
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new SummaryPage();

			self::$instance->setup_component();
		}
		return self::$instance;
	}

	private function __construct() {
		// and absolutly nothing here!!!
		// turn to instance function
	}

	/**
	 * A dummy magic method to prevent singleton instanse from being cloned
	 *
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'SummaryPage' ), '1.7' ); }

	/**
	 * A dummy magic method to prevent singleton instanse from being unserialized
	 *
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'SummaryPage' ), '1.7' ); }


	private function setup_component() {
		add_action( 'pre_get_posts', array($this,'show_child') );
	}


	public function show_child( $query ) {
	    if ( $query->is_page() && $query->is_main_query() ) {
	    	//print_r($query);

	    	$page_id = $query->get_queried_object_id();
	    	if ( !$page_id ) $page_id = $query->get( 'page_id' ); // in case referenced by index
			
	    	$is_summary = get_post_meta( $page_id, self::$IS_SUMMARY_KEY, true );
	    	if ( $is_summary ) {
	    		$children = get_pages( array(
					'parent'		=> $page_id,
					'post_type'		=> 'page',
					'sort_column'	=> 'menu_order',
					'sort_order'	=> 'ASC',
				));

		        $incl = array((int)$page_id);
		        foreach ($children as $key => $value) {
		        	array_push( $incl, (int)($value->ID) );
		        }
		        //print_r($incl);

		    	$query->init();
		    	$query->set( 'order',			'ASC' );
		    	$query->set( 'orderby',			'post__in' );
		    	$query->set( 'posts_per_page',	'-1' );
		    	$query->set( 'post_type',		'page' );
		    	$query->set( 'post__in',		$incl );
	    	}	
	    }
	    return;
	}
}

function sp() {
	return SummaryPage::instance();
}

$sp = sp();

endif;
?>