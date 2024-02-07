<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.expresstechsoftwares.com/
 * @since      1.0.0
 */
// If uninstall not called from WordPress, then exit.
if ( defined( 'WP_UNINSTALL_PLUGIN' )
		&& $_REQUEST['plugin'] == 'pmpro-discord-add-on/pmpro-discord.php'
		&& $_REQUEST['slug'] == 'pmpro-discord-add-on'
	&& wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'updates' )
  ) {
	$ets_pmpro_discord_data_erases = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_data_erases' ) ) );
	if ( $ets_pmpro_discord_data_erases == true ) {
		global $wpdb;
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "usermeta WHERE `meta_key` LIKE '_ets_pmpro_discord%'" );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE `option_name` LIKE 'ets_pmpro_discord_%'" );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE `option_name` LIKE 'ets_pmpor_discord_%'" );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE `option_name` LIKE '_ets_pmpro_discord_%'" );
	}
}

