<?php
/**
 * Plugin Name: Connect Paid Memberships Pro to Discord
 * Plugin URI:  https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon
 * Description: Connect your PaidMebershipPro site to your discord server, enable your members to be part of your community.
 * Version: 1.2.13
 * Author: ExpressTech Software Solutions Pvt. Ltd., Strangers Studios
 * Author URI: https://www.expresstechsoftwares.com
 * Text Domain: pmpro-discord-add-on
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// create plugin version constant.
define( 'ETS_PMPRO_VERSION', '1.2.13' );

// create plugin url constant.
define( 'ETS_PMPRO_DISCORD_URL', plugin_dir_url( __FILE__ ) );

// create plugin path constant.
define( 'ETS_PMPRO_DISCORD_PATH', plugin_dir_path( __FILE__ ) );

// discord API url.
define( 'ETS_DISCORD_API_URL', 'https://discord.com/api/v10/' );

// discord Bot Permissions.
define( 'ETS_DISCORD_BOT_PERMISSIONS', 8 );

// discord api call scopes.
define( 'ETS_DISCORD_OAUTH_SCOPES', 'identify email connections guilds guilds.join gdm.join rpc rpc.notifications.read rpc.voice.read rpc.voice.write rpc.activities.write bot webhook.incoming applications.builds.upload applications.builds.read applications.commands applications.store.update applications.entitlements activities.read activities.write relationships.read' );

// define group name for action scheduler actions.
define( 'ETS_DISCORD_AS_GROUP_NAME', 'ets-pmpro-discord' );

// define interval to keep checking and send membership expiration warning DM.
define( 'ETS_PMPRO_DISOCRD_EXPIRATION_WARNING_CRON', 5 );

// Follwing response codes not cosider for re-try API calls.
define( 'ETS_PMPRO_DISCORD_DONOT_RETRY_THESE_API_CODES', array( 0, 10003, 50033, 10004, 50025, 10013, 10011 ) );

// following http response codes should not get re-try. except 429 !
define( 'ETS_PMPRO_DISCORD_DONOT_RETRY_HTTP_CODES', array( 400, 401, 403, 404, 405, 502 ) );
/**
 * Class to connect discord app
 */
class Ets_Pmpro_Add_Discord {
	function __construct() {
		// Add internal classes
		require_once ETS_PMPRO_DISCORD_PATH . 'libraries/action-scheduler/action-scheduler.php';
		require_once ETS_PMPRO_DISCORD_PATH . 'includes/functions.php';
		require_once ETS_PMPRO_DISCORD_PATH . 'includes/classes/class-pmpro-discord-admin-setting.php';
		require_once ETS_PMPRO_DISCORD_PATH . 'includes/classes/class-discord-api.php';
		require_once ETS_PMPRO_DISCORD_PATH . 'includes/classes/class-discord-addon-logs.php';
		require_once ETS_PMPRO_DISCORD_PATH . 'includes/classes/class-discord-addon-admin-notices.php';

		// initiate cron event
		register_activation_hook( __FILE__, array( $this, 'ets_pmpro_discord_set_up_plugin' ) );
	}

	/**
	 * Description: set up the plugin upon activation.
	 *
	 * @param None
	 * @return None
	 */

	public function ets_pmpro_discord_set_up_plugin() {
		$this->set_redirect_url_on_pmpro_activation();
		$this->set_default_setting_values();
		update_option( 'ets_pmpro_discord_uuid_file_name', wp_generate_uuid4() );
		wp_schedule_event( time(), 'hourly', 'ets_pmrpo_discord_schedule_expiration_warnings' );
	}

	/**
	 * To to save redirect url
	 *
	 * @param None
	 * @return None
	 */
	public function set_redirect_url_on_pmpro_activation() {
		$ets_pre_saved_url         = get_option( 'ets_pmpro_discord_redirect_url' );
		$ets_pmpro_profile_page_id = get_option( 'pmpro_member_profile_edit_page_id' );
		if ( $ets_pmpro_profile_page_id && empty( $ets_pre_saved_url ) ) {
			$ets_pmpro_discord_redirect_url = get_formated_discord_redirect_url( get_permalink( $ets_pmpro_profile_page_id ) );
			update_option( 'ets_pmpro_discord_redirect_url', $ets_pmpro_discord_redirect_url );
		}
	}
	/**
	 * Set default settings on activation
	 */
	public function set_default_setting_values() {
		update_option( 'ets_pmpro_discord_payment_failed', true );
		update_option( 'ets_pmpro_discord_log_api_response', false );
		update_option( 'ets_pmpro_retry_failed_api', true );
		update_option( 'ets_pmpro_discord_job_queue_concurrency', 1 );
		update_option( 'ets_pmpro_member_kick_out', 0 );
		update_option( 'ets_pmpro_discord_btn_color', '#77a02e' );
		update_option( 'ets_pmpro_btn_disconnect_color', '#ff0000' );
		update_option( 'ets_pmpro_discord_loggedout_btn_text', 'Connect To Discord' );
		update_option( 'ets_pmpro_discord_loggedin_btn_text', 'Connect To Discord' );
		update_option( 'ets_pmpro_disconnect_btn_text', 'Disconnect From Discord' );
		update_option( 'ets_pmpro_discord_job_queue_batch_size', 7 );
		update_option( 'ets_pmpro_allow_none_member', 'yes' );
		update_option( 'ets_pmpro_retry_api_count', '5' );
		update_option( 'ets_pmpro_discord_send_welcome_dm', true );
		update_option( 'ets_pmpro_discord_welcome_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Welcome, Your membership [MEMBERSHIP_LEVEL] is starting from [MEMBERSHIP_STARTDATE] at [SITE_URL] the last date of your membership is [MEMBERSHIP_ENDDATE] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_pmpro_discord_send_expiration_warning_dm', true );
		update_option( 'ets_pmpro_discord_expiration_warning_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] is expiring at [MEMBERSHIP_ENDDATE] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_pmpro_discord_send_membership_expired_dm', true );
		update_option( 'ets_pmpro_discord_expiration_expired_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] is expired at [MEMBERSHIP_ENDDATE] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_pmpro_discord_send_membership_cancel_dm', true );
		update_option( 'ets_pmpro_discord_cancel_message', 'Hi [MEMBER_USERNAME], ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] at [BLOG_NAME] is cancelled, Regards, [SITE_URL]' );
		update_option( 'ets_pmpro_discord_embed_messaging_feature', false );
		update_option( 'ets_pmpro_discord_data_erases', false );
	}

}
new Ets_Pmpro_Add_Discord();
