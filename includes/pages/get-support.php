<?php
  $currUserName = '';
  $currentUser  = wp_get_current_user();
if ( $currentUser ) {
	$currUserName = sanitize_text_field( trim( $currentUser->user_login ) );
}
?>
<div class="contact-form " style="min-height: auto!important;">
  <form accept="#" method="post">
	
	  <div class="ets-container">
		<div class="top-logo-title">
		  <img src="<?php echo esc_attr( ETS_PMPRO_DISCORD_URL . 'assets/images/ets-logo.png ' ); ?>" class="img-fluid company-logo" alt="">
		  <h1><?php echo __( 'ExpressTech Softwares Solutions Pvt. Ltd.', 'pmpro-discord-add-on' ); ?></h1>
		  <p><?php echo __( 'ExpressTech Software Solution Pvt. Ltd. is the leading Enterprise WordPress development company.', 'pmpro-discord-add-on' ); ?><br>
		  <?php echo __( 'Contact us for any WordPress Related development projects.', 'pmpro-discord-add-on' ); ?></p>
		</div>

		<ul style="text-align: left;">
			<li class="mp-icon mp-icon-right-big"><?php esc_html_e( 'If you encounter any issues or errors, please report them on our support forum for the Connect Paid Memberships Pro to Discord plugin. Our community will be happy to help you troubleshoot and resolve the issue.', 'pmpro-discord-add-on' ); ?></li>
			<li class="mp-icon mp-icon-right-big">
			<?php
			echo wp_kses(
				'<a target="_blank" href="https://wordpress.org/support/plugin/pmpro-discord-add-on/">Support » Plugin: Connect Paid Memberships Pro to Discord</a>',
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			);
			?>
 </li>
		</ul>

			  </div>
  </form>
</div>
