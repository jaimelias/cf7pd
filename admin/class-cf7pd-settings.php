<?php 

class Cf7pd_Settings
{
	
	public static function settings_init()
	{
		
		//setting, id, sanitize
		register_setting('cf7pd-settings', 'pipedrive_token', array(&$this, 'sanitize_token'));

		//geolocation		
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
			array(&$this, 'input'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);
		
		add_settings_field( 
			'ipgeolocation', 
			esc_html(__( 'IPGeolocation API Key', 'cf7pd' )), 
			array(&$this, 'display_ipgeolocation'), 
			'cf7pd-settings', 
			'cf7pd-settings-section' 
		);		
	}
	
	public static function input() { 
		$options = get_option( 'pipedrive_token' );
		?>
		<input type='text' name='pipedrive_token[pipedrive_field_0]' value='<?php echo esc_html($options['pipedrive_field_0']); ?>'><br/> <small><?php echo esc_html(__('Visit your Pipedrive account and go to Settings > Personal > Other > API', 'cf7pd')); ?></small>
		<?php
	}

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
		add_submenu_page( 'wpcf7', 'Pipedrive - Settings', 'Pipedrive', 'manage_options', 'pipedrive', array(&$this, 'settings_page') );
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