<?php
//--If Add Shortcode use any single page with design--//
function af_add_css_js(){        
    wp_enqueue_style( 'af_bootstrap', plugin_dir_url(__FILE__) . '../css/main.css', array(), '1.0.0', 'all' );  
}add_action('wp_enqueue_scripts','af_add_css_js');
function af_admin_enqueue($hook) {
   if ( 'toplevel_page_appform' == $hook ) {  
      wp_enqueue_style('af-admin-css', plugin_dir_url( __FILE__ ). '../css/admin.css', array(), '1.0.0', 'all');
	  wp_enqueue_script('af_custom_js', plugin_dir_url(__FILE__) . '../js/custom.js' , array('jquery'),'1.0.0',true);   
    }
  }add_action('admin_enqueue_scripts', 'af_admin_enqueue');
?>
