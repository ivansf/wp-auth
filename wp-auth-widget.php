<?php 

class wp_auth_widget extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'WP Auth Login Box' );
	}

	function widget( $args, $instance ) {
		?>
		<div id="wp-auth-login-widget">
			<form action="<?php echo get_bloginfo('url') ?>/login_process" method="post">
				<p>
					<label for="">Username</label> <br>
					<input type="text" name="wp-auth-login" value="">
				</p>
				<p>
					<label for="">Password</label> <br>
					<input type="password" name="wp-auth-password" value="">
				</p>
				<input type="submit" value="Login" class="submit">
			</form>
		</div>

		<?php
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