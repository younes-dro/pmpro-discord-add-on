<div class="error-log">
<?php
	$uuid     = get_option( 'ets_pmpro_discord_uuid_file_name' );
	$filename = $uuid . PMPro_Discord_Logs::$log_file_name;
	$handle   = fopen( WP_CONTENT_DIR . '/' . $filename, 'a+' );
  if( $handle ){
    while ( ! feof( $handle ) ) {
      echo fgets( $handle ) . '<br />';
    }
  }
	fclose( $handle );
?>
</div>
<div class="pmpro-clrbtndiv">
	<div class="form-group">
		<input type="button" class="pmpro-clrbtn ets-submit ets-bg-red" id="pmpro-clrbtn" name="pmpro_clrbtn" value="Clear Logs !">
		<span class="clr-log spinner" ></span>
	</div>
	<div class="form-group">
		<input type="button" class="ets-submit ets-bg-green" value="Refresh" onClick="window.location.reload()">
	</div>
  <div class="form-group">
		<a href="<?php echo esc_attr( content_url('/') . $filename ); ?>" class="ets-submit ets-pmpro-bg-download" download><?php echo __( 'Download', 'pmpro-discord-add-on'  ); ?></a>
	</div>
	<div class="form-group">
		<a href="<?php echo get_site_url();?>/wp-admin/tools.php?page=action-scheduler&status=pending&s=pmpro" class="ets-submit ets-bg-green"><?php echo __( 'API Queue', 'pmpro-discord-add-on'  ); ?></a>
	</div>
</div>
