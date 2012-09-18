<div class="wrap">
	<form method="post" action="/">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>WP Auth Options</h2>

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
						<select name="wp-auth-style">
							<option>White - Default</option>
							<option>Dark</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						Button Style
					</th>
					<td>
						<select name="wp-auth-style">
							<option>White - Default</option>
							<option>Dark</option>
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