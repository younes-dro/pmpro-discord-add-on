<?php
$ets_pmpro_discord_client_id    = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_client_id' ) ) );
$discord_client_secret          = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_client_secret' ) ) );
$discord_bot_token              = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_bot_token' ) ) );
$ets_pmpro_discord_redirect_url = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_redirect_url' ) ) );
$ets_discord_roles              = sanitize_text_field( trim( get_option( 'ets_pmpor_discord_role_mapping' ) ) );
$ets_pmpro_discord_guild_id     = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_guild_id' ) ) );
$current_screen                 = ets_pmpro_discord_get_current_screen_url();
?>
<form method="post" action="<?php echo get_site_url() . '/wp-admin/admin-post.php'; ?>">
 <input type="hidden" name="action" value="pmpro_discord_save_application_details">
 <input type="hidden" name="referrer" value="<?php echo $current_screen; ?>" />
	<?php wp_nonce_field( 'save_discord_settings', 'ets_discord_save_settings' ); ?>
	<div class="ets-input-group">
	  <label><?php echo __( 'Client ID', 'pmpro-discord-add-on' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_pmpro_discord_client_id" value="<?php if ( isset( $ets_pmpro_discord_client_id ) ) { echo esc_attr( $ets_pmpro_discord_client_id ); } ?>" required placeholder="Discord Client ID">
	</div>
	<div class="ets-input-group">
	  <label><?php echo __( 'Client Secret', 'pmpro-discord-add-on' ); ?> :</label>
		<input type="password" class="ets-input" name="ets_pmpro_discord_client_secret" value="<?php if ( isset( $discord_client_secret ) ) { echo esc_attr( $discord_client_secret ); } ?>" required placeholder="Discord Client Secret">
	</div>
	<div class="ets-input-group">
	  <label><?php echo __( 'Redirect URL', 'pmpro-discord-add-on' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_pmpro_discord_redirect_url" placeholder="Discord Redirect Url" value="<?php if ( isset( $ets_pmpro_discord_redirect_url ) ) { echo esc_attr( $ets_pmpro_discord_redirect_url ); } ?>" required>
		<p class="description"><?php echo __( 'Registered Discord APP URL', 'pmpro-discord-add-on' ); ?>
    <?php if($ets_pmpro_discord_client_id) {  ?>
	  <a target="_blank" href="<?php echo sprintf( 'https://discord.com/developers/applications/%d/oauth2/general', $ets_pmpro_discord_client_id ); ?>">Open Discord.com/developers/applications</a>
    <?php } ?>
  </p>
	</div>
	<div class="ets-input-group">
	  <label><?php echo __( 'Bot Token', 'pmpro-discord-add-on' ); ?> :</label>
		<input type="password" class="ets-input" name="ets_pmpro_discord_bot_token" value="<?php if ( isset( $discord_bot_token ) ) { echo esc_attr( $discord_bot_token ); } ?>" required placeholder="Discord Bot Token">
	</div>
	<div class="ets-input-group">
	  <label><?php echo __( 'Server ID', 'pmpro-discord-add-on' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_pmpro_discord_guild_id" placeholder="Discord Server Id" value="<?php if ( isset( $ets_pmpro_discord_guild_id ) ) { echo esc_attr( $ets_pmpro_discord_guild_id ); } ?>" required>
	</div>
	<?php if ( empty( $ets_pmpro_discord_client_id ) || empty( $discord_client_secret ) || empty( $discord_bot_token ) || empty( $ets_pmpro_discord_redirect_url ) || empty( $ets_pmpro_discord_guild_id ) ) { ?>
	  <p class="ets-danger-text description">
		<?php echo __( 'Please save your form', 'pmpro-discord-add-on' ); ?>
	  </p>
	<?php } ?>
	<p>
	  <button type="submit" name="submit" value="ets_submit" class="ets-submit ets-bg-green">
		<?php echo __( 'Save Settings', 'pmpro-discord-add-on' ); ?>
	  </button>
	  <?php if ( get_option( 'ets_pmpro_discord_client_id' ) ) : ?>
		<a href="?action=discord-connectToBot" class="ets-btn pmpro-btn-connect-to-bot" id="pmpro-connect-discord-bot"><?php echo __( 'Connect your Bot', 'pmpro-discord-add-on' ); ?> <i class='fab fa-discord'></i></a>
	  <?php endif; ?>
	</p>
</form>
