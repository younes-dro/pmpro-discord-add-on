<?php
$upon_failed_payment                          = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_payment_failed' ) ) );
$log_api_res                                  = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_log_api_response' ) ) );
$retry_failed_api                             = sanitize_text_field( trim( get_option( 'ets_pmpro_retry_failed_api' ) ) );
$set_job_cnrc                                 = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_job_queue_concurrency' ) ) );
$set_job_q_batch_size                         = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_job_queue_batch_size' ) ) );
$retry_api_count                              = sanitize_text_field( trim( get_option( 'ets_pmpro_retry_api_count' ) ) );
$member_kick_out                              = sanitize_text_field( trim( get_option( 'ets_pmpro_member_kick_out' ) ) );
$member_force_discord_login                   = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_force_login_with_discord' ) ) );
$member_discord_login                         = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_login_with_discord' ) ) );
$ets_pmpro_discord_send_expiration_warning_dm = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_send_expiration_warning_dm' ) ) );
$ets_pmpro_discord_expiration_warning_message = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_expiration_warning_message' ) ) );
$ets_pmpro_discord_expired_message            = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_expired_message' ) ) );
$ets_pmpro_discord_send_membership_expired_dm = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_send_membership_expired_dm' ) ) );
$ets_pmpro_discord_expiration_expired_message = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_expiration_expired_message' ) ) );
$ets_pmpro_discord_send_welcome_dm            = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_send_welcome_dm' ) ) );
$ets_pmpro_discord_welcome_message            = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_welcome_message' ) ) );
$ets_pmpro_discord_send_membership_cancel_dm  = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_send_membership_cancel_dm' ) ) );
$ets_pmpro_discord_cancel_message             = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_cancel_message' ) ) );
$ets_pmpro_discord_embed_messaging_feature    = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_embed_messaging_feature' ) ) );
$ets_pmpro_discord_data_erases    = sanitize_text_field( trim( get_option( 'ets_pmpro_discord_data_erases' ) ) );
$current_screen = ets_pmpro_discord_get_current_screen_url();
?>
<form method="post" action="<?php echo get_site_url().'/wp-admin/admin-post.php' ?>">
 <input type="hidden" name="action" value="pmpro_discord_save_advance_settings">
 <input type="hidden" name="referrer" value="<?php echo $current_screen; ?>" />
