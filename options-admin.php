<?php
// functions for managing Shibboleth options through the WordPress administration panel

add_action('admin_menu', 'shibboleth_admin_panels');

/**
 * Setup admin menus for Shibboleth options.
 *
 * @action: admin_menu
 **/
function shibboleth_admin_panels() {
	// global options page
	if (isset($GLOBALS['wpmu_version'])) {
		$hookname = add_submenu_page('wpmu-admin.php', __('Shibboleth Options', 'shibboleth'), 'Shibboleth', 8, 'shibboleth-options', 'shibboleth_options_page' );
	} else {
		$hookname = add_options_page(__('Shibboleth options', 'shibboleth'), 'Shibboleth', 8, 'shibboleth-options', 'shibboleth_options_page' );
	}

	add_contextual_help($hookname, shibboleth_help_text());
}


/**
 * Add Shibboleth links to the "help" pull down panel.
 */
function shibboleth_help_text() {
	$text = '
	<ul>
		<li><a href="https://spaces.internet2.edu/display/SHIB/" target="_blank">Shibboleth 1.3 Wiki</a></li>
		<li><a href="https://spaces.internet2.edu/display/SHIB2/" target="_blank">Shibboleth 2 Wiki</a></li>
		<li><a href="http://shibboleth.internet2.edu/lists.html" target="_blank">Shibboleth Mailing Lists</a></li>
	</ul>';

	return $text;
}


/**
 * WordPress options page to configure the Shibboleth plugin.
 *
 * @uses apply_filters() Calls 'shibboleth_plugin_path'
 */
