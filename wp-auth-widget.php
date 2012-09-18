<?php 

class wp_auth_widget extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'WP Auth Login Box' );
	}

	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		if (!is_user_logged_in()):
			
		?>
			<div id="wp-auth-login-widget">
				<?php echo $args['before_title'] . apply_filters('widget_title', 'Login') . $args['after_title']; ?>
				<div id="wp-auth-login-widget">
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
					</form>
				</div>
			</div>
		<?php
		else: ?>
			<div id="wp-auth-login-widget">
				<?php echo $args['before_title'] . apply_filters('widget_title', 'My Account') . $args['after_title']; ?>
				<div id="wp-auth-logged-in">
					<div class="profile-pic">
						<?php echo get_avatar( wp_get_current_user()->data->user_email, '80' ); ?>
					</div>
					<div class="logged-msg">
						Logged in as: <br>
						<strong><?php echo wp_get_current_user()->data->user_nicename ?></strong><br>
						<a href="<?php echo get_bloginfo('url') ?>/logout" class="submit <?php echo get_option('wp-auth-buttonstyle', 'blue') ?>">Logout</a>
					</div>
				</div>
			</div>
		<?php endif;
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
	}

	function form( $instance ) {
		// Output admin widget options form
	}
}

function wp_auth_widget_register() {
	register_widget( 'wp_auth_widget' );
}

add_action( 'widgets_init', 'wp_auth_widget_register' );