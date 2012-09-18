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
		if (!session_id() ) {
			session_start();
		}



		register_activation_hook(__FILE__, array($this, 'activate'));
		
		$this->path = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
		require plugin_dir_path(__FILE__) . 'wp-auth-widget.php';

		wp_register_style('wp-auth', $this->path . 'css/wp-auth.css' );
		wp_enqueue_style('wp-auth');

		add_action('admin_menu', array(&$this, 'pages'), 5, __FILE__, 'wpauth_toplevel_page');
		add_action('wp', array(&$this, 'login_process') );
		add_action('wp', array(&$this, 'logout') );

		add_shortcode('wpauth-login', array(&$this, 'shortcode_login'));

	}


	function activate()
	{
		
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
		add_options_page('Save Options', 'Sub page', 'manage_options', 'save_wp_auth_options', array(&$this, 'save_wp_auth_options'));
	}

	function page_config()
	{
		// Template page.
		require_once( dirname( __FILE__ ) . '/view-auth-options.php' );
	}

	function save_wp_auth_options()
	{
		update_option('wp-auth-boxstyle', $_POST['wp-auth-box-style']);
		update_option('wp-auth-buttonstyle', $_POST['wp-auth-button-style']);

		wp_redirect(admin_url() . 'admin.php?page=wp_auth_admin', 302);
		// return 'asdasd';
	}

	function shortcode_login()
	{
		if (!session_id() ) {
			session_start();
		}
		if (isset($_SESSION['error_msg'])) {
			echo '<div class="wp-auth-error">' . $_SESSION['error_msg'] . '</div>';
			unset($_SESSION['error_msg']);
		}
		?>
		<div id="wp-auth-login" class="<?php echo get_option('wp-auth-boxstyle', 'white') ?>">
			<form action="<?php echo get_bloginfo('url') ?>/login_process" method="post">
				<p>
					<label for="">Username</label> <br>
					<input type="text" name="wp-auth-login" value="">
				</p>
				<p>
					<label for="">Password</label> <br>
					<input type="password" name="wp-auth-password" value="">
				</p>
				<input type="submit" value="Login" class="submit <?php echo get_option('wp-auth-buttonstyle', 'blue') ?>">
			</form>
		</div>
		<?php

	}

	function login_process()
	{
		global $wp_query, $wpdb;


		// creating a temporary fake post we can use to return.
		// Based on Query Wrangler plugin.
		$post = new stdClass();
		$post->ID           = -42;  // Arbitrary post id
		$post->post_title   = $post_title;
		$post->post_content = 'Login page'; // this won't show.
		$post->post_status  = 'publish';
		$post->post_type    = 'page';
		// $post->post_category= array('uncategorized');
		$post->post_excerpt = '';


		if ($wp_query->query_vars['name'] === 'login_process') {

			$wp_query->queried_object = $post;
			$wp_query->post           = $post;
			$wp_query->found_posts    = true;
			$wp_query->post_count     = true;
			//$wp_query->max_num_pages = true;
			$wp_query->is_single      = true;
			$wp_query->is_posts_page  = true;
			$wp_query->is_page        = true;
			$wp_query->posts          = array($post);
			$wp_query->is_404         = false;
			$wp_query->is_post        = false;
			$wp_query->is_home        = false;
			$wp_query->is_archive     = false;
			$wp_query->is_category    = false;
			status_header(200);

			//$wp_query = atb_query_handler($wp_query);
			

			if ( !is_user_logged_in() ) {
		        $creds = array();
		        $creds['user_login'] = $_POST['wp-auth-login'];
		        $creds['user_password'] = $_POST['wp-auth-password'];
		        $creds['remember'] = true;
		        $user = wp_signon( $creds, false );

		        if ( is_wp_error($user) ) {
		        	$_SESSION['error_msg'] = $user->get_error_message();
		        	wp_redirect($_SERVER['HTTP_REFERER'], 302);
		        	exit();
		        }

		    } else {
		    	wp_redirect(get_bloginfo('url'), 302);
		    	exit();
		    }


			#unset($_SESSION['visitor_id']);
			#unset($_SESSION['plan']);
			#unset($_SESSION['password']);

			wp_redirect(get_bloginfo('url'), 302);
			exit();
		}
	}


	function logout()
	{
		global $wp_query, $wpdb;


		// creating a temporary fake post we can use to return.
		// Based on Query Wrangler plugin.
		$post = new stdClass();
		$post->ID           = -42;  // Arbitrary post id
		$post->post_title   = $post_title;
		$post->post_content = 'Login page'; // this won't show.
		$post->post_status  = 'publish';
		$post->post_type    = 'page';
		// $post->post_category= array('uncategorized');
		$post->post_excerpt = '';

		if ($wp_query->query_vars['name'] === 'logout') {

			$wp_query->queried_object = $post;
			$wp_query->post           = $post;
			$wp_query->found_posts    = true;
			$wp_query->post_count     = true;
			//$wp_query->max_num_pages = true;
			$wp_query->is_single      = true;
			$wp_query->is_posts_page  = true;
			$wp_query->is_page        = true;
			$wp_query->posts          = array($post);
			$wp_query->is_404         = false;
			$wp_query->is_post        = false;
			$wp_query->is_home        = false;
			$wp_query->is_archive     = false;
			$wp_query->is_category    = false;
			status_header(200);

			wp_logout();
			wp_redirect(get_bloginfo('url'), 302);
			exit();
		}
		
	}

}

$wpauth = new WpAuth();