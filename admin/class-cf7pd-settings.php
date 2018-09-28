<?php 

class Cf7pd_Settings
{
	
	public static function settings_init()
	{
		
		remove_submenu_page('wpcf7', 'wpcf7-integration');
		
		//setting, id, sanitize
		register_setting( 'cf7pd-settings', 'pipedrive_token', array('Cf7pd_Settings', 'sanitize_token'));

		//recaptcha wordpress global option
		register_setting('cf7pd-settings', 'captcha_site_key', 'sanitize_text_field');
		register_setting('cf7pd-settings', 'captcha_secret_key', 'sanitize_text_field');			
		register_setting('cf7pd-settings', 'ipgeolocation', 'sanitize_text_field');			

		add_settings_section(
			'cf7pd-settings-section', 
			esc_html(__( 'Pipedrive', 'cf7pd' )), 
			'', 
			'cf7pd-settings'
		);

		add_settings_field( 
			'pipedrive_field_0',
			esc_html(__( 'Pipedrive Token', 'cf7pd' )), 
			array('Cf7pd_Settings', 'pipedrive_field_0_render'), 
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
			'ipgeolocation', 
			esc_html(__( 'IPGeolocation API Key', 'cf7pd' )), 
			array('Cf7pd_Settings', 'display_ipgeolocation'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);				
		
	}
	
	public static function pipedrive_field_0_render(  ) { 
		$options = get_option( 'pipedrive_token' );
		?>
		<input type='text' name='pipedrive_token[pipedrive_field_0]' value='<?php echo esc_html($options['pipedrive_field_0']); ?>'><br/> <small><?php echo esc_html(__('Visit your Pipedrive account and go to Settings > Personal > Other > API', 'cf7pd')); ?></small>
		<?php
	}
	
	
	public static function display_captcha_site_key_element() { ?>
		<input type="text" name="captcha_site_key" id="captcha_site_key" value="<?php echo esc_html(get_option('captcha_site_key')); ?>" /> <a target="_blank" href="https://www.google.com/recaptcha/admin">Invisible Recaptcha</a>
	<?php }

	public static function display_captcha_secret_key_element() { ?>
		<input type="text" name="captcha_secret_key" id="captcha_secret_key" value="<?php echo esc_html(get_option('captcha_secret_key')); ?>" /> <a target="_blank" href="https://www.google.com/recaptcha/admin">Invisible Recaptcha</a>
	<?php }	

	public static function display_ipgeolocation() { ?>
		<input type="text" name="ipgeolocation" id="ipgeolocation" value="<?php echo esc_html(get_option('ipgeolocation')); ?>" /> <a target="_blank" href="https://app.ipgeolocation.io/auth/login">IPGeolocation</a>
	<?php }	
	
	public static function sanitize_token( $input ) {
		$valid = array();
		$valid['pipedrive_field_0'] = sanitize_text_field( $input['pipedrive_field_0'] );
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