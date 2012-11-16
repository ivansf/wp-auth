<div class="wrap">
	<form method="post" action="<?php echo admin_url() ?>admin.php?page=wp_auth_admin">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>WP Auth Options</h2>

			<?php //echo get_option('wp-auth-boxstyle', 'white') ?>
			<?php //echo get_option('wp-auth-buttonstyle', 'blue') ?>
			<?php $box_style = get_option('wp-auth-boxstyle', 'white') ?>
			<?php $button_style = get_option('wp-auth-buttonstyle', 'blue') ?>
			<?php $wp_hide_admin = get_option('wp-auth-hide-admin', 'no'); //echo $wp_hide_admin; exit; ?>
			<?php $wp_hide_admin_bar = get_option('wp-auth-hide-admin-bar', 'no'); //echo $wp_hide_admin; exit; ?>

		<h2>Instructions</h2>

		<p>To create a login form, place the shortcode <code>[wpauth-login]</code> into any page or post.</p>
		<p>To create a registration form, place the shortcode <code>[wpauth-registration]</code> into any page or post.</p>
		<p>To create a password recovery form, place the shortcode <code>[wpauth-recover]</code> into any page or post.</p>


		<h2>Theme</h2>
		<table class="form-table">
			<tbody>
				<!--
				<tr>
					<th scope="row">
						Box Style

					</th>
					<td>
						<select name="wp-auth-box-style">
							
							<option value="light" <?php if ($box_style == 'light') echo 'selected' ?> >Light</option>
							<option value="dark" <?php if ($box_style == 'dark') echo 'selected' ?> >Dark</option>
						</select>
						<p class="description">Box surrounding the login, registration and password recovery box.</p>
					</td> 
				</tr>-->
				<tr>
					<th scope="row">
						Button Style
					</th>
					<td>
						<select name="wp-auth-button-style">
							<option value="blue" <?php if ($box_style == 'blue') echo 'selected' ?> >Blue</option>
							<option value="light" <?php if ($button_style == 'light') echo 'selected' ?> >Light</option>
							<option value="dark" <?php if ($button_style == 'dark') echo 'selected' ?> >Dark</option>
						</select>
						<p class="description">Theme developers can target these classes in order to override button themes.</p>
					</td>
				</tr>
			</tbody>
		</table>
		


		<h2>Tips &amp; Tricks</h2>

		<p>To create logout link, simple create anywhere a link to: <code>/logout</code> </p>

		<p>WP Auth offers an option to block access to <strong>/wp-admin</strong> and <strong>/wp-login.php</strong>. 
			With this, the only way to access your site will be by using a page with the login shortcode on it.</p>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">Admin protection</th>
					<td>
						<input type="checkbox" name="lock-wp-admin" value="yes"
							<?php echo $wp_hide_admin ?  'checked' : '';  ?>
						 > Hide admin pages when not logged in.
						<p class="description">This option will hide wp-admin/ and wp-login.php for users that are not logged in.</p>
						<p class="description"><strong>Warning:</strong> Make sure you add a login page using the shortcode or the widget before enabling this option.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Admin Bar</th>
					<td>
						<input type="checkbox" name="hide-top-bar" value="yes"
							<?php echo $wp_hide_admin_bar ?  'checked' : '';  ?>
						> Hides the admin bar for users with Subscriber role.
						<p class="description">Hides admin top bar for subscribers.</p>
					</td>
				</tr>
			</tbody>
		</table>

		<p>
		<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
		<div class="clear">&nbsp;</div>
		A plugin made <a href="http://www.byrobots.com/?utm_source=wpauth&utm_medium=link&utm_campaign=WP%2BAuth" target="_blank">byRobots</a>
		</p>
	</form>
</div>