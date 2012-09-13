<?php 
/*
Plugin Name: WP Auth
Plugin URI: http://www.ivansotof.com/
Description: Auth functions for extending WP.
Version: 1
Author: Ivan Soto
Author URI: http://www.ivansotof.com
*/

class WpAuth {

	function WpAuth() {
		register_activation_hook(__FILE__, array($this, 'activate'));
		
		$this->path = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));

		add_action('admin_menu', array(&$this, 'pages'), 5, __FILE__, 'wpauth_toplevel_page');
	}


	function activate()
	{
		// global $wpdb;
		// $table_name = $wpdb->prefix . "advice_user";
		// $sql = "CREATE TABLE `$table_name` (
		  // `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  // `customer_id` int(11) DEFAULT NULL,
		  // `goal1_name` varchar(60) DEFAULT NULL,
		  // `goal2_name` varchar(60) DEFAULT NULL,
		  // `goal3_name` varchar(60) DEFAULT NULL,
		  // PRIMARY KEY (`id`)
		// )";
		// require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		// dbDelta($sql);
	}

	/**
	 * Defines option pages.
	 */
	function pages() 
	{
		add_menu_page('WP Auth Options', 'WP Auth', 'edit_pages', 'wp_auth_admin', array(&$this, 'page_config'), 
			$this->path . 'img/ico-unlocked.png' );
		add_submenu_page('wp_auth_admin', 'Lockdown Options','Lockdown', 'edit_pages', 
			'wp_auth_admin_sub', array(&$this, 'page_config'));

	}


	function page_config()
	{
		// Template page.
		require_once( dirname( __FILE__ ) . '/view-auth-options.php' );
	}
}

$wpauth = new WpAuth();