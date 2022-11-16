<?php
/*
 * Plugin Name: Applicant Form
 * Plugin URI: #
 * Description: Install and send your CV.
 * Author: Md. Shahinur Islam
 * Author URI: https://profiles.wordpress.org/shahinurislam
 * Version: 1.0
 * Text Domain: af
 * Domain Path: /lang
 * Network: True
 * License: GPLv2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */
 
define( 'AF_PLUGIN', __FILE__ );
define( 'AF_PLUGIN_DIR', untrailingslashit( dirname( AF_PLUGIN ) ) );
require_once AF_PLUGIN_DIR . '/include/enqueue.php';
require_once AF_PLUGIN_DIR . '/include/dashboard_widget.php';
require_once AF_PLUGIN_DIR . '/include/top_level_menu.php';
//db create
global $af_jal_db_version;
$af_jal_db_version = '1.0';
function af_jal_install() {
	global $wpdb;
	global $af_jal_db_version;
	
	$table_name = $wpdb->prefix . 'applicant_submissions';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		first_name tinytext NOT NULL,		
		last_name tinytext NOT NULL,
		present_address text NOT NULL,
		email_address varchar(55) DEFAULT '' NOT NULL,
		mobile_no tinytext NOT NULL,
		post_name text NOT NULL,
		cv text NOT NULL,
		created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	add_option( 'af_jal_db_version', $af_jal_db_version );
}
register_activation_hook( __FILE__, 'af_jal_install' );
global $wpdb;
$installed_ver = get_option( "af_jal_db_version" );
if ( $installed_ver != $af_jal_db_version ) {

	$table_name = $wpdb->prefix . 'applicant_submissions';

	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		first_name tinytext NOT NULL,		
		last_name tinytext NOT NULL,
		present_address text NOT NULL,
		email_address varchar(55) DEFAULT '' NOT NULL,
		mobile_no tinytext NOT NULL,
		post_name text NOT NULL,
		cv text NOT NULL,
		created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( "af_jal_db_version", $af_jal_db_version );
}
function af_update_db_check() {
    global $af_jal_db_version;
    if ( get_site_option( 'af_jal_db_version' ) != $af_jal_db_version ) {
        af_jal_install();
    }
}
add_action( 'plugins_loaded', 'af_update_db_check' );
//shortcode
function af_shortcode_wrapper($atts) {
ob_start();
if ( isset( $_POST['submit'] ) ){	

	require_once(ABSPATH . 'wp-admin/includes/file.php');
	global $wp_filesystem;
	WP_Filesystem();
	$content_directory = $wp_filesystem->wp_content_dir() . 'uploads/';
	$wp_filesystem->mkdir( $content_directory . 'applicant_submission_files' );
	$target_dir_location = $content_directory . 'applicant_submission_files/';	
	$name_file = $_FILES['cv']['name'];
	$name_file = time().'_'.$name_file;
    $tmp_name = $_FILES['cv']['tmp_name']; 
    if( move_uploaded_file( $tmp_name, $target_dir_location.$name_file ) ) {
        $cvfiles = "with your cv.";
    } else {
        $cvfiles = "without your cv.";
    }
	
	global $wpdb;
	$table_name = $wpdb->prefix."applicant_submissions";
	$sql = $wpdb->prepare( "INSERT INTO ".$table_name." (first_name, last_name, present_address, email_address, mobile_no, post_name, cv ) VALUES ( %s, %s, %s, %s, %s, %s, %s )", $_POST['first_name'], $_POST['last_name'], $_POST['present_address'], $_POST['email_address'], $_POST['mobile_no'], $_POST['post_name'], $name_file );
	$wpdb->query($sql);
	// get the inserted record id.
	$id = $wpdb->insert_id;
	if($id>0){
		?>
		<div class="alert-af alert-success-af" role="alert">
			Application has been sent <?php esc_html_e($cvfiles);?>
		</div>
		<?php
		function wpb_sender_name( $original_email_from ) {
            return 'Application Form';
        }
        add_filter( 'wp_mail_from_name', 'wpb_sender_name' );
		//user notification
		$to = $_POST['email_address'];
        $subject = 'Thanks for application';
        $body = 'Dear, Thanks for application. We will contact soon.';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $body, $headers );
        
        
	}else{
		?><div class="alert-af alert-success-af" role="alert">Application not sent <?php esc_html_e($cvfiles);?></div><?php
	}
}
?>
<form action="" method="post" id="myForm" enctype="multipart/form-data">
<div class="afmb-3">
	<label for="exampleInputname" class="form-label-af">First Name</label>
	<input type="text" name="first_name" class="form-control-af" id="exampleInputname" aria-describedby="Fname" required>
</div>
<div class="afmb-3">
	<label for="exampleInputlast" class="form-label-af">Last Name</label>
	<input type="text" name="last_name" class="form-control-af" id="exampleInputlast" aria-describedby="Lname" required>
</div>
<div class="afmb-3">
	<label for="exampleInputpa" class="form-label-af">Present Address</label>
	<input type="text" name="present_address" class="form-control-af" id="exampleInputpa" aria-describedby="paddress" required>
</div>
<div class="afmb-3">
	<label for="exampleInputEmail1" class="form-label-af">Email address</label>
	<input type="email" name="email_address" class="form-control-af" id="exampleInputEmail1" aria-describedby="email" required>
</div>
<div class="afmb-3">
	<label for="exampleInputPhone" class="form-label-af">Mobile No</label>
	<input type="text" name="mobile_no" class="form-control-af" id="exampleInputPhone" aria-describedby="Phone" required>
</div>
<div class="afmb-3">
	<label for="exampleInputPostname" class="form-label-af">Post Name</label>
	<input type="text" name="post_name" class="form-control-af" id="exampleInputPostname" aria-describedby="Post" required>
</div>
<div class="afmb-3">
	<label for="exampleInputPostname" class="form-label-af">Upload CV</label>
	<input type="file" name="cv" class="form-control-af" id="exampleInputPostname" aria-describedby="cv_upload" multiple="false" required>
</div>
  <button type="submit" name="submit" class="btn-af btn-primary-af">Submit</button>
</form>

<?php   
 return ob_get_clean();
}
add_shortcode('applicant_form','af_shortcode_wrapper'); 
//side setting link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'af_plugin_action_links' );
function af_plugin_action_links( $actions ) {
   $actions[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=appform') ) .'">Settings</a>';
   return $actions;
}
?>
