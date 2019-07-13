<div class="wrap">
	<div class="mk-plugin-panel">
		<form method="post">
			<div class="mk-plugin-panel-heading">
				<span><?php esc_html_e( 'Settings', 'logo_by_conditions' ); ?></span>
				<div class="pull-right">
					<?php submit_button( __( 'Submit' ), 'primary', '' ); ?>
				</div>
			</div>
			<div class="mk-plugin-panel-body">
				<div class="mk-plugin-blocks">

					<div class="mk-plugin-block">
						<div class="mk-plugin-form-group">
							<label for="default-logo"><?php esc_html_e( 'Default Logo', 'logo_by_conditions' ); ?></label>
							<input type="text" class="mk-plugin-form-control mk-upload" name="default_logo" id="default-logo" value="<?php echo esc_html( get_option( 'default_logo' ) ); ?>" />
						</div>
					</div>

					<div class="mk-plugin-block">
						<div class="mk-plugin-block-title"><?php esc_html_e( 'Usage', 'logo_by_conditions' ); ?></div>
						<pre>$logo_by_conditions = new Logo_by_Conditions();
echo $logo_by_conditions->get_logo_url();</pre>
					</div>
					<div class="mk-plugin-block">
						<div class="mk-plugin-block-title"><?php esc_html_e( 'If you want you can enable auto changer', 'logo_by_conditions' ); ?></div>
						<div class="mk-plugin-form-group">
							<input type="checkbox" class="mk-plugin-form-control" name="auto_changer" value="1" id="auto-changer" <?php echo esc_html( get_option( 'auto_changer' ) === 1 ? 'checked' : '' ); ?> />
							<label for="auto-changer"><?php esc_html_e( 'Enable', 'logo_by_conditions' ); ?></label>
						</div>
						<div class="mk-plugin-form-group">
							<label for="logo-selector"><?php esc_html_e( 'Logo selector', 'logo_by_conditions' ); ?></label>
							<input type="text" class="mk-plugin-form-control" name="logo_selector" id="logo-selector" value="<?php echo esc_html( get_option( 'logo_selector' ) ); ?>" />
							<div class="help-block"><?php esc_html_e( 'If you was enabled auto changer you should write logo selector, like #ex_logo or .ex_logo' ); ?></div>
						</div>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'logo_by_conditions', 'logo_by_conditions' ); ?>
		</form>
	</div>
</div>
