<?php
//Dashboard Widget
add_action('wp_dashboard_setup', 'af_custom_dashboard_widgets');
function af_custom_dashboard_widgets() {
global $wp_meta_boxes;
wp_add_dashboard_widget('custom_help_widget', 'Application Submission Lists', 'af_custom_dashboard_help');
}
function af_custom_dashboard_help() {
    ?>
    <style>
    .widgetClass{font-family: arial, sans-serif;border-collapse: collapse;width: 100%;}
    .widgetClass td, th { border: 1px solid #dddddd;text-align: left;padding: 8px;}
    </style>
    <table class="widgetClass">
	  <thead>
	  <tr>
		<th>SL.</th>
		<th>First Name</th>
		<th>Email Address</th>
		<th>CV</th>
	  </tr>
	  </thead>
	  <tbody>
	  <?php 
	  global $wpdb;
	  $table_name = $wpdb->prefix . "applicant_submissions";
	  $user = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY ID DESC LIMIT 5" );
	  $i = 0;
	  foreach ($user as $row){ 
	   ?>
	   <tr>
		<td><?php esc_html_e($i+=1,'af'); ?></td>
		<td><?php esc_html_e($row->first_name,'af'); ?></td> 
		<td><?php esc_html_e($row->email_address,'af'); ?></td> 
		<td><a href="<?php $upload_dir = wp_upload_dir(); echo $upload_dir['baseurl'].'/applicant_submission_files/'.$row->cv;?>" target="_blank">Download CV</a>
		</td>
	  </tr>  
	  <?php 
	  }
        if(empty($user) || !isset($user)){
            echo '<tr class="odd"><td colspan="10" style="text-align: center">No data available in table</td></tr>';
        }
	 ?> 
	  </tbody>
	</table>
    <?php
}
?>