<?php
/**
 * Class Ets_Pmpro_Utils
 *
 * Description: This class provides utility functions for the ETS PMPro Discord integration.
 *
 * @package YourPluginNamespace
 */

class Ets_Pmpro_Utils {

	/**
	 * Ets_Pmpro_Utils constructor.
	 *
	 * Description: Initializes the Ets_Pmpro_Utils class.
	 */
	public function __construct() {
	}

	/**
	 * Get General Channel ID (Static)
	 *
	 * Description: Retrieves the ID of the general channel.
	 *
	 * @return int The ID of the general channel.
	 */
	public static function get_general_channel_id( $server_id, $bot_token) {


		$user_id                  = get_current_user_id();
		if ( $server_id && $bot_token ) {
			$discod_server_channels_api = ETS_DISCORD_API_URL . 'guilds/' . $server_id . '/channels';
			$guild_args              = array(
				'method'  => 'GET',
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bot ' . $bot_token,
				),
			);
			$guild_response          = wp_remote_post( $discod_server_channels_api, $guild_args );

			ets_pmpro_discord_log_api_response( $user_id, $discod_server_channels_api, $guild_args, $guild_response );

			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );
			if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
				if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
					PMPro_Discord_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				} else {
					$response_arr['previous_mapping'] = get_option( 'ets_category_channel_mappings' );

					$discord_channels = array();
					foreach ( $response_arr as $key => $value ) {
						$is_parent = null;
						if ( is_array( $value ) ) {
							if ( array_key_exists( 'parent_id', $value ) ) {
								$is_parent = $value['parent_id'];
							}
						}
						// var_dump($value['type']);
						if ( 'previous_mapping' !== $key && array_key_exists( 'type', $value ) && $value['type'] == 0 ) {
							if ( 'previous_mapping' !== $key && $is_parent !== null && isset( $value['parent_id'] ) ) {
								$discord_channels[ $value['id'] ] = $value['name'];
							}
						}
					}

					$general_channel_name = 'general';
					if ( isset( $discord_channels ) ) {
						$normalized_channels = array_map( 'remove_accents', $discord_channels );
						$key = array_search( remove_accents( $general_channel_name ), $normalized_channels, true );
					
						if ( $key !== false ) {
							$general_channel_id = $key;
							update_option( 'ets_pmpro_discord_general_channel_id', $general_channel_id);
							return $general_channel_id;
						}
					} else {
						return null;
					}
				}
			}

			return;
		}		

	}

}


