<?php
/**
 * Admin setting
 */
class Ets_Pmpro_Admin_Setting {
	function __construct() {
		// Add new menu option in the admin menu.
		add_action( 'admin_menu', array( $this, 'ets_pmpro_discord_add_new_menu' ) );
		// Add script for back end.
		add_action( 'admin_enqueue_scripts', array( $this, 'ets_pmpro_discord_add_admin_script' ) );

		// Add script for front end.
		add_action( 'admin_enqueue_scripts', array( $this, 'ets_pmpro_discord_add_script' ) );

		// Add script for front end.
		add_action( 'wp_enqueue_scripts', array( $this, 'ets_pmpro_discord_add_script' ) );

		// Add new button in pmpro profile
		add_shortcode( 'discord_connect_button', array( $this, 'ets_pmpro_discord_add_connect_discord_button' ) );
		// Add new shortcode to fetch and display the Discord username, account name, and roles linked to a user's membership
		add_shortcode( 'discord_user_info', array( $this, 'ets_pmpro_discord_display_user_info' ) );


		add_action( 'pmpro_show_user_profile', array( $this, 'ets_pmpro_show_discord_button' ) );

		add_action( 'wp_body_open', array( $this, 'ets_pmpro_discord_add_inline_css_checkout' ) );

		// change hook call on cancel and change
		add_action( 'pmpro_after_change_membership_level', array( $this, 'ets_pmpro_discord_as_schdule_job_pmpro_cancel' ), 10, 3 );

		// Pmpro expiry
		add_action( 'pmpro_membership_post_membership_expiry', array( $this, 'ets_pmpro_discord_as_schdule_job_pmpro_expiry' ), 10, 2 );

		add_action( 'admin_post_pmpro_discord_save_application_details', array( $this, 'ets_pmpro_discord_save_application_details' ), 10 );

		add_action( 'admin_post_pmpro_discord_save_role_mapping', array( $this, 'ets_pmpro_discord_save_role_mapping' ), 10 );

		add_action( 'admin_post_pmpro_discord_save_advance_settings', array( $this, 'ets_pmpro_discord_save_advance_settings' ), 10 );

		add_action( 'admin_post_pmpro_discord_save_appearance_settings', array( $this, 'ets_pmpro_discord_save_appearance_settings' ), 10 );

		add_action( 'pmpro_delete_membership_level', array( $this, 'ets_pmpro_discord_as_schedule_job_pmpro_level_deleted' ), 10, 1 );

		add_action( 'pmpro_checkout_after_pricing_fields', array( $this, 'ets_pmpro_discord_checkout_after_email' ) );

		// add_action( 'pmpro_checkout_after_user_fields', array( $this, 'ets_pmpro_discord_checkout_after_email' ) );

		add_filter( 'pmpro_manage_memberslist_custom_column', array( $this, 'ets_pmpro_discord_pmpro_extra_cols_body' ), 10, 2 );

		add_filter( 'pmpro_manage_memberslist_columns', array( $this, 'ets_pmpro_discord_manage_memberslist_columns' ) );

		add_filter( 'action_scheduler_queue_runner_batch_size', array( $this, 'ets_pmpro_discord_queue_batch_size' ) );

		add_filter( 'action_scheduler_queue_runner_concurrent_batches', array( $this, 'ets_pmpro_discord_concurrent_batches' ) );

		add_filter( 'pmpro_change_level', array( $this, 'ets_pmpro_discord_handle_cancel_on_next_payment' ), 99, 4 );

		add_filter( 'ets_pmpro_show_connect_button_on_profile', array( $this, 'ets_pmpro_discord_show_connect_button_on_profile' ), 10, 1 );

		add_filter( 'manage_users_columns', array( $this, 'ets_pmpro_discord_add_discord_connected_account' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'ets_pmpro_discord_discord_connected_account' ), 99, 3 );

		add_action( 'wp_ajax_ets_pmpro_discord_notice_dismiss', array( $this, 'ets_pmpro_discord_notice_dismiss' ) );
	}
	/**
	 * set action scheuduler concurrent batches number
	 *
	 * @param INT $batch_size
	 * @return INT $batch_size
	 */
	public function ets_pmpro_discord_concurrent_batches( $batch_size ) {
		if ( ets_pmpro_discord_get_all_pending_actions() !== false ) {
			return absint( get_option( 'ets_pmpro_discord_job_queue_concurrency' ) );
		} else {
			return $batch_size;
		}
	}
	/**
	 * set action scheuduler batch size.
	 *
	 * @param INT $concurrent_batches
	 * @return INT $concurrent_batches
	 */
	public function ets_pmpro_discord_queue_batch_size( $concurrent_batches ) {
		if ( ets_pmpro_discord_get_all_pending_actions() !== false ) {
			return absint( get_option( 'ets_pmpro_discord_job_queue_batch_size' ) );
		} else {
			return $concurrent_batches;
		}
	}
	/**
	 * Add button to make connection in between user and discord
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_connect_discord_button() {
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		wp_enqueue_script( 'ets_pmpro_add_discord_script' );
		$user_id = sanitize_text_field( trim( get_current_user_id() ) );

		$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );

		$allow_none_member              = sanitize_text_field( trim( get_option( 'ets_pmpro_allow_none_member' ) ) );
		$default_role                   = sanitize_text_field( trim( get_option( '_ets_pmpro_discord_default_role_id' ) ) );
		$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_pmpor_discord_role_mapping' ), true );
		$all_roles                      = unserialize( get_option( 'ets_pmpro_discord_all_roles' ) );
		$roles_color                    = unserialize( get_option( 'ets_pmpro_discord_roles_color' ) );
		$btn_color                      = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_btn_color' ) ) );
		$ets_pmpro_btn_disconnect_color = sanitize_text_field( trim( get_option( 'ets_pmpro_btn_disconnect_color' ) ) );
		$loggedout_btn_text             = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_loggedout_btn_text' ) ) );
		$loggedin_btn_text              = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_loggedin_btn_text' ) ) );
		$ets_pmpro_disconnect_btn_text  = sanitize_text_field( trim( get_option( 'ets_pmpro_disconnect_btn_text' ) ) );
		$role_will_assign_text          = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_role_will_assign_text' ) ) );
		$role_assigned_text             = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_role_assigned_text' ) ) );
		
		if ( $btn_color == '' || empty( $btn_color ) ) {
			$btn_color = '#77a02e';
		}
		if ( $ets_pmpro_btn_disconnect_color == '' || empty( $ets_pmpro_btn_disconnect_color ) ) {
			$ets_pmpro_btn_disconnect_color = '#ff0000';
		}
		if ( $loggedout_btn_text == '' || empty( $loggedout_btn_text ) ) {
			$loggedout_btn_text = 'Login With Discord';
		}
		if ( $loggedin_btn_text == '' || empty( $loggedin_btn_text ) ) {
			$loggedin_btn_text = 'Connect To Discord';
		}
		if ( $ets_pmpro_disconnect_btn_text == '' || empty( $ets_pmpro_disconnect_btn_text ) ) {
			$ets_pmpro_disconnect_btn_text = 'Disconnect From Discord';
		}

		if ( isset( $_GET['level'] ) && $_GET['level'] > 0 ) {
			$curr_level_id = $_GET['level'];
		} else {
			  $curr_level_ids = ets_pmpro_discord_get_current_level_ids( $user_id );
		}

		$mapped_role_name = '';

		if ( is_array( $curr_level_ids ) && is_array( $all_roles ) ) {
			foreach ( $curr_level_ids as $curr_level_id ) {
				if ( is_array( $ets_pmpor_discord_role_mapping ) && array_key_exists( 'pmpro_level_id_' . $curr_level_id, $ets_pmpor_discord_role_mapping ) ) {
					$mapped_role_id = $ets_pmpor_discord_role_mapping[ 'pmpro_level_id_' . $curr_level_id ];
					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						$mapped_role_name .= '<span> <i style="background-color:#' . dechex( $roles_color[ $mapped_role_id ] ) . '"></i>' . $all_roles[ $mapped_role_id ] . '</span>';
					}
				}
			}
		}

		$default_role_name = '';
		if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
						$default_role_name = '<span> <i style="background-color:#' . dechex( $roles_color[ $default_role ] ) . '">' . $all_roles[ $default_role ] . '</i></span>';
		}
		$pmpro_connecttodiscord_btn = '';
		if ( Check_saved_settings_status() ) {
			if ( $access_token ) {
				$discord_user_name           = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_username', true ) ) );
				$pmpro_connecttodiscord_btn .= '<div><label class="ets-connection-lbl">' . esc_html__( 'Discord connection', 'pmpro-discord-add-on' ) . '</label>';
				$pmpro_connecttodiscord_btn .= '<style>.pmpro-btn-disconnect{background-color: ' . $ets_pmpro_btn_disconnect_color . ';}</style><a href="#" class="ets-btn pmpro-btn-disconnect" id="pmpro-disconnect-discord" data-user-id="' . esc_attr( $user_id ) . '">' . esc_html( $ets_pmpro_disconnect_btn_text ) . '<i class="fab fa-discord"></i></a>';
				$pmpro_connecttodiscord_btn .= '<span class="ets-spinner"></span><p class="ets_assigned_role">';
				if ( $mapped_role_name || $default_role_name ) {
					$pmpro_connecttodiscord_btn .= $role_assigned_text . ' ';
				}
				if ( $mapped_role_name ) {
					$pmpro_connecttodiscord_btn .= ets_pmpro_discord_allowed_html( $mapped_role_name );
				}
				if ( $default_role_name && $mapped_role_name ) {
					// $pmpro_connecttodiscord_btn .= ' , ';
				}
				if ( $default_role_name ) {
					$pmpro_connecttodiscord_btn .= ets_pmpro_discord_allowed_html( $default_role_name );
				}
				$pmpro_connecttodiscord_btn .= '</p><p class="ets_assigned_role">';
				$pmpro_connecttodiscord_btn .= esc_html__( 'Connected account: ' . $discord_user_name, 'memberpress-discord-add-on' );
				$pmpro_connecttodiscord_btn .= '</p></div>';
			} elseif ( pmpro_hasMembershipLevel() || $allow_none_member == 'yes' ) {
				$btn_text = $user_id ? $loggedin_btn_text : $loggedout_btn_text;

				$current_url                 = ets_pmpro_discord_get_current_screen_url();
				$pmpro_connecttodiscord_btn .= '<style>.pmpro-btn-connect{background-color: ' . $btn_color . ';}</style><div><label class="ets-connection-lbl">' . esc_html__( 'Discord connection', 'pmpro-discord-add-on' ) . '</label>';
				$pmpro_connecttodiscord_btn .= '<a href="?action=discord-login&url=' . $current_url . '" class="pmpro-btn-connect ets-btn" >' . esc_html( $btn_text ) . '<i class="fab fa-discord"></i></a>';
				$pmpro_connecttodiscord_btn .= '<p class="ets_assigned_role">';
				if ( $mapped_role_name || $default_role_name ) {
					$pmpro_connecttodiscord_btn .= $role_will_assign_text. ' ';
				}
				if ( $mapped_role_name ) {
					$pmpro_connecttodiscord_btn .= ets_pmpro_discord_allowed_html( $mapped_role_name );
				}
				if ( $default_role_name && $mapped_role_name ) {
					// $pmpro_connecttodiscord_btn .= ' , ';
				}
				if ( $default_role_name ) {
					$pmpro_connecttodiscord_btn .= ets_pmpro_discord_allowed_html( $default_role_name );
				}
				$pmpro_connecttodiscord_btn .= '</p></div>';

			}
		}
		return $pmpro_connecttodiscord_btn;

	}

	/**
	 * Fetch and display the Discord username, WordPress account name, and roles linked to a user's membership.
	 *
	 * @return string|null The formatted HTML string containing the Discord username, WordPress account name, and roles.
	 */
	public function ets_pmpro_discord_display_user_info(){
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return 'You must be logged in to view this information.';
		}
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );

		$discord_user_name 				= sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_username', true ) ) );
		$wp_account_name 				= get_the_author_meta('display_name', $user_id);
		$all_roles                      = unserialize( get_option( 'ets_pmpro_discord_all_roles' ) );
		$roles_color 					= unserialize( get_option( 'ets_pmpro_discord_roles_color' ) );
		$default_role 					= sanitize_text_field( trim( get_option( '_ets_pmpro_discord_default_role_id' ) ) );
		$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_pmpor_discord_role_mapping' ), true );
		$curr_level_ids 				= ets_pmpro_discord_get_current_level_ids( $user_id );

		if ( substr( $discord_user_name,-2) === '#0'){
			$discord_user_name = substr( $discord_user_name, 0, -2 );
		}
		$mapped_role_name = '';


		if ( is_array( $curr_level_ids ) && is_array( $all_roles ) ) {
			foreach ( $curr_level_ids as $curr_level_id ) {
				if ( is_array( $ets_pmpor_discord_role_mapping ) && array_key_exists( 'pmpro_level_id_' . $curr_level_id, $ets_pmpor_discord_role_mapping ) ) {
					$mapped_role_id = $ets_pmpor_discord_role_mapping[ 'pmpro_level_id_' . $curr_level_id ];
					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						$mapped_role_name .= '<span> <i style="background-color:#' . dechex( $roles_color[ $mapped_role_id ] ) . '"></i>' . $all_roles[ $mapped_role_id ] . '</span>';
					}
				}
			}
		}

		$default_role_name = '';
		if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = '<span class="discord-role"> <i style="background-color:#' . dechex( $roles_color[ $default_role ] ) . '"></i>' . $all_roles[ $default_role ] . '</span>';
		}

		$output = '<div class="discord-user-info">';
		$output .= '<p><strong>Account Name:</strong> ' . esc_html($wp_account_name) . '</p>';
		$output .= '<p><strong>Discord Username:</strong> ' . esc_html($discord_user_name) . '</p>';
		if ( $mapped_role_name ) {
			$output .= '<p><strong>Assigned Discord Role:</strong> ' . ets_pmpro_discord_allowed_html( $mapped_role_name ) . '</p>';
		}
		if ( $default_role_name ) {
			$output .= '<p><strong>Default Discord Role:</strong> ' . ets_pmpro_discord_allowed_html( $default_role_name ) . '</p>';
		}
		$output .= '</div>';

		return $output;
	}


	/**
	 * Show status of PMPro connection with user
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_show_discord_button() {
		$show = apply_filters( 'ets_pmpro_show_connect_button_on_profile', true );
		if ( $show ) {
			echo do_shortcode( '[discord_connect_button]' );
		}
	}

	/**
	 * Method to queue all members into cancel job when pmpro level is deleted.
	 *
	 * @param INT $level_id
	 * @return NONE
	 */
	public function ets_pmpro_discord_as_schedule_job_pmpro_level_deleted( $level_id ) {
		global $wpdb;
		$result                         = $wpdb->get_results( $wpdb->prepare( 'SELECT `user_id` FROM ' . $wpdb->prefix . 'pmpro_memberships_users' . ' WHERE `membership_id` = %d GROUP BY `user_id`', array( $level_id ) ) );
		$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_pmpor_discord_role_mapping' ), true );
		update_option( 'ets_admin_level_deleted', true );
		foreach ( $result as $key => $ids ) {
			$user_id      = $ids->user_id;
			$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
			if ( $access_token ) {
				as_schedule_single_action( ets_pmpro_discord_get_random_timestamp( ets_pmpro_discord_get_highest_last_attempt_timestamp() ), 'ets_pmpro_discord_as_handle_pmpro_cancel', array( $user_id, $level_id, $level_id ), ETS_DISCORD_AS_GROUP_NAME );
			}
		}
	}

	/**
	 * Method for allow user to login with discord account.
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_checkout_after_email() {
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		if ( ! is_user_logged_in() ) {
			$default_role                   = sanitize_text_field( trim( get_option( '_ets_pmpro_discord_default_role_id' ) ) );
			$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_pmpor_discord_role_mapping' ), true );
			$all_roles                      = unserialize( get_option( 'ets_pmpro_discord_all_roles' ) );
			$member_discord_login           = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_login_with_discord' ) ) );
			$btn_color                      = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_btn_color' ) ) );
			$btn_text                       = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_loggedout_btn_text' ) ) );
			$role_will_assign_text          = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_role_will_assign_text' ) ) );
			$role_assigned_text             = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_role_assigned_text' ) ) );
			echo '<style>.pmpro-btn-connect{background-color: ' . $btn_color . ';}</style>';
			if ( $member_discord_login ) {
				$curr_level_id     = $_GET['level'] ?? '';
				$mapped_role_name  = '';
				$default_role_name = '';
				if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
					$default_role_name = $all_roles[ $default_role ];
				}
				if ( $curr_level_id && is_array( $all_roles ) ) {
					if ( is_array( $ets_pmpor_discord_role_mapping ) && array_key_exists( 'pmpro_level_id_' . $curr_level_id, $ets_pmpor_discord_role_mapping ) ) {
						$mapped_role_id = $ets_pmpor_discord_role_mapping[ 'pmpro_level_id_' . $curr_level_id ];
						if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
							$mapped_role_name = $all_roles[ $mapped_role_id ];
						}
					}
				}
				$current_url = ets_pmpro_discord_get_current_screen_url();
				echo '<a href="?action=discord-login&fromcheckout=1&url=' . $current_url . '" class="pmpro-btn-connect ets-btn" >' . esc_html( $btn_text ) . '<i class="fab fa-discord"></i></a>';
				$pmpro_connecttodiscord_btn = '';
				if ( $mapped_role_name ) {
					$pmpro_connecttodiscord_btn .= '<p class="ets_assigned_role">' . $role_will_assign_text. ' ';
					$pmpro_connecttodiscord_btn .= esc_html( $mapped_role_name );
					if ( $default_role_name ) {
						$pmpro_connecttodiscord_btn .= ', ' . esc_html( $default_role_name );
					}
					$pmpro_connecttodiscord_btn .= '</p>';

					echo $pmpro_connecttodiscord_btn;
				}
			}
		}
	}

	/**
	 * Method to save job queue for cancelled pmpro members.
	 *
	 * @param INT $level_id
	 * @param INT $user_id
	 * @param INT $cancel_level
	 * @return NONE
	 */
	public function ets_pmpro_discord_as_schdule_job_pmpro_cancel( $level_id, $user_id, $cancel_level ) {
		$membership_status = sanitize_text_field( trim( $this->ets_check_current_membership_status( $user_id ) ) );
		$access_token      = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
		$next_payment      = pmpro_next_payment( $user_id );
		global $pmpro_next_payment_timestamp;

		if ( ! empty( $cancel_level ) || $membership_status == 'admin_cancelled' ) {
			$args = array(
				'hook'    => 'ets_pmpro_discord_as_handle_pmpro_cancel',
				'args'    => array( $level_id, $user_id, $cancel_level ),
				'status'  => ActionScheduler_Store::STATUS_PENDING,
				'orderby' => 'date',
			);

			// check if member is already added to job queue.
			$cancl_arr_already_added = as_get_scheduled_actions( $args, ARRAY_A );

			if ( count( $cancl_arr_already_added ) === 0 && $access_token && ( $membership_status == 'cancelled' || $membership_status == 'admin_cancelled' ) ) {
				as_schedule_single_action( ets_pmpro_discord_get_random_timestamp( ets_pmpro_discord_get_highest_last_attempt_timestamp() ), 'ets_pmpro_discord_as_handle_pmpro_cancel', array( $user_id, $level_id, $cancel_level ), ETS_DISCORD_AS_GROUP_NAME );
			}
		}
	}

	/**
	 * If the cancel on next payment is enabled.
	 */
	public function ets_pmpro_discord_handle_cancel_on_next_payment( $level, $user_id, $old_level_status, $cancel_level ) {
		global $wpdb;
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$cancel_on_next_payment = is_plugin_active( 'pmpro-cancel-on-next-payment-date/pmpro-cancel-on-next-payment-date.php' );
		$access_token           = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );

		if ( $cancel_on_next_payment && $old_level_status == 'cancelled' && $cancel_level ) {
			$end_date           = $wpdb->get_var( $wpdb->prepare( "SELECT enddate FROM $wpdb->pmpro_memberships_users WHERE status=%s AND membership_id=%d AND user_id=%d", 'active', $level, $user_id ) );
			$end_date_timestamp = date_timestamp_get( date_create( $end_date ) );
			if ( $end_date_timestamp !== false ) {
				if ( $access_token ) {
					as_schedule_single_action( $end_date_timestamp, 'ets_pmpro_discord_as_handle_pmpro_cancel', array( $user_id, $level, $cancel_level ), ETS_DISCORD_AS_GROUP_NAME );
				}
			}
		}
		return $level;
	}

	/*
	* Action schedule to schedule a function to run upon PMPRO Expiry.
	*
	* @param INT $user_id
	* @param INT $level_id
	* @return NONE
	*/
	public function ets_pmpro_discord_as_schdule_job_pmpro_expiry( $user_id, $level_id ) {
		$existing_members_queue = sanitize_text_field( trim( get_option( 'ets_queue_of_pmpro_members' ) ) );
		  $membership_status    = sanitize_text_field( trim( $this->ets_check_current_membership_status( $user_id ) ) );
		  $access_token         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
		if ( $membership_status == 'expired' && $access_token ) {
			as_schedule_single_action( ets_pmpro_discord_get_random_timestamp( ets_pmpro_discord_get_highest_last_attempt_timestamp() ), 'ets_pmpro_discord_as_handle_pmpro_expiry', array( $user_id, $level_id ), ETS_DISCORD_AS_GROUP_NAME );
		}
	}


	/**
	 * Localized script and style
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_script() {
		$min = ( WP_DEBUG) ? '' : '.min';
		wp_register_style(
			'ets_pmpro_add_discord_style',
			ETS_PMPRO_DISCORD_URL . 'assets/css/ets-pmpro-discord-style' . $min . '.css',
			false,
			ETS_PMPRO_VERSION
		);

		wp_register_script(
			'ets_pmpro_add_discord_script',
			ETS_PMPRO_DISCORD_URL . 'assets/js/ets-pmpro-add-discord-script.min.js',
			array( 'jquery' ),
			ETS_PMPRO_VERSION
		);

		$script_params = array(
			'admin_ajax'        => admin_url( 'admin-ajax.php' ),
			'permissions_const' => ETS_DISCORD_BOT_PERMISSIONS,
			'is_admin'          => is_admin(),
			'ets_discord_nonce' => wp_create_nonce( 'ets-discord-ajax-nonce' ),
		);
		wp_localize_script( 'ets_pmpro_add_discord_script', 'etsPmproParams', $script_params );

	}

	/**
	 * Localized admin script and style
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_admin_script() {

		wp_register_style(
			'ets_pmpro_add_skeletabs_style',
			ETS_PMPRO_DISCORD_URL . 'assets/css/skeletabs.css',
			false,
			ETS_PMPRO_VERSION
		);
		wp_enqueue_style( 'ets_pmpro_add_skeletabs_style' );

		wp_register_script(
			'ets_pmpro_add_skeletabs_script',
			ETS_PMPRO_DISCORD_URL . 'assets/js/skeletabs.js',
			array( 'jquery' ),
			ETS_PMPRO_VERSION
		);
	}

	/**
	 * Add menu in PmPro membership dashboard sub-menu
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_new_menu() {
		// Add sub-menu into PmPro main-menus list
		add_submenu_page( 'pmpro-dashboard', __( 'Discord Settings', 'paid-memberships-pro' ), __( 'Discord Settings', 'paid-memberships-pro' ), 'manage_options', 'discord-options', array( $this, 'ets_pmpro_discord_setting_page' ) );
	}

	/**
	 * Get user membership status by user_id
	 *
	 * @param INT $user_id
	 * @return STRING $status
	 */
	public function ets_check_current_membership_status( $user_id ) {
		global $wpdb;
		$sql    = $wpdb->prepare( 'SELECT `status` FROM ' . $wpdb->prefix . 'pmpro_memberships_users' . ' WHERE `user_id`= %d ORDER BY `id` DESC limit 1', array( $user_id ) );
		$result = $wpdb->get_results( $sql );
		return $result[0]->status;
	}

	/**
	 * Define plugin settings rules
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		wp_enqueue_script( 'ets_pmpro_add_skeletabs_script' );
		wp_enqueue_script( 'ets_pmpro_add_discord_script' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		$log_api_res = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_log_api_response' ) ) );
		if ( isset( $_GET['save_settings_msg'] ) ) {
			?>
				<div class="notice notice-success is-dismissible support-success-msg">
					<p><?php echo esc_html( $_GET['save_settings_msg'] ); ?></p>
				</div>
			<?php
		}
		if ( $log_api_res ) {
			echo '<div class="notice notice-error is-dismissible"> <p>PMPRO - Discord logging is currently enabled. Since logs may contain sensitive information, please ensure that you only leave it enabled for as long as it is needed for troubleshooting. If you currently have a support ticket open, please do not disable logging until the Support Team has reviewed your logs.</p> </div>';
		}
		?>
		<h1><?php echo __( 'PMPRO Discord Add On Settings', 'pmpro-discord-add-on' ); ?></h1>

	  <div id="outer" class="skltbs-theme-light" data-skeletabs='{ "startIndex": 1 }'>
		<ul class="skltbs-tab-group">
			  <li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="settings" ><?php echo __( 'Application Details', 'pmpro-discord-add-on' ); ?><span class="initialtab spinner"></span></button>
			  </li>
					<?php if ( Check_saved_settings_status() ) : ?>
			  <li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="level-mapping" ><?php echo __( 'Role Mappings', 'pmpro-discord-add-on' ); ?></button>
			  </li>
					<?php endif; ?>
			  <li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="advanced" data-toggle="tab" data-event="ets_advanced"><?php echo __( 'Advanced', 'pmpro-discord-add-on' ); ?>	
				</button>
			  </li>
		<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="appearance" data-toggle="tab" data-event="ets_appearance"><?php echo __( 'Appearance', 'pmpro-discord-add-on' ); ?>	
				</button>
			  </li>
			  <li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="logs" data-toggle="tab" data-event="ets_logs"><?php echo __( 'Logs', 'pmpro-discord-add-on' ); ?>	
				</button>
			  </li>
			<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="docs" data-toggle="tab" data-event="ets_docs"><?php echo __( 'Documentation', 'pmpro-discord-add-on' ); ?>	
				</button>
			  </li>
			
			<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="support" data-toggle="tab" data-event="ets_about_us"><?php echo __( 'Support', 'pmpro-discord-add-on' ); ?>	
				</button>
			  </li>
		</ul>
		<div class="skltbs-panel-group">
		  <div id="ets_pmpro_application_details" class="skltbs-panel">
					<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/discord-settings.php'; ?>
		  </div>
					<?php if ( Check_saved_settings_status() ) : ?>
		  <div id="ets_pmpro_role_mapping"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/discord-role-level-map.php'; ?>
		  </div>
					<?php endif; ?>
		  <div id="ets_pmpro_advance_settings"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/advanced.php'; ?>
		  </div>
	  <div id="ets_pmpro_appearance"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/appearance.php'; ?>
		  </div>	
		  <div id="ets_pmpro_error_log"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/error_log.php'; ?>
		  </div>
					<div id="ets_pmpro_documentation"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/documentation.php'; ?>
		  </div>
					
					<div id="ets_pmpro_support"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/get-support.php'; ?>
		  </div>
		</div>
	  </div>
		<?php
		$this->get_Support_Data();
	}


	/**
	 * Save application details
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_application_details() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_pmpro_discord_client_id = isset( $_POST['ets_pmpro_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_client_id'] ) ) : '';

		$discord_client_secret = isset( $_POST['ets_pmpro_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_client_secret'] ) ) : '';

		$discord_bot_token = isset( $_POST['ets_pmpro_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_bot_token'] ) ) : '';

		$ets_pmpro_discord_redirect_url = isset( $_POST['ets_pmpro_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_redirect_url'] ) ) : '';

		$ets_pmpro_discord_guild_id = isset( $_POST['ets_pmpro_discord_guild_id'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_guild_id'] ) ) : '';

		if ( isset( $_POST['submit'] ) && ! isset( $_POST['ets_pmpor_discord_role_mapping'] ) ) {
			if ( isset( $_POST['ets_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_settings'], 'save_discord_settings' ) ) {
				if ( $ets_pmpro_discord_client_id ) {
					update_option( 'ets_pmpro_discord_client_id', $ets_pmpro_discord_client_id );
				}

				if ( $discord_client_secret ) {
					update_option( 'ets_pmpro_discord_client_secret', $discord_client_secret );
				}

				if ( $discord_bot_token ) {
					update_option( 'ets_pmpro_discord_bot_token', $discord_bot_token );
				}

				if ( $ets_pmpro_discord_redirect_url ) {
					// add a query string param `via` GH #185.
					$ets_pmpro_discord_redirect_url = get_formated_discord_redirect_url( $ets_pmpro_discord_redirect_url );
					update_option( 'ets_pmpro_discord_redirect_url', $ets_pmpro_discord_redirect_url );
				}

				if ( $ets_pmpro_discord_guild_id ) {
					update_option( 'ets_pmpro_discord_guild_id', $ets_pmpro_discord_guild_id );
				}
				$message      = 'Your settings are saved successfully.';
				$pre_location = $_POST['referrer'] . '&save_settings_msg=' . $message . '#ets_pmpro_application_details';
				wp_safe_redirect( $pre_location );
			}
		}
	}

	/**
	 * Save Role mappiing settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_role_mapping() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_discord_roles = isset( $_POST['ets_pmpor_discord_role_mapping'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpor_discord_role_mapping'] ) ) : '';

		$_ets_pmpro_discord_default_role_id = isset( $_POST['pmpro_defaultRole'] ) ? sanitize_textarea_field( trim( $_POST['pmpro_defaultRole'] ) ) : '';

		$allow_none_member = isset( $_POST['allow_none_member'] ) ? sanitize_textarea_field( trim( $_POST['allow_none_member'] ) ) : '';

		$ets_discord_roles   = stripslashes( $ets_discord_roles );
		$save_mapping_status = update_option( 'ets_pmpor_discord_role_mapping', $ets_discord_roles );
		if ( isset( $_POST['ets_pmpor_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_pmpor_discord_role_mappings_nonce'], 'discord_role_mappings_nonce' ) ) {
			if ( ( $save_mapping_status || isset( $_POST['ets_pmpor_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
				if ( $_ets_pmpro_discord_default_role_id ) {
					update_option( '_ets_pmpro_discord_default_role_id', $_ets_pmpro_discord_default_role_id );
				}

				if ( $allow_none_member ) {
					update_option( 'ets_pmpro_allow_none_member', $allow_none_member );
				}
				$message = 'Your mappings are saved successfully.';
			}
			if ( isset( $_POST['flush'] ) ) {
				delete_option( 'ets_pmpor_discord_role_mapping' );
				delete_option( '_ets_pmpro_discord_default_role_id' );
				delete_option( 'ets_pmpro_allow_none_member' );
				$message = 'Your settings flushed successfully.';
			}
			$pre_location = $_POST['referrer'] . '&save_settings_msg=' . $message . '#ets_pmpro_role_mapping';
			wp_safe_redirect( $pre_location );
		}
	}

	/**
	 * Save advance settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_advance_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$set_job_cnrc = isset( $_POST['set_job_cnrc'] ) ? sanitize_textarea_field( trim( $_POST['set_job_cnrc'] ) ) : '';

		$set_job_q_batch_size = isset( $_POST['set_job_q_batch_size'] ) ? sanitize_textarea_field( trim( $_POST['set_job_q_batch_size'] ) ) : '';

		$retry_api_count = isset( $_POST['ets_pmpro_retry_api_count'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_retry_api_count'] ) ) : '';

		$ets_pmpro_discord_send_expiration_warning_dm = isset( $_POST['ets_pmpro_discord_send_expiration_warning_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_expiration_warning_dm'] ) ) : false;

		$ets_pmpro_discord_expiration_warning_message = isset( $_POST['ets_pmpro_discord_expiration_warning_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_expiration_warning_message'] ) ) : '';

		$ets_pmpro_discord_send_membership_expired_dm = isset( $_POST['ets_pmpro_discord_send_membership_expired_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_membership_expired_dm'] ) ) : false;

		$ets_pmpro_discord_expiration_expired_message = isset( $_POST['ets_pmpro_discord_expiration_expired_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_expiration_expired_message'] ) ) : '';

		$ets_pmpro_discord_send_welcome_dm = isset( $_POST['ets_pmpro_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_welcome_dm'] ) ) : false;

		$ets_pmpro_discord_welcome_message = isset( $_POST['ets_pmpro_discord_welcome_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_welcome_message'] ) ) : '';

		$ets_pmpro_discord_send_membership_cancel_dm = isset( $_POST['ets_pmpro_discord_send_membership_cancel_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_membership_cancel_dm'] ) ) : '';

		$ets_pmpro_discord_cancel_message = isset( $_POST['ets_pmpro_discord_cancel_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_cancel_message'] ) ) : '';

		$ets_pmpro_discord_embed_messaging_feature = isset( $_POST['ets_pmpro_discord_embed_messaging_feature'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_embed_messaging_feature'] ) ) : '';

		$ets_pmpro_discord_data_erases = isset( $_POST['ets_pmpro_discord_data_erases'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_data_erases'] ) ) : '';

		if ( isset( $_POST['adv_submit'] ) ) {
			if ( isset( $_POST['ets_discord_save_adv_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_adv_settings'], 'save_discord_adv_settings' ) ) {
				if ( isset( $_POST['upon_failed_payment'] ) ) {
					update_option( 'ets_pmpro_discord_payment_failed', true );
				} else {
					update_option( 'ets_pmpro_discord_payment_failed', false );
				}

				if ( isset( $_POST['log_api_res'] ) ) {
					update_option( 'ets_pmpro_discord_log_api_response', true );
				} else {
					update_option( 'ets_pmpro_discord_log_api_response', false );
				}

				if ( isset( $_POST['retry_failed_api'] ) ) {
					update_option( 'ets_pmpro_retry_failed_api', true );
				} else {
					update_option( 'ets_pmpro_retry_failed_api', false );
				}

				if ( isset( $_POST['member_kick_out'] ) ) {
					update_option( 'ets_pmpro_member_kick_out', true );
				} else {
					update_option( 'ets_pmpro_member_kick_out', false );
				}

				if ( isset( $_POST['member_force_discord_login'] ) ) {
					update_option( 'ets_pmpro_discord_force_login_with_discord', true );
					update_option( 'ets_pmpro_discord_login_with_discord', true );
				} else {
					update_option( 'ets_pmpro_discord_force_login_with_discord', false );
				}

				if ( isset( $_POST['member_discord_login'] ) ) {
					update_option( 'ets_pmpro_discord_login_with_discord', true );
				} elseif ( isset( $_POST['member_force_discord_login'] ) ) {
					update_option( 'ets_pmpro_discord_login_with_discord', true );
				} else {
					update_option( 'ets_pmpro_discord_login_with_discord', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_welcome_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_welcome_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_welcome_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_expiration_warning_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_expiration_warning_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_expiration_warning_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_welcome_message'] ) && $_POST['ets_pmpro_discord_welcome_message'] != '' ) {
					update_option( 'ets_pmpro_discord_welcome_message', $ets_pmpro_discord_welcome_message );
				} else {
					update_option( 'ets_pmpro_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_pmpro_discord_expiration_warning_message'] ) && $_POST['ets_pmpro_discord_expiration_warning_message'] != '' ) {
					update_option( 'ets_pmpro_discord_expiration_warning_message', $ets_pmpro_discord_expiration_warning_message );
				} else {
					update_option( 'ets_pmpro_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_pmpro_discord_expiration_expired_message'] ) && $_POST['ets_pmpro_discord_expiration_expired_message'] != '' ) {
					update_option( 'ets_pmpro_discord_expiration_expired_message', $ets_pmpro_discord_expiration_expired_message );
				} else {
					update_option( 'ets_pmpro_discord_expiration_expired_message', 'Your membership is expired' );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_membership_expired_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_membership_expired_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_membership_expired_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_membership_cancel_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_membership_cancel_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_membership_cancel_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_cancel_message'] ) && $_POST['ets_pmpro_discord_cancel_message'] != '' ) {
					update_option( 'ets_pmpro_discord_cancel_message', $ets_pmpro_discord_cancel_message );
				} else {
					update_option( 'ets_pmpro_discord_cancel_message', 'Your membership is cancled' );
				}

				if ( isset( $_POST['set_job_cnrc'] ) ) {
					if ( $set_job_cnrc < 1 ) {
						update_option( 'ets_pmpro_discord_job_queue_concurrency', 1 );
					} else {
						update_option( 'ets_pmpro_discord_job_queue_concurrency', $set_job_cnrc );
					}
				}

				if ( isset( $_POST['set_job_q_batch_size'] ) ) {
					if ( $set_job_q_batch_size < 1 ) {
						update_option( 'ets_pmpro_discord_job_queue_batch_size', 1 );
					} else {
						update_option( 'ets_pmpro_discord_job_queue_batch_size', $set_job_q_batch_size );
					}
				}

				if ( isset( $_POST['ets_pmpro_retry_api_count'] ) ) {
					if ( $retry_api_count < 1 ) {
						update_option( 'ets_pmpro_retry_api_count', 1 );
					} else {
						update_option( 'ets_pmpro_retry_api_count', $retry_api_count );
					}
				}

				if ( isset( $_POST['ets_pmpro_discord_embed_messaging_feature'] ) ) {
					update_option( 'ets_pmpro_discord_embed_messaging_feature', true );
				} else {
					update_option( 'ets_pmpro_discord_embed_messaging_feature', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_data_erases'] ) ) {
					update_option( 'ets_pmpro_discord_data_erases', true );
				} else {
					update_option( 'ets_pmpro_discord_data_erases', false );
				}

				$message      = 'Your settings are saved successfully.';
				$pre_location = $_POST['referrer'] . '&save_settings_msg=' . $message . '#ets_pmpro_advance_settings';
				wp_safe_redirect( $pre_location );
			}
		}

	}

	/**
	 * Save appearance settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_appearance_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$ets_pmpro_btn_color            = isset( $_POST['ets_pmpro_btn_color'] ) && $_POST['ets_pmpro_btn_color'] !== '' ? sanitize_text_field( trim( $_POST['ets_pmpro_btn_color'] ) ) : '#77a02e';
		$ets_pmpro_btn_disconnect_color = isset( $_POST['ets_pmpro_btn_disconnect_color'] ) && $_POST['ets_pmpro_btn_disconnect_color'] != '' ? sanitize_text_field( trim( $_POST['ets_pmpro_btn_disconnect_color'] ) ) : '#ff0000';
		$ets_pmpro_loggedin_btn_text    = isset( $_POST['ets_pmpro_loggedin_btn_text'] ) && $_POST['ets_pmpro_loggedin_btn_text'] != '' ? sanitize_text_field( trim( $_POST['ets_pmpro_loggedin_btn_text'] ) ) : 'Connect To Discord';
		$ets_pmpro_loggedout_btn_text   = isset( $_POST['ets_pmpro_loggedout_btn_text'] ) && $_POST['ets_pmpro_loggedout_btn_text'] != '' ? sanitize_text_field( trim( $_POST['ets_pmpro_loggedout_btn_text'] ) ) : 'Login With Discord';
		$ets_pmpro_disconnect_btn_text  = $_POST['ets_pmpro_disconnect_btn_text'] ? sanitize_text_field( trim( $_POST['ets_pmpro_disconnect_btn_text'] ) ) : 'Disconnect From Discord';
		$ets_pmpro_role_assigned_text   = isset( $_POST['ets_pmpro_role_assigned_text'] ) && $_POST['ets_pmpro_role_assigned_text'] != '' ? sanitize_textarea_field( trim( $_POST['ets_pmpro_role_assigned_text'] ) ) : 'Following Roles was assigned to you in Discord:';
		$ets_pmpro_role_will_assign_text = isset( $_POST['ets_pmpro_role_will_assign_text'] ) && $_POST['ets_pmpro_role_will_assign_text'] != '' ? sanitize_textarea_field( trim( $_POST['ets_pmpro_role_will_assign_text'] ) ) : 'Following Roles will be assigned to you in Discord:';

		if ( isset( $_POST['apr_submit'] ) ) {

			if ( isset( $_POST['ets_discord_save_aprnc_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_aprnc_settings'], 'save_discord_aprnc_settings' ) ) {
				if ( $ets_pmpro_btn_color ) {
					update_option( 'ets_pmpro_discord_btn_color', $ets_pmpro_btn_color );
				}
				if ( $ets_pmpro_btn_disconnect_color ) {
					update_option( 'ets_pmpro_btn_disconnect_color', $ets_pmpro_btn_disconnect_color );
				}
				if ( $ets_pmpro_loggedout_btn_text ) {
					update_option( 'ets_pmpro_discord_loggedout_btn_text', $ets_pmpro_loggedout_btn_text );
				}
				if ( $ets_pmpro_loggedin_btn_text ) {
					update_option( 'ets_pmpro_discord_loggedin_btn_text', $ets_pmpro_loggedin_btn_text );
				}
				if ( $ets_pmpro_disconnect_btn_text ) {
					update_option( 'ets_pmpro_disconnect_btn_text', $ets_pmpro_disconnect_btn_text );
				}
				if ( $ets_pmpro_role_assigned_text ) {
					update_option( 'ets_pmpro_discord_role_assigned_text', $ets_pmpro_role_assigned_text );
				}
				if ( $ets_pmpro_role_will_assign_text ) {
					update_option( 'ets_pmpro_discord_role_will_assign_text', $ets_pmpro_role_will_assign_text );
				}
				$message      = 'Your settings are saved successfully.';
				$pre_location = $_POST['referrer'] . '&save_settings_msg=' . $message . '#ets_pmpro_appearance';
				wp_safe_redirect( $pre_location );
			}
		}

	}

	/**
	 * Send mail to support form current user
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function get_Support_Data() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		if ( isset( $_POST['save'] ) ) {
			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['ets_discord_get_support'], 'get_support' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
			}
			$etsUserName  = isset( $_POST['ets_user_name'] ) ? sanitize_text_field( trim( $_POST['ets_user_name'] ) ) : '';
			$etsUserEmail = isset( $_POST['ets_user_email'] ) ? sanitize_text_field( trim( $_POST['ets_user_email'] ) ) : '';
			$message      = isset( $_POST['ets_support_msg'] ) ? sanitize_text_field( trim( $_POST['ets_support_msg'] ) ) : '';
			$sub          = isset( $_POST['ets_support_subject'] ) ? sanitize_text_field( trim( $_POST['ets_support_subject'] ) ) : '';

			if ( $etsUserName && $etsUserEmail && $message && $sub ) {

				$subject   = $sub;
				$to        = array(
					'contact@expresstechsoftwares.com',
					'vinod.tiwari@expresstechsoftwares.com',
				);
				$content   = 'Name: ' . $etsUserName . '<br>';
				$content  .= 'Contact Email: ' . $etsUserEmail . '<br>';
				$content  .= 'Message: ' . $message;
				$headers   = array();
				$blogemail = get_bloginfo( 'admin_email' );
				$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $blogemail . '>' . "\r\n";
				$mail      = wp_mail( $to, $subject, $content, $headers );

				if ( $mail ) {
					?>
						<div class="notice notice-success is-dismissible support-success-msg">
							<p><?php echo __( 'Your request have been successfully submitted!', 'pmpro-discord-add-on' ); ?></p>
						</div>
					<?php
				}
			}
		}
	}

	/*
	* Add extra column body into pmpro members list
	* @param STRING $colname
	* @param INT $user
	* @return NONE
	*/
	public function ets_pmpro_discord_pmpro_extra_cols_body( $colname, $user_id ) {
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		wp_enqueue_script( 'ets_pmpro_add_discord_script' );
		$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
		if ( 'discord' === $colname ) {
			if ( $access_token ) {
				$discord_username = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_username', true ) ) );
				echo '<p class="' . esc_attr( $user_id ) . ' ets-save-success">Success</p><a class="button button-primary ets-run-api" data-uid="' . esc_attr( $user_id ) . '" href="#">';
				echo __( 'Run API', 'pmpro-discord-add-on' );
				echo '</a><span class="' . esc_attr( $user_id ) . ' spinner"></span>';
				echo esc_html( $discord_username );
			} else {
				echo __( 'Not Connected', 'pmpro-discord-add-on' );
			}
		}

		if ( 'joined_date' === $colname ) {
			echo esc_html( get_user_meta( $user_id, '_ets_pmpro_discord_join_date', true ) );
		}
	}
	/*
	* Add extra column into pmpro members list
	* @param ARRAY $columns
	* @return ARRAY $columns
	*/
	public function ets_pmpro_discord_manage_memberslist_columns( $columns ) {
		$columns['discord']     = __( 'Discord', 'pmpro-discord-add-on' );
		$columns['joined_date'] = __( 'Joined Date', 'pmpro-discord-add-on' );
		return $columns;
	}

	/*
	* Add extra css
	* @param NONE
	* @return NONE
	*/
	public function ets_pmpro_discord_add_inline_css_checkout() {
		$member_force_discord_login = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_force_login_with_discord' ) ) );
		$member_discord_login       = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_login_with_discord' ) ) );
		if ( in_array( 'pmpro-checkout', get_body_class() ) && $member_force_discord_login && $member_discord_login ) {
			if ( ! is_user_logged_in() ) {
				$custom_css = 'body.pmpro-checkout div#pmpro_user_fields,body.pmpro-checkout div#pmpro_billing_address_fields,body.pmpro-checkout div#pmpro_payment_information_fields,body.pmpro-checkout div.pmpro_submit{display: none!important;}';
			} else {
				$custom_css = '';
			}
			wp_add_inline_style( 'ets_pmpro_add_discord_style', $custom_css );
		}
	}

	/**
	 *  Filter call back to show or hide the Connect Discord button on profile page.
	 *
	 * @param bool $show By default True.
	 */
	public function ets_pmpro_discord_show_connect_button_on_profile( $show = true ) {

		return $show;
	}

	/**
	 * Add  Discord connected column to WP Users listing.
	 *
	 * @param ARRAY $columns
	 * @return VOID
	 */
	public function ets_pmpro_discord_add_discord_connected_account( $columns ) {
		$columns['ets_pmpro_discord_account'] = esc_html__( 'Discord Connected Account', 'pmpro-discord-add-on' );
		return $columns;

	}

	/**
	 *  Display discord-connected account details and link for the account inside discord.
	 *
	 * @param STRING $value
	 * @param STRING $column_name
	 * @param INT    $user_id
	 * @return void
	 */
	public function ets_pmpro_discord_discord_connected_account( $value, $column_name, $user_id ) {
		if ( $column_name === 'ets_pmpro_discord_account' ) {
			$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
			if ( $access_token ) {
				$discord_user_id  = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_user_id', true ) ) );
				$discord_username = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_username', true ) ) );
				return '<a target="_blank" href="https://discord.com/channels/@me/' . $discord_user_id . '"  class="" >' . esc_html__( 'Discord: ' . $discord_username ) . '</a>';
			} else {
				return esc_html__( 'Not Connected', 'pmpro-discord-add-on' );
			}
		}

		return $value;

	}

	public function ets_pmpro_discord_notice_dismiss(){
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_discord_nonce'], 'ets-discord-ajax-nonce' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
		}

		update_user_meta( get_current_user_id(), '_ets_pmpro_discord_dismissed_notification', true );
		$event_res = array(
			'status'  => 1,
			'message' => __( 'success', 'pmpro-discord-add-on' ),
		);
		return wp_send_json( $event_res );

		exit();
	}

}
new Ets_Pmpro_Admin_Setting();
