<div class="wrap">
	<form method="post" action="<?php echo admin_url() ?>admin.php?page=save_wp_auth_options&noheader=true">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>WP Auth Options</h2>

		<pre>
			<?php echo get_option('wp-auth-boxstyle', 'white') ?>
			<?php echo get_option('wp-auth-buttonstyle', 'blue') ?>
			

			<?php $box_style = get_option('wp-auth-boxstyle', 'white') ?>
			<?php $button_style = get_option('wp-auth-buttonstyle', 'blue') ?>
		</pre>

		<h3>Instructions</h3>

		<p>To create a login page, place the shortcode [wpauth-login] into any page, widget or post.</p>


		<h3>Theme</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						Box Style
					</th>
					<td>
						<select name="wp-auth-box-style">
							
							<option value="light" <?php if ($box_style == 'light') echo 'selected' ?> >Light</option>
							<option value="dark" <?php if ($box_style == 'dark') echo 'selected' ?> >Dark</option>
						</select>
					</td>
				</tr>
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
					</td>
				</tr>
			</tbody>
		</table>
		<p>To create logout link, simple create anywhere a link to /logout</p>

		<h3>Options</h3>

		<p>WP Auth offers an option to block access to <strong>/wp-admin</strong> and <strong>/wp-login.php</strong>. 
			With this, the only way to access your site will be by using a page with the login shortcode on it.</p>

		<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">

	</form>
</div>