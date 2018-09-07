<?php 

class Cf7pd_Settings
{
	
	public static function settings_init()
	{
		
		remove_submenu_page('wpcf7', 'wpcf7-integration');
		
		//setting, id, sanitize
		register_setting( 'cf7pd-settings', 'pipedrive_token', array('Cf7pd_Settings', 'sanitize_token'));
		register_setting( 'cf7pd-settings', 'pipedrive_closing_enable', array('Cf7pd_Settings', 'sanitize_closing_enable'));
		register_setting( 'cf7pd-settings', 'pipedrive_closing_days', array('Cf7pd_Settings', 'sanitize_closing_days'));
		register_setting( 'cf7pd-settings', 'pipedrive_adwords_conversion', 'sanitize_adwords_conversion');	

		//recaptcha wordpress global option
		register_setting('cf7pd-settings', 'captcha_site_key', 'sanitize_text_field');
		register_setting('cf7pd-settings', 'captcha_secret_key', 'sanitize_text_field');			
		register_setting('cf7pd-settings', 'livechat_license', 'sanitize_text_field');			

		add_settings_section(
			'cf7pd-settings-section', 
			esc_html(__( 'Pipedrive', 'cf7pd' )), 
			'', 
			'cf7pd-settings'
		);

		add_settings_field( 
			'pipedrive_field_0',
			esc_html(__( 'Token', 'cf7pd' )), 
			array('Cf7pd_Settings', 'pipedrive_field_0_render'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);
		
		add_settings_field( 
			'pipedrive_field_1',
			esc_html(__( 'Enable Deals', 'cf7pd' )), 
			array('Cf7pd_Settings', 'pipedrive_field_1_render'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);			

		add_settings_field( 
			'pipedrive_field_2',
			esc_html(__( 'Days to Close Deals', 'cf7pd' )), 
			array('Cf7pd_Settings', 'pipedrive_field_2_render'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);	

		add_settings_field( 
			'pipedrive_field_3',
			esc_html(__( 'Adwords Conversion Key', 'cf7pd' )), 
			array('Cf7pd_Settings', 'pipedrive_field_3_render'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);


		add_settings_field( 
			'captcha_site_key', 
			esc_html(__( 'Recaptcha Site Key', 'cf7pd' )), 
			array('Cf7pd_Settings', 'display_captcha_site_key_element'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);	

		add_settings_field( 
			'captcha_secret_key', 
			esc_html(__( 'Recaptcha Secret Key', 'cf7pd' )), 
			array('Cf7pd_Settings', 'display_captcha_secret_key_element'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);

		add_settings_field( 
			'livechat_license', 
			esc_html(__( 'Livechat License', 'cf7pd' )), 
			array('Cf7pd_Settings', 'display_livechat_license'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);		
		
	}
	
	public static function pipedrive_field_0_render(  ) { 
		$options = get_option( 'pipedrive_token' );
		?>
		<input type='text' name='pipedrive_token[pipedrive_field_0]' value='<?php echo esc_html($options['pipedrive_field_0']); ?>'>
		<?php
	}
	
	public static function pipedrive_field_1_render(  ) { 
		$options = get_option( 'pipedrive_closing_enable' );
		?>
		<select name='pipedrive_closing_enable[pipedrive_field_1]'>
			<option value="1" <?php selected( $options['pipedrive_field_1'], 1 ); ?> ><?php echo esc_html(__('No', 'dynamicpackages')); ?></option>
			<option value="2" <?php selected( $options['pipedrive_field_1'], 2 ); ?> ><?php echo esc_html(__('Yes', 'dynamicpackages')); ?></option>
		</select>
		<?php
	}
	
	public static function pipedrive_field_2_render(  ) { 
		$options = get_option( 'pipedrive_closing_days' );
		?>
		<input type='text' name='pipedrive_closing_days[pipedrive_field_2]' value='<?php echo esc_html($options['pipedrive_field_2']); ?>'>
		<?php
	}

	public static function pipedrive_field_3_render(  ) { 
		$options = get_option( 'pipedrive_adwords_conversion' );
		?>
		<input type='text' name='pipedrive_adwords_conversion[pipedrive_field_3]' value='<?php echo esc_html($options['pipedrive_field_3']); ?>'>
		<?php
	}	
	
	public static function display_captcha_site_key_element() { ?>
		<input type="text" name="captcha_site_key" id="captcha_site_key" value="<?php echo esc_html(get_option('captcha_site_key')); ?>" />
	<?php }

	public static function display_captcha_secret_key_element() { ?>
		<input type="text" name="captcha_secret_key" id="captcha_secret_key" value="<?php echo esc_html(get_option('captcha_secret_key')); ?>" />
	<?php }	

	public static function display_livechat_license() { ?>
		<input type="text" name="livechat_license" id="livechat_license" value="<?php echo esc_html(get_option('livechat_license')); ?>" />
	<?php }		
	
	public static function sanitize_token( $input ) {
		$valid = array();
		$valid['pipedrive_field_0'] = sanitize_text_field( $input['pipedrive_field_0'] );
		return $valid;
	}
	
	public static function sanitize_closing_enable( $input ) {
		$valid = array();
		$valid['pipedrive_field_1'] = sanitize_text_field( $input['pipedrive_field_1'] );
		return $valid;
	}		
	public static function sanitize_closing_days( $input ) {
		
		$valid = array();
		$valid['pipedrive_field_2'] = intval(sanitize_text_field( $input['pipedrive_field_2'] ));
		return $valid;
	}	
	public static function sanitize_adwords_conversion( $input ) {
		$valid = array();
		$valid['pipedrive_field_3'] = sanitize_text_field( $input['pipedrive_field_3'] );
		return $valid;
	}		
	public static function add_settings_page()
	{
		add_submenu_page( 'wpcf7', 'Pipedrive - Settings', 'Pipedrive', 'manage_options', 'pipedrive', array('Cf7pd_Settings', 'settings_page') );
	}
	public static function settings_page()
	{ 
		?><div class="wrap">
		<div class="card active">
		<form action='options.php' method='post'>
		<?php
			settings_fields( 'cf7pd-settings' );
			do_settings_sections( 'cf7pd-settings' );
			submit_button();
		?>			
		</form>
		</div></div>
		<?php
	}
	public static function invalid_token()
	{
		if(isset($_GET["page"]))
		{
			if(Cf7pd_Admin::is_cf7_page())
			{

				$fields = Cf7pd_Curl::get_PersonFields();
				
				if(!array_key_exists('data', $fields))
				{
					echo '<div class="error"><p>'.__('Invalid or Empty Pipedrive Token', 'cf7pd').'</p></div>';
				}
			}
		}
	}
	
}

?>