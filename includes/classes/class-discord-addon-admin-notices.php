<?php



 defined( 'ABSPATH' ) || exit;

 /**
  * ETS PMPRO Admin notices
  *
  * @since 1.2.11
  */
class Ets_Pmpro_Admin_Notices {

	/**
	 * Static constructor
	 *
	 * @return void
	 */
	public static function init() {

		add_action( 'admin_notices', array( __CLASS__, 'ets_pmpro_discord_display_notification' ) );
	}

	/**
	 * Display the review notification
	 *
	 * @return void
	 */
	public static function ets_pmpro_discord_display_notification() {

		$screen = get_current_screen();

		if ( $screen && $screen->id === 'memberships_page_discord-options' ) {

			$dismissed = get_user_meta( get_current_user_id(), '_ets_pmpro_discord_dismissed_notification', true );

			if ( ! $dismissed ) {
				ob_start();
				require_once ETS_PMPRO_DISCORD_PATH . 'includes/template/notification/review/review.php';
				$notification_content = ob_get_clean();
				echo wp_kses( $notification_content, self::ets_pmpro_discord_allowed_html() );
			}
		}
	}

	/**
	 * Get allowed_html
	 *
	 * @return ARRAY
	 */
	public static function ets_pmpro_discord_allowed_html() {
		$allowed_html = array(
			'div' => array(
				'class' => array(),
			),
			'p'   => array(
				'class' => array(),
			),
			'a'   => array(
				'id'           => array(),
				'data-user-id' => array(),
				'href'         => array(),
				'class'        => array(),
				'style'        => array(),
			),

			'img' => array(
				'src'   => array(),
				'class' => array(),
			),
			'h1'  => array(),
			'b'   => array(),
		);

		return $allowed_html;
	}

}

Ets_Pmpro_Admin_Notices::init();