<?php wp_nonce_field( 'save_discord_adv_settings', 'ets_discord_save_adv_settings' ); ?>
  <table class="form-table" role="presentation">
	<tbody>
  <tr>
		<th scope="row"><?php echo __( 'Shortcode', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		[discord_connect_button]
    <br/>
    <small><?php echo __( ' Using the shortcode [discord_connect_button] on any page, anyone can join the website discord server by authentication via member discord account. New members will get default role if selected in the setting.', 'pmpro-discord-add-on' ); ?></small>
		</fieldset></td>
	  </tr>
  <tr>
		<th scope="row"><?php echo __( 'Use rich embed messaging feature?', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_discord_embed_messaging_feature" type="checkbox" id="ets_pmpro_discord_embed_messaging_feature" 
		<?php
		if ( $ets_pmpro_discord_embed_messaging_feature == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
                <br/>
                <small>Use [LINEBREAK] to split lines.</small>                
		</fieldset></td>
	  </tr>  
	  <tr>
		<th scope="row"><?php echo __( 'Data erases on uninstall?', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_discord_data_erases" type="checkbox" id="ets_pmpro_discord_data_erases" 
		<?php
		if ( $ets_pmpro_discord_data_erases == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
                <br/>
                <small>By checking this box, you are indicating that you want to delete all data associated with the plugin when it is uninstalled.</small>                
		</fieldset></td>
	  </tr>	          
  <tr>
		<th scope="row"><?php echo __( 'Send welcome message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_discord_send_welcome_dm" type="checkbox" id="ets_pmpro_discord_send_welcome_dm" 
		<?php
		if ( $ets_pmpro_discord_send_welcome_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Membership welcome message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_pmpro_discord_dm_textarea" name="ets_pmpro_discord_welcome_message" id="ets_pmpro_discord_welcome_message" row="25" cols="50"><?php if ( $ets_pmpro_discord_welcome_message ) { echo wp_unslash($ets_pmpro_discord_welcome_message); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME], [MEMBERSHIP_ENDDATE], [MEMBERSHIP_STARTDATE]</small>
		</fieldset></td>
	  </tr>

	<tr>
		<th scope="row"><?php echo __( 'Send membership expiration warning message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_discord_send_expiration_warning_dm" type="checkbox" id="ets_pmpro_discord_send_expiration_warning_dm" 
		<?php
		if ( $ets_pmpro_discord_send_expiration_warning_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Membership expiration warning message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<textarea  class="ets_pmpro_discord_dm_textarea" name="ets_pmpro_discord_expiration_warning_message" id="ets_pmpro_discord_expiration_warning_message" row="25" cols="50"><?php if ( $ets_pmpro_discord_expiration_warning_message ) { echo wp_unslash($ets_pmpro_discord_expiration_warning_message); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME], [MEMBERSHIP_ENDDATE], [MEMBERSHIP_STARTDATE]</small>
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Send membership expired message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_discord_send_membership_expired_dm" type="checkbox" id="ets_pmpro_discord_send_membership_expired_dm" 
		<?php
		if ( $ets_pmpro_discord_send_membership_expired_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Membership expired message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<textarea  class="ets_pmpro_discord_dm_textarea" name="ets_pmpro_discord_expiration_expired_message" id="ets_pmpro_discord_expiration_expired_message" row="25" cols="50"><?php if ( $ets_pmpro_discord_expiration_expired_message ) { echo wp_unslash($ets_pmpro_discord_expiration_expired_message); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME]</small>
		</fieldset>
  </td>
		</tr>
	<tr>
		<th scope="row"><?php echo __( 'Send membership cancel message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_discord_send_membership_cancel_dm" type="checkbox" id="ets_pmpro_discord_send_membership_cancel_dm" 
		<?php
		if ( $ets_pmpro_discord_send_membership_cancel_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
		<tr>
		<th scope="row"><?php echo __( 'Membership cancel message', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<textarea  class="ets_pmpro_discord_dm_textarea" name="ets_pmpro_discord_cancel_message" id="ets_pmpro_discord_cancel_message" row="25" cols="50"><?php if ( $ets_pmpro_discord_cancel_message ) { echo wp_unslash($ets_pmpro_discord_cancel_message); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME]</small>
		</fieldset>
  </td>
		</tr>
  <tr>
		<th scope="row"><?php echo __( 'Re-assign roles upon payment failure', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="upon_failed_payment" type="checkbox" id="upon_failed_payment" 
		<?php
		if ( $upon_failed_payment == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Retry Failed API calls', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="retry_failed_api" type="checkbox" id="retry_failed_api" 
		<?php
		if ( $retry_failed_api == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Kick members out when they Disconnect their Account?', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="member_kick_out" type="checkbox" id="member_kick_out" 
		<?php
		if ( $member_kick_out == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset>
    <small>Members will be kicked out if this setting is checked.</small>
  </td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Force Discord Authentication before checkout', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="member_force_discord_login" type="checkbox" id="member_force_discord_login" 
		<?php
		if ( $member_force_discord_login == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset>
  </td>
	  </tr>        
	  <tr>
		<th scope="row"><?php echo __( 'Login with Discord on checkout Page', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="member_discord_login" type="checkbox" id="member_discord_login" 
		<?php
		if ( $member_discord_login == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset>
    <small>A new account will be created if the discord account E-mail is not exist into the system.</small>
  </td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'How many times a failed API call should get re-try', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_pmpro_retry_api_count" type="number" min="1" id="ets_pmpro_retry_api_count" value="<?php if ( isset( $retry_api_count ) ) { echo intval($retry_api_count); } else { echo 1; } ?>">
		</fieldset></td>
	  </tr> 
	  <tr>
		<th scope="row"><?php echo __( 'Set job queue concurrency', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="set_job_cnrc" type="number" min="1" id="set_job_cnrc" value="<?php if ( isset( $set_job_cnrc ) ) { echo intval($set_job_cnrc); } else { echo 1; } ?>">
		</fieldset></td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Set job queue batch size', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="set_job_q_batch_size" type="number" min="1" id="set_job_q_batch_size" value="<?php if ( isset( $set_job_q_batch_size ) ) { echo intval($set_job_q_batch_size); } else { echo 10; } ?>">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Log API calls response (For debugging purpose)', 'pmpro-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="log_api_res" type="checkbox" id="log_api_res" 
		<?php
		if ( $log_api_res == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	
	</tbody>
  </table>
  <div class="bottom-btn">
	<button type="submit" name="adv_submit" value="ets_submit" class="ets-submit ets-bg-green">
	  <?php echo __( 'Save Settings', 'pmpro-discord-add-on' ); ?>
	</button>
  </div>
</form>
