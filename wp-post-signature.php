<?php
/*
Plugin Name: WP Post Signature
Plugin URI: http://wordpress.org/extend/plugins/wp-post-signature/
Description: This plugin allows you to append a signature after every post. Some variables can be used, such as %post_title%, %post_link%, %bloginfo_name%, %bloginfo_url%, and so on. It supports multiuser.
Version: 0.2.0
Author: Soli
Author URI: http://www.cbug.org
Text Domain: wp-post-signature
Domain Path: /lang


Copyright (c) 2011
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt
*/

if(!class_exists('WPPostSignature')) {
class WPPostSignature {

function is_str_and_not_empty($var) {
	if (!is_string($var))
		return false;
	
	if (empty($var))
		return false;
	
	if ($var=='')
		return false;
	
	return true; 
}

function AppendSignature($content) {

	global $post;
	$author = $post->post_author;
	$categories = get_the_category($post->ID);
	$wp_post_signature = maybe_unserialize(get_option('wp_post_signature'));
	$current_signature = $wp_post_signature[$author];

	if(!isset($current_signature['signature_switch']) || $current_signature['signature_switch'] == 'no') {
		return $content;
	}

	if(isset($current_signature['signature_include_types'])){
		if(!is_singular()) {
			if(!in_array('postlist', $current_signature['signature_include_types'])) {
				return $content;
			}
		}else if(is_single()) {
			if(!in_array('post', $current_signature['signature_include_types'])) {
				return $content;
			}
		}else if(is_page()) {
			if(!in_array('page', $current_signature['signature_include_types'])) {
				return $content;
			}
		}else {
			if(!in_array('other', $current_signature['signature_include_types'])) {
				return $content;
			}
		}

	}else if(!is_singular()) {	// for compatible with v 0.2.0 and before
		if(!isset($current_signature['signature_archive_list_switch']) || $current_signature['signature_archive_list_switch'] != 'yes') {
			return $content;
		}
	}


	if(isset($current_signature['signature_exclude_cates'])){
		foreach ($categories as $category) {
			if(in_array($category->cat_ID, $current_signature['signature_exclude_cates'])){
				return $content;
			}
		}
	}

	//variables
	$env_vars = array();
	$env_vars['%post_title%']					= get_the_title();
	$env_vars['%post_link%']					= get_permalink();
	$env_vars['%post_author%']					= get_the_author();
	$env_vars['%post_trackback_url%']			= get_trackback_url();

	$env_vars['%post_date%']					= $post->post_date;
	$env_vars['%post_date_gmt%']				= $post->post_date_gmt;
	$env_vars['%post_modified%']				= $post->post_modified;
	$env_vars['%post_modified_gmt%']			= $post->post_modified_gmt;

	$env_vars['%bloginfo_name%']				= get_bloginfo('name');
	$env_vars['%bloginfo_description%']			= get_bloginfo('description');
	$env_vars['%bloginfo_siteurl%']				= get_site_url();
	$env_vars['%bloginfo_url%']					= get_home_url();
	$env_vars['%bloginfo_admin_email%']			= get_bloginfo('admin_email');
	$env_vars['%bloginfo_pingback_url%']		= get_bloginfo('pingback_url');
	$env_vars['%bloginfo_atom_url%']			= get_bloginfo('atom_url');
	$env_vars['%bloginfo_rdf_url%']				= get_bloginfo('rdf_url');
	$env_vars['%bloginfo_rss_url%']				= get_bloginfo('rss_url');
	$env_vars['%bloginfo_rss2_url%']			= get_bloginfo('rss2_url');
	$env_vars['%bloginfo_comments_atom_url%']	= get_bloginfo('comments_atom_url');
	$env_vars['%bloginfo_comments_rss2_url%']	= get_bloginfo('comments_rss2_url');


	if($current_signature['signature_pos'] == 'top') {
		return strtr(stripslashes($current_signature['signature_text']), $env_vars) . $content;
	} else {
		return $content . strtr(stripslashes($current_signature['signature_text']), $env_vars);
	}
}

/**
 * Registers additional links for the plugin on the WP plugin configuration page
 *
 * Registers the links if the $file param equals to the plugin
 * @param $links Array An array with the existing links
 * @param $file string The file to compare to
 */
function RegisterPluginLinks($links, $file) {
	load_plugin_textdomain( 'wp-post-signature', false, dirname( plugin_basename( __FILE__ ) ) . "/lang" );
	$base = plugin_basename(__FILE__);
	if ($file ==$base) {
		$links[] = '<a href="options-general.php?page=wp-post-signature">' . __('Settings','wp-post-signature') . '</a>';
		$links[] = '<a href="http://www.cbug.org/category/wp-post-signature">' . __('FAQ','wp-post-signature') . '</a>';
	}
	return $links;
}

/**
 * Handled the plugin activation on installation
 */
function ActivatePlugin() {
	$optfile = trailingslashit(dirname(__FILE__)) . "options.txt";
	$options = file_get_contents($optfile);
	add_option("wp_post_signature", $options, null, 'no');
}

/**
 * Handled the plugin deactivation
 */
function DeactivatePlugin() {
	$optfile = trailingslashit(dirname(__FILE__)) . "options.txt";
	file_put_contents($optfile, get_option("wp_post_signature"));
	delete_option("wp_post_signature");
}

} // end of class WPPostSignature
} // end of if(!class_exists('WPPostSignature'))

load_plugin_textdomain( 'wp-post-signature', false, dirname( plugin_basename( __FILE__ ) ) . "/lang" );

if(class_exists('WPPostSignature')) {

	$wppostsignature = new WPPostSignature();

	if(isset($wppostsignature)) {
		register_activation_hook(__FILE__, array(&$wppostsignature, 'ActivatePlugin'));
		register_deactivation_hook(__FILE__, array(&$wppostsignature, 'DeactivatePlugin'));

		//Additional links on the plugin page
		add_filter('plugin_row_meta', array(&$wppostsignature, 'RegisterPluginLinks'),10,2);

		//Add the filter
		add_filter('the_content', array(&$wppostsignature, 'AppendSignature'));
	}
}

/* Options Page */
require_once(trailingslashit(dirname(__FILE__)) . "wp-post-signature-page.php");

if(class_exists('WPPostSignaturePage')) {
	$wppostsignature_page = new WPPostSignaturePage();
	
	if(isset($wppostsignature_page)) {
		add_action('admin_menu', array(&$wppostsignature_page, 'WPPostSignature_Menu'), 1);
	}
}
?>
