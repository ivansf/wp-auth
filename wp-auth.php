<?php 
/*
Plugin Name: WP Auth
Plugin URI: http://www.ivansotof.com/
Description: Auth functions for extending WP.
Version: 1.0
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

		wp_register_style('wp-auth', $this->path . 'css/wp-auth.css' );
		wp_enqueue_style('wp-auth');

		add_action('admin_menu', array(&$this, 'pages'), 5, __FILE__, 'wpauth_toplevel_page');

		add_action('wp', array(&$this, 'login_process') );
		add_action('wp', array(&$this, 'register_process') );
		add_action('wp', array(&$this, 'recover_process') );
		add_action('wp', array(&$this, 'logout') );

		add_shortcode('wpauth-login', array(&$this, 'shortcode_login'));
		add_shortcode('wpauth-registration', array(&$this, 'shortcode_registration'));
		add_shortcode('wpauth-recover', array(&$this, 'shortcode_recover'));

		// hiding a warning that will try to debug later.
		@$this->hide_admin_login();

		$this->hide_admin_bar();
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
		// Future Feature
		//add_submenu_page('wp_auth_admin', 'Lockdown Options','Lockdown', 'edit_pages', 
		//	'wp_auth_admin_sub', array(&$this, 'page_config'));
		// add_options_page('Save Options', 'Sub page', 'manage_options', 'save_wp_auth_options', array(&$this, 'save_wp_auth_options'));
	}

	function page_config()
	{
		if (!empty($_POST)) {
			//print_r($_POST); exit();
			update_option('wp-auth-boxstyle', $_POST['wp-auth-box-style']);
			update_option('wp-auth-buttonstyle', $_POST['wp-auth-button-style']);
			update_option('wp-auth-hide-admin', $_POST['lock-wp-admin']);
			update_option('wp-auth-hide-admin-bar', $_POST['hide-top-bar']);
		}
		// Template page.
		include ( dirname( __FILE__ ) . '/view-auth-options.php' );
	}

	// function save_wp_auth_options()
	// {
		

	// 	wp_redirect(admin_url() . 'admin.php?page=wp_auth_admin', 302);
	// 	// return 'asdasd';
	// }

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
					<label for="wp-auth-login">Username</label>
					<input type="text" name="wp-auth-login" value="">
				</p>
				<p>
					<label for="wp-auth-password">Password</label>
					<input type="password" name="wp-auth-password" value="">
				</p>
				<input type="submit" value="Login" class="submit <?php echo get_option('wp-auth-buttonstyle', 'blue') ?>">
				<p>
				<a href="#">Lost your password?</a>
				</p>
			</form>
		</div>
		<?php

	}

	function shortcode_registration()
	{
		if ($_SESSION['error_msg']) {
			echo '<div class="error_msg">' . $_SESSION['error_msg'] . '</div>';
			unset($_SESSION['error_msg']);
		}

		if (is_user_logged_in()){
			?>
			You are already logged in. You cannot create an account.
			<?php 
		} elseif ($_SESSION['success_msg']) {
			echo $_SESSION['success_msg'];
			unset($_SESSION['success_msg']);
		} else {
			?>
			<div id="wp-auth-login" class="<?php echo get_option('wp-auth-boxstyle', 'white') ?>">
				<form action="<?php echo get_bloginfo('url') ?>/register_process" method="post">
					<p>
						<label for="wp-auth-reg-username">Username</label>
						<input type="text" name="wp-auth-reg-username" value="">
					</p>
					<p>
						<label for="wp-auth-reg-email">Email</label>
						<input type="text" name="wp-auth-reg-email" value="">
					</p>
					<p>
						<label for="wp-auth-reg-password">Password</label>
						<input type="password" name="wp-auth-reg-password" value="">
					</p>
					<p>
						<label for="wp-auth-reg-password-repeat">Re-type Password</label>
						<input type="password" name="wp-auth-reg-password-repeat" value="">
					</p>
					<input type="submit" value="Register" class="submit <?php echo get_option('wp-auth-buttonstyle', 'blue') ?>">
				</form>
			</div>
			<?php
		}

	}

	function shortcode_recover()
	{

		if (is_user_logged_in()){
			?>
			You are already logged in. You cannot reset your password.
			<?php 
		} elseif ($_SESSION['success_msg']) {
			echo $_SESSION['success_msg'];
			unset($_SESSION['success_msg']);
		} else {
			?>
			<div id="wp-auth-login" class="<?php echo get_option('wp-auth-boxstyle', 'white') ?>">
				<form action="<?php echo get_bloginfo('url') ?>/recover_password_process" method="post">
					<p>
						<label for="wp-auth-recover-email">Email Address</label>
						<input type="text" name="wp-auth-recover-email" value="">
					</p>
					<input type="submit" value="Recover" class="submit <?php echo get_option('wp-auth-buttonstyle', 'blue') ?>">
				</form>
			</div>
			<?php
		}
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

			wp_redirect(get_bloginfo('url'), 302);
			exit();
		}
	}

	function register_process()
	{
		global $wp_query, $wpdb;

		$ref = $_SERVER['HTTP_REFERER'];

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

		if ($wp_query->query_vars['name'] === 'register_process') {

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

			$user_id = username_exists( $_POST['wp-auth-reg-username'] );

			// TODO: add password check.
			if ($_POST['wp-auth-reg-password'] !== $_POST['wp-auth-reg-password-repeat']) {
				$_SESSION['error_msg'] = 'Passwords don\'t match';
				wp_redirect( $ref , 302);
				exit();
			}

			if ($_POST['wp-auth-reg-password'] == '') {
				$_SESSION['error_msg'] = 'Password cannot be empty';
				wp_redirect( $ref , 302);
				exit();
			}

			if (!is_email($_POST['wp-auth-reg-email'] )) {
				$_SESSION['error_msg'] = 'The email address entered is invalid.';
				wp_redirect( $ref , 302);
				exit();
			}

			if ( !$user_id and email_exists( $_POST['wp-auth-reg-email'] ) == false ) {
				//$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id = wp_create_user( $_POST['wp-auth-reg-username'], $_POST['wp-auth-reg-password'], $_POST['wp-auth-reg-email'] );
				//print_r($user_id); exit();
				if (is_numeric($user_id) && ($user_id > 0 )) {
					$_SESSION['success_msg'] = 'User created. You can now log into the site.';
					wp_redirect( $ref , 302);
					exit();
				} else {
					$_SESSION['error_msg'] = 'Error creating the account.';
					print_r($user_id); exit();
					wp_redirect( $ref , 302);
					exit();
				}
				

			} else {
				$_SESSION['error_msg'] = 'Cannot create the account. Username or email address already in use.';
				wp_redirect( $ref , 302);
				exit();
				//$random_password = __('User already exists.  Password inherited.');
			}
		}

	}


	function recover_process()
	{
		global $wp_query, $wpdb;

		$ref = $_SERVER['HTTP_REFERER'];

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

		if ($wp_query->query_vars['name'] === 'recover_password_process') {

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

			//echo $_POST['wp-auth-recover-email']; exit();

			//if ( strpos( $_POST['wp-auth-recover-email'], '@' ) ) {
			$user_data = get_user_by( 'email', trim( $_POST['wp-auth-recover-email'] ) );
			if ( empty( $user_data ) ) {
				$_SESSION['error_msg'] = __('<strong>ERROR</strong>: There is no user registered with that email address.');
				wp_redirect( $ref , 302);
				exit();
			} 

			$user_login = $user_data->user_login;
			$user_email = $user_data->user_email;

			$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));

			do_action('retrieve_password', $user_login);

			if ( empty($key) ) {
				$key = wp_generate_password(20, false);
				do_action('retrieve_password_key', $user_login, $key);
				$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
			}
			$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
			$message .= network_home_url( '/' ) . "\r\n\r\n";
			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
			$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
			$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
			$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
			$title = __('Password Reset');

			if ( $message && !wp_mail($user_email, $title, $message) ) {
				wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );
			} else {
				$_SESSION['success_msg'] = __('Password reset link was sent to your email account.');
				wp_redirect( $ref , 302);
				exit();
			}

			// } else {
			// 	$login = trim($_POST['user_login']);
			// 	$user_data = get_user_by('login', $login);
			// }

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


	function hide_admin_login()
	{
		$current_file = end( explode('/', $_SERVER['SCRIPT_FILENAME'] ) );

		if (get_option('wp-auth-hide-admin', 'no') !== 'yes') {
			return;
		}

    	if ( in_array( $current_file, 
    		array('wp-app.php', 'async-upload.php', 'admin-ajax.php') ) ) 	{
			return;
		}
    	
		if ( !is_admin() && $current_file !== 'wp-login.php') {
			return;
		}

		// We only try to hide it for non logged in users.
		if ( !is_user_logged_in() )	{
			
			$template = get_query_template('404');
			if ( empty($template) || !file_exists($template) ) {
				$template = WP_CONTENT_DIR . '/themes/twentyeleven/404.php';
				if (!file_exists($template))
					wp_die('404 - File not found!', '', array('response' => 404));
			}

			// returning the right status.
			status_header(404);
			
			// we render and we are done!
			require_once( $template );
			exit;
		}

	}

	function hide_admin_bar()
	{
		$user = wp_get_current_user();
		if (get_option('wp-auth-hide-admin-bar', 'no') !== 'yes') 
			return;
		if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $role )
				if ($role === 'subscriber') show_admin_bar(false);
		}
	}
}

// adding an instance of our awesome class.
function start_wp_auth()
{
	$wpauth = new WpAuth();
}

// hooking Wp Auth to the init.
add_action('init', 'start_wp_auth');


// adding our widget too!
require plugin_dir_path(__FILE__) . 'wp-auth-widget.php';



