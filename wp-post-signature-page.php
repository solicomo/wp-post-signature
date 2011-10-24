<?php
/*
WP Post Signature Page
*/

$wpps_status = "normal";

if($_POST['wpps_update_options'] == 'Y') {
	global $current_user;
	require (ABSPATH . WPINC . '/pluggable.php');
	get_currentuserinfo();
	$wp_post_signature = maybe_unserialize(get_option('wp_post_signature'));
	$wp_post_signature[$current_user->ID] = $_POST;
	update_option("wp_post_signature", maybe_serialize($wp_post_signature));
	$wpps_status = 'update_success';
}

if(!class_exists('WPPostSignaturePage')) {
class WPPostSignaturePage {
function WPPostSignature_Options_Page() {
	?>

	<div class="wrap">
	<div id="wpps-options">
	<div id="wpps-title"><h2>WP Post Signature</h2></div>
	<?php
	global $wpps_status;
	if($wpps_status == 'update_success')
		$message =__('Configuration updated', 'wp-post-signature') . "<br />";
	else if($wpps_status == 'update_failed')
		$message =__('Error while saving options', 'wp-post-signature') . "<br />";
	else
		$message = '';

	if($message != "") {
	?>
		<div class="updated"><strong><p><?php
		echo $message;
		?></p></strong></div><?php
	} ?>
	<div id="wpps-desc">
	<p><?php _e('This plugin allows you to append a signature after every post. Some variables can be used, such as %post_title%, %post_link%, %bloginfo_name%, %bloginfo_url%, and so on. It supports multiuser.', 'wp-post-signature'); ?></p>
	</div>

	<!--left-->
	<div class="postbox-container" style="width:75%;">
	<div class="metabox-holder">
	<div class="meta-box-sortabless">

	<!--setting-->
	<div id="wpps-setting" class="postbox">
	<h3 class="hndle"><?php _e('Settings', 'wp-post-signature'); ?></h3>
	<?php
		global $current_user;
		get_currentuserinfo();
		$wp_post_signature = maybe_unserialize(get_option('wp_post_signature'));
		$current_signature = $wp_post_signature[$current_user->ID];
	?>
	<form method="post" action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=wp-post-signature">
	<div style="padding-left: 10px;">
	<input type="hidden" name="wpps_update_options" value="Y">

	<p><?php _e('Enter your post signature in the text area below. HTML markup is allowed.', 'wp-post-signature'); ?></p>
	<textarea cols="75" rows="5" name="signature_text"><?php echo stripslashes($current_signature['signature_text']); ?></textarea><br />
	<p><?php _e('Will the signature be on or off by default?', 'wp-post-signature'); ?></p>
	<input type="radio" name="signature_switch" value="yes" <?php if($current_signature['signature_switch'] == 'yes') { echo 'checked'; } ?> /><?php _e('On', 'wp-post-signature'); ?>
	<input type="radio" name="signature_switch" value="no" <?php if($current_signature['signature_switch'] == 'no') { echo 'checked'; } ?> /><?php _e('Off', 'wp-post-signature'); ?><br />

	<p><?php _e('Where should the signature be placed?', 'wp-post-signature'); ?></p>
	<input type="radio" name="signature_pos" value="top" <?php if($current_signature['signature_pos'] == 'top') { echo 'checked'; } ?> /><?php _e('Top', 'wp-post-signature'); ?>
	<input type="radio" name="signature_pos" value="bottom" <?php if($current_signature['signature_pos'] == 'bottom') { echo 'checked'; } ?> /><?php _e('Bottom', 'wp-post-signature'); ?><br />

	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes', 'wp-post-signature'); ?>" />
	</p>
	</div>
	</form>
	</div>
	<!--setting end-->

	<!--others-->
	<div id="wpps-info" class="postbox">
	<h3 class="hndle"><?php _e('Supported variables:', 'wp-post-signature'); ?></h3>

	<table class="form-table">
	<tr>
	<th scope="row"><b><?php _e('Variable', 'wp-post-signature'); ?></b></th>
	<td><b><?php _e('Description', 'wp-post-signature'); ?></b></td>
	<td><b><?php _e('Sample Value', 'wp-post-signature'); ?></b></td>
	</tr>

	<tr>
	<th scope="row">%post_title%</th>
	<td><?php _e('The post title.', 'wp-post-signature'); ?></td>
	<td>How to use WP Post Signature?</td>
	</tr>

	<tr>
	<th scope="row">%post_link%</th>
	<td><?php _e('The permalink for a post with a custom post type.', 'wp-post-signature'); ?></td>
	<td>http://www.cbug.org/2011/05/08/57.html</td>
	</tr>

	<tr>
	<th scope="row">%post_author%</th>
	<td><?php _e('The post author.', 'wp-post-signature'); ?></td>
	<td>Soli</td>
	</tr>

	<tr>
	<th scope="row">%post_trackback_url%</th>
	<td><?php _e('The post trackback URL.', 'wp-post-signature'); ?></td>
	<td>Soli</td>
	</tr>

	<tr>
	<th scope="row">%post_date%</th>
	<td><?php _e('The post date.', 'wp-post-signature'); ?></td>
	<td>2011-05-09 16:37:15</td>
	</tr>

	<tr>
	<th scope="row">%post_date_gmt%</th>
	<td><?php _e('The post GMT datetime. (GMT = Greenwich Mean Time)', 'wp-post-signature'); ?></td>
	<td>2011-05-09 08:37:15</td>
	</tr>

	<tr>
	<th scope="row">%post_modified%</th>
	<td><?php _e('Date the post was last modified.', 'wp-post-signature'); ?></td>
	<td>2011-05-09 18:37:15</td>
	</tr>

	<tr>
	<th scope="row">%post_modified_gmt%</th>
	<td><?php _e('GMT date the post was last modified. (GMT = Greenwich Mean Time)', 'wp-post-signature'); ?></td>
	<td>2011-05-09 10:37:15</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_name%</th>
	<td><?php _e('The "Site Title" set in Settings > General.', 'wp-post-signature'); ?></td>
	<td>Soli's blog</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_description%</th>
	<td><?php _e('The "Tagline" set in Settings > General.', 'wp-post-signature'); ?></td>
	<td>Just another WordPress blog</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_siteurl%</th>
	<td><?php _e('The "WordPress address (URI)" set in Settings > General.', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/wp</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_url%</th>
	<td><?php _e('The "Site address (URI)" set in Settings > General.', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_admin_email%</th>
	<td><?php _e('The "E-mail address" set in Settings > General.', 'wp-post-signature'); ?></td>
	<td>admin@example.com</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_pingback_url%</th>
	<td><?php _e('The Pingback XML-RPC file URL (xmlrpc.php).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/wp/xmlrpc.php</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_atom_url%</th>
	<td><?php _e('The Atom feed URL (/feed/atom).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/feed/atom</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_rdf_url%</th>
	<td><?php _e('The RDF/RSS 1.0 feed URL (/feed/rfd).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/feed/rdf</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_rss_url%</th>
	<td><?php _e('The RSS 0.92 feed URL (/feed/rss).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/feed/rss</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_rss2_url%</th>
	<td><?php _e('The RSS 2.0 feed URL (/feed).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/feed</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_comments_atom_url%</th>
	<td><?php _e('The comments Atom feed URL (/comments/feed).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/comments/feed/atom</td>
	</tr>

	<tr>
	<th scope="row">%bloginfo_comments_rss2_url%</th>
	<td><?php _e('The comments RSS 2.0 feed URL (/comments/feed).', 'wp-post-signature'); ?></td>
	<td>http://www.example.com/home/comments/feed</td>
	</tr>

	</table>
	</div>
	<!--others end-->

	</div></div></div>
	<!--left end-->

	<!--right-->
	<div class="postbox-container" style="width:21%;">
	<div class="metabox-holder">
	<div class="meta-box-sortables">

	<!--about-->
	<div id="wpps-about" class="postbox">
	<h3 class="hndle"><?php _e('About this plugin', 'wp-post-signature'); ?></h3>
	<div class="inside"><ul>
	<li><a href="http://www.cbug.org"><?php _e('Plugin URI', 'wp-post-signature'); ?></a></li>
	<li><a href="http://www.cbug.org" target="_blank"><?php _e('Author URI', 'wp-post-signature'); ?></a></li>
	</ul></div>
	</div>
	<!--about end-->

	<!--others-->
	<!--others end-->

	</div></div></div>
	<!--right end-->

	</div>
	</div>
	<?php
}

function WPPostSignature_Menu() {
	add_options_page(__('WP Post Signature'), __('WP Post Signature'), 1, 'wp-post-signature', array(__CLASS__,'WPPostSignature_Options_Page'));
}

} // end of class WPPostSignaturePage
} // end of if(!class_exists('WPPostSignaturePage'))
?>
