<?php
/*
Plugin Name: NerdTools Bad Bots Spam Defender
Plugin URI: http://www.nerdtools.co.uk/badbots/
Description: Automatically denies comment access to known spammers in the NerdTools Bad Bots database. More information can be found in the readme.txt file.
Version: 1.1
Author: NerdTools
Author URI: http://www.nerdtools.co.uk/
License: GPL2
*/

// core script //
// get settings //
$enabled = get_option('enabled');
$message = get_option('message');

// check if enabled //
function nerdtools_bad_bots_spam_defender_not_enabled() {
    ?>
    <div class="error">
        <p><?php _e( 'NerdTools Bad Bots Spam Defender is installed but not enabled - click  <a href="/wp-admin/options-general.php?page=nerdtools_bad_bots_spam_defender.php">here</a> to adjust the plugin settings', 'nerdtools_bad_bots_spam_defender_not_enabled' ); ?>.</p>
    </div>
    <?php
}
if ($enabled!="1"){
// call above function if enabled not 1 //
add_action( 'admin_notices', 'nerdtools_bad_bots_spam_defender_not_enabled' );
}

function nerdtools_bad_bots_spam_defender() {
// get comment authors IP address //
$ip = $_SERVER['REMOTE_ADDR'];

// check to see if IP blacklisted //
$response = wp_remote_get("http://core.nerdtools.co.uk/badbot/wordpress/check.php?ip=$ip");

// if blacklisted return error else continue as normal //
if ($enabled=="1" && empty($message) && "1"==$response['body']) {
wp_die(__('Sorry, commenting is unavailable from your current IP address due to multiple spam reports to the NerdTools Bad Bots database.'));
} elseif ($enabled=="1" && "1"==$response['body']) {
wp_die(__(''.$message.''));
}
}

// call above function //
add_action('pre_comment_on_post', 'nerdtools_bad_bots_spam_defender');
// core script //

// settings page //
function nerdtools_bad_bots_spam_defender_menu() {
add_options_page('NerdTools Bad Bots Spam Defender', 'NT BB Spam Defender', 'manage_options', 'nerdtools_bad_bots_spam_defender.php', 'nerdtools_bad_bots_spam_defender_settings');
add_action( 'admin_init', 'register_nerdtools_bad_bots_spam_defender_settings' );
}

function register_nerdtools_bad_bots_spam_defender_settings() {
register_setting('nerdtools_badbots_spam_defender_group', 'enabled');
register_setting('nerdtools_badbots_spam_defender_group', 'message');
} 

function nerdtools_bad_bots_spam_defender_settings() {
?>
<div class="wrap">
<h2>NerdTools Bad Bots Spam Defender</h2>
<p>Thanks for installing this plugin! Below you will find the plugin settings, along with some interesting stats.
<br><br>If you would like to contribute to the Bad Bots database please consider installing the <b>NerdTools Bad Bots Spam Reporter</b> plugin found <a href="http://wordpress.org/plugins/nerdtools-bad-bots-spam-reporter/" target="_blank">here</a>.
<br><br>For support or suggestions email <b>support@nerdtools.co.uk</b>.</p>
<h3>Settings</h3>
<form method="post" action="options.php">
    <?php settings_fields('nerdtools_badbots_spam_defender_group'); ?>
    <?php do_settings_sections('nerdtools_badbots_spam_defender_group'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable Spam Defending?</th>
        <td><input type="checkbox" name="enabled" value="1" <?php $enabled = get_option('enabled'); if ($enabled=="1") { echo "checked"; } ?> /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Custom Message to Display on Bad Bot Detection</th>
        <td><textarea name="message" rows="3"cols="30"><?php $message = get_option('message'); if (empty($message)) { echo "Not set - Default message will be displayed"; } else { echo $message; } ?></textarea></td>
        </tr>    
    </table>
    <?php submit_button(); ?>
</form>
<h3>Stats</h3>
<iframe src="http://core.nerdtools.co.uk/badbot/wordpress/defender-stats.php" height="600" width="600" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe>
</div>
<?php
}
// call above function //
add_action('admin_menu', 'nerdtools_bad_bots_spam_defender_menu');
// settings page //
?>