function shibboleth_options_page() {
	global $wp_roles;

	if (isset($_POST['submit'])) {
		check_admin_referer('shibboleth_update_options');

		$shib_headers = (array) shibboleth_get_option('shibboleth_headers');
		$shib_headers = array_merge($shib_headers, $_POST['headers']);
		shibboleth_update_option('shibboleth_headers', $shib_headers);

		$shib_roles = (array) shibboleth_get_option('shibboleth_roles');
		$shib_roles = array_merge($shib_roles, $_POST['shibboleth_roles']);
		shibboleth_update_option('shibboleth_roles', $shib_roles);

		shibboleth_update_option('shibboleth_login_url', $_POST['login_url']);
		shibboleth_update_option('shibboleth_logout_url', $_POST['logout_url']);
		shibboleth_update_option('shibboleth_password_change_url', $_POST['password_change_url']);
		shibboleth_update_option('shibboleth_password_reset_url', $_POST['password_reset_url']);
		shibboleth_update_option('shibboleth_default_login', (boolean) $_POST['default_login']);
		shibboleth_update_option('shibboleth_update_users', (boolean) $_POST['update_users']);
		shibboleth_update_option('shibboleth_update_roles', (boolean) $_POST['update_roles']);
	}

	$shib_headers = shibboleth_get_option('shibboleth_headers');
	$shib_roles = shibboleth_get_option('shibboleth_roles');

	$shibboleth_plugin_path = apply_filters('shibboleth_plugin_path', plugins_url('shibboleth'));

	screen_icon('shibboleth');

?>
	<style type="text/css">
		#icon-shibboleth { background: url("<?php echo $shibboleth_plugin_path . '/icon.png' ?>") no-repeat; height: 36px width: 36px; }
	</style>

	<div class="wrap">
		<form method="post">

			<h2><?php _e('Shibboleth Options', 'shibboleth') ?></h2>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="login_url"><?php _e('Session Initiator URL', 'shibboleth') ?></label</th>
					<td>
						<input type="text" id="login_url" name="login_url" value="<?php echo shibboleth_get_option('shibboleth_login_url') ?>" size="50" /><br />
						<?php _e('This URL is constructed from values found in your main Shibboleth'
							. ' SP configuration file: your site hostname, the Sessions handlerURL,'
							. ' and the SessionInitiator Location.', 'shibboleth'); ?>
						<br /><?php _e('Wiki Documentation', 'shibboleth') ?>: 
						<a href="https://spaces.internet2.edu/display/SHIB/SessionInitiator" target="_blank">Shibboleth 1.3</a> |
						<a href="https://spaces.internet2.edu/display/SHIB2/NativeSPSessionInitiator" target="_blank">Shibboleth 2</a>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="logout_url"><?php _e('Logout URL', 'shibboleth') ?></label</th>
					<td>
						<input type="text" id="logout_url" name="logout_url" value="<?php echo shibboleth_get_option('shibboleth_logout_url') ?>" size="50" /><br />
						<?php _e('This URL is constructed from values found in your main Shibboleth'
							. ' SP configuration file: your site hostname, the Sessions handlerURL,'
							. ' and the LogoutInitiator Location (also known as the'
							. ' SingleLogoutService Location in Shibboleth 1.3).', 'shibboleth'); ?>
						<br /><?php _e('Wiki Documentation', 'shibboleth') ?>: 
						<a href="https://spaces.internet2.edu/display/SHIB/SPMainConfig" target="_blank">Shibboleth 1.3</a> |
						<a href="https://spaces.internet2.edu/display/SHIB2/NativeSPLogoutInitiator" target="_blank">Shibboleth 2</a>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="password_change_url"><?php _e('Password Change URL', 'shibboleth') ?></label</th>
					<td>
						<input type="text" id="password_change_url" name="password_change_url" value="<?php echo shibboleth_get_option('shibboleth_password_change_url') ?>" size="50" /><br />
						<?php _e('If this option is set, Shibboleth users will see a "change password" link on their profile page directing them to this URL.', 'shibboleth') ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="password_reset_url"><?php _e('Password Reset URL', 'shibboleth') ?></label</th>
					<td>
						<input type="text" id="password_reset_url" name="password_reset_url" value="<?php echo shibboleth_get_option('shibboleth_password_reset_url') ?>" size="50" /><br />
						<?php _e('If this option is set, Shibboleth users who try to reset their forgotten password using WordPress will be redirected to this URL.', 'shibboleth') ?>
					</td>
				</tr>
				<tr>
				<th scope="row"><label for="default_login"><?php _e('Shibboleth is default login', 'shibboleth') ?></label></th>
					<td>
						<input type="checkbox" id="default_login" name="default_login" <?php echo shibboleth_get_option('shibboleth_default_login') ? ' checked="checked"' : '' ?> />
						<label for="default_login"><?php _e('Use Shibboleth as the default login method for users.', 'shibboleth'); ?></label>

						<p><?php _e('If set, this will cause all standard WordPress login links to initiate Shibboleth'
							. ' login instead of local WordPress authentication.  Shibboleth login can always be'
							. ' initiated from the WordPress login form by clicking the "Login with Shibboleth" link.', 'shibboleth'); ?></p>
					</td>
				</tr>
			</table>

			<br class="clear" />

			<h3><?php _e('User Profile Data', 'shibboleth') ?></h3>

			<p><?php _e('Define the Shibboleth headers which should be mapped to each user profile attribute.  These'
				. ' header names are configured in <code>attribute-map.xml</code> (for Shibboleth 2.x) or'
				. ' <code>AAP.xml</code> (for Shibboleth 1.x).', 'shibboleth') ?></p>

			<p>
				<?php _e('Wiki Documentation', 'shibboleth') ?>: 
				<a href="https://spaces.internet2.edu/display/SHIB/AttributeAcceptancePolicy" target="_blank">Shibboleth 1.3</a> |
				<a href="https://spaces.internet2.edu/display/SHIB2/NativeSPAddAttribute" target="_blank">Shibboleth 2</a>
			</p>

			<table class="form-table optiontable editform" cellspacing="2" cellpadding="5" width="100%">
				<tr valign="top">
					<th scope="row"><label for="username"><?php _e('Username') ?></label</th>
					<td><input type="text" id="username" name="headers[username]" value="<?php echo $shib_headers['username'] ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="first_name"><?php _e('First name') ?></label</th>
					<td><input type="text" id="first_name" name="headers[first_name]" value="<?php echo $shib_headers['first_name'] ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="last_name"><?php _e('Last name') ?></label</th>
					<td><input type="text" id="last_name" name="headers[last_name]" value="<?php echo $shib_headers['last_name'] ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nickname"><?php _e('Nickname') ?></label</th>
					<td><input type="text" id="nickname" name="headers[nickname]" value="<?php echo $shib_headers['nickname'] ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="display_name"><?php _e('Display name') ?></label</th>
					<td><input type="text" id="display_name" name="headers[display_name]" value="<?php echo $shib_headers['display_name'] ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="email"><?php _e('Email Address') ?></label</th>
					<td><input type="text" id="email" name="headers[email]" value="<?php echo $shib_headers['email'] ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="update_users"><?php _e('Update User Data', 'shibboleth') ?></label</th>
					<td>
						<input type="checkbox" id="update_users" name="update_users" <?php echo shibboleth_get_option('shibboleth_update_users') ? ' checked="checked"' : '' ?> />
						<label for="update_users"><?php _e('Use Shibboleth data to update user profile data each time the user logs in.', 'shibboleth'); ?></label>

						<p><?php _e('This will prevent users from being able to manually update these'
							. ' fields.  Note that Shibboleth data is always used to populate the user'
							. ' profile during account creation.', 'shibboleth'); ?></p>

					</td>
				</tr>
			</table>

			<br class="clear" />

			<h3><?php _e('User Role Mappings', 'shibboleth') ?></h3>

			<p><?php _e('Users can be placed into one of WordPress\'s internal roles based on any'
				. ' attribute.  For example, you could define a special eduPersonEntitlement value'
				. ' that designates the user as a WordPress Administrator.  Or you could automatically'
				. ' place all users with an eduPersonAffiliation of "faculty" in the Author role.', 'shibboleth'); ?></p>

			<p><?php _e('<strong>Current Limitations:</strong> While WordPress supports users having'
				. ' multiple roles, the Shibboleth plugin will only place the user in the highest ranking'
				. ' role.  Only a single header/value pair is supported for each user role.  This may be'
				. ' expanded in the future to support multiple header/value pairs or regular expression'
				. ' values.  In the meantime, you can use the <em>shibboleth_roles</em> and'
				. ' <em>shibboleth_user_role</em> WordPress filters to provide your own logic for assigning'
				. ' user roles.', 'shibboleth'); ?></p>

			<style type="text/css">
				#role_mappings { padding: 0; }
				#role_mappings thead th { padding: 5px 10px; }
				#role_mappings td, #role_mappings th { border-bottom: 0px; }
			</style>

			<table class="form-table optiontable editform" cellspacing="2" cellpadding="5" width="100%">

				<tr>
					<th scope="row"><?php _e('Role Mappings', 'shibboleth') ?></th>
					<td id="role_mappings">
						<table id="">
						<col width="10%"></col>
						<col></col>
						<col></col>
						<thead>
							<tr>
								<th></th>
								<th scope="column"><?php _e('Header Name', 'shibboleth') ?></th>
								<th scope="column"><?php _e('Header Value', 'shibboleth') ?></th>
							</tr>
						</thead>
						<tbody>
<?php

					foreach ($wp_roles->role_names as $key => $name) {
						echo'
						<tr valign="top">
							<th scope="row">' . _c($name) . '</th>
							<td><input type="text" id="role_'.$key.'_header" name="shibboleth_roles['.$key.'][header]" value="' . @$shib_roles[$key]['header'] . '" style="width: 100%" /></td>
							<td><input type="text" id="role_'.$key.'_value" name="shibboleth_roles['.$key.'][value]" value="' . @$shib_roles[$key]['value'] . '" style="width: 100%" /></td>
						</tr>';
					}
?>

						</tbody>
						</table>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e('Default Role', 'shibboleth') ?></th>
					<td>
						<select id="default_role" name="shibboleth_roles[default]">
						<option value=""><?php _e('(none)') ?></option>
<?php
			foreach ($wp_roles->role_names as $key => $name) {
				echo '
						<option value="' . $key . '"' . ($shib_roles['default'] == $key ? ' selected="selected"' : '') . '>' . _c($name) . '</option>';
			}
?>
						</select>

						<p><?php _e('If a user does not map into any of the roles above, they will'
							. ' be placed into the default role.  If there is no default role, the'
							. ' user will not be able to login with Shibboleth.', 'shibboleth'); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="update_roles"><?php _e('Update User Roles', 'shibboleth') ?></label></th>
					<td>
						<input type="checkbox" id="update_roles" name="update_roles" <?php echo shibboleth_get_option('shibboleth_update_roles') ? ' checked="checked"' : '' ?> />
						<label for="update_roles"><?php _e('Use Shibboleth data to update user role mappings each time the user logs in.', 'shibboleth') ?></label>

						<p><?php _e('Be aware that if you use this option, you should <strong>not</strong> update user roles manually,'
						. ' since they will be overwritten from Shibboleth the next time the user logs in.  Note that Shibboleth data'
					   	. ' is always used to populate the initial user role during account creation.', 'shibboleth') ?></p>

					</td>
				</tr>
			</table>


			<?php wp_nonce_field('shibboleth_update_options') ?>
			<p class="submit"><input type="submit" name="submit" value="<?php _e('Update Options') ?>" /></p>
		</form>
	</div>

<?php
}