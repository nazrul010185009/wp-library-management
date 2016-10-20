<?php 
/*
Plugin Name:    WP Books Stock System
Plugin URI:     http://awesomewaterfall.com
Description:    This plugin is very useful for library management
Version:        1.0
Author:         awesomewaterfall
Author URI:     http://awesomewaterfall.com
*/


if ( ! defined( 'ABSPATH' ) ) exit;

if ( !defined( 'WLMS_PLUGIN_URL' ) )
  define('WLMS_PLUGIN_URL', plugins_url('',__FILE__));
	
if ( !defined( 'WLMS_PLUGIN_DIR' ) )
  define( 'WLMS_PLUGIN_DIR', dirname(__FILE__) );

include('includes/wlms-plugin-files.php');

// plugin global assets
function wlms_plugin_init()
{
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-core');
  wp_enqueue_script( 'jquery-ui-datepicker' );
  wp_enqueue_style( 'wlms-datepicker-css', WLMS_PLUGIN_URL.'/assets/css/jquery-ui/jquery-ui.css');
  
  ob_start();
  
  if( !session_id() )
  {
    session_start();
  }
}

// admin assets
function register_wlms_plugin_script_admin() 
{
  wp_enqueue_style( 'wlms-admin-style', WLMS_PLUGIN_URL.'/assets/css/admin/wlms-admin-style.css');
  wp_enqueue_script( 'wlms-admin-js', WLMS_PLUGIN_URL.'/assets/js/wlms-admin.min.js');
  wp_enqueue_script( 'wlms-validation-js', WLMS_PLUGIN_URL.'/assets/js/jquery.validation.js');
  wp_enqueue_script( 'wlms-raphael-script', WLMS_PLUGIN_URL.'/assets/js/raphael-min.js' );
  wp_enqueue_script( 'wlms-morris-js', WLMS_PLUGIN_URL.'/assets/js/morris.min.js');
  
  $params = array(
    'ajaxurl'    => admin_url('admin-ajax.php'),
    'ajax_nonce' => wp_create_nonce('eLibrary_ajax_call'),
  );
  wp_localize_script( 'wlms-admin-js', 'ajax_object', $params );
  
  wlms_tablesorter_assets();
}


//table sorter assets
function wlms_tablesorter_assets()
{
  wp_enqueue_style( 'wlms-dataTable-css', WLMS_PLUGIN_URL.'/assets/datatables/jquery.dataTables.css');
  wp_enqueue_script( 'wlms-dataTable-js-1', WLMS_PLUGIN_URL.'/assets/datatables/jquery.dataTables.min.js');
  wp_enqueue_script( 'wlms-dataTable-js-2', WLMS_PLUGIN_URL.'/assets/datatables/dataTables.bootstrap.min.js');
}


// custom template
function wlms_use_custom_template( $template )
{
	$template_slug = basename(rtrim( $template, '.php' ));
	
	if( is_home() && isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' ) 
	{
		$template = plugin_dir_path( __FILE__ ) . 'includes/pages/frontend/wlms-member-dashboard.php';
    wp_enqueue_script( 'wlms-frontend-js', WLMS_PLUGIN_URL.'/assets/js/wlms-frontend.min.js');
    wp_enqueue_style( 'wlms-frontend-style-1', WLMS_PLUGIN_URL.'/assets/css/frontend/wlms-frontend-style.css');
    wp_enqueue_style( 'wlms-frontend-style-2', WLMS_PLUGIN_URL.'/assets/css/admin/wlms-admin-style.css');
    wlms_tablesorter_assets();
    
    $params = array(
    'ajaxurl'    => admin_url('admin-ajax.php'),
    'ajax_nonce' => wp_create_nonce('eLibrary_ajax_call'),
    );
    wp_localize_script( 'wlms-frontend-js', 'ajax_object', $params );
  
	}
  
	return $template;
}

add_action( 'init', 'wlms_plugin_init');		
add_action( 'admin_enqueue_scripts', 'register_wlms_plugin_script_admin' );
add_shortcode('wlms_login', 'wlms_login_details' );
add_filter( 'template_include', 'wlms_use_custom_template', 99 );
register_activation_hook( __FILE__, 'wlms_plugin_install' );
register_deactivation_hook(__FILE__, 'wlms_plugin_uninstall');
?>