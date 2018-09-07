<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://jaimelias.com/
 * @since      1.0.0
 *
 * @package    Cf7pd
 * @subpackage Cf7pd/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7pd
 * @subpackage Cf7pd/public
 * @author     jaimelias <escribeme@jaimelias.com>
 */
class Cf7pd_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version; 
		
		add_shortcode('recaptcha_button', array('Cf7pd_Public', 'recaptcha'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7pd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7pd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 
		global $post; 
		
		if(is_singular())
		{
			if(has_shortcode( $post->post_content, 'contact-form-7') || Cf7pd_Public::shortcode_widget('contact-form-7'))
			{
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7pd-public.css', array(), time(), 'all' );			
			}			
		}

		wp_dequeue_style( 'contact-form-7' );
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7pd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7pd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $post;
		
		wp_enqueue_script('landing-cookies', plugin_dir_url( __FILE__ ) . 'js/cookies.js', array('jquery'), $this->version, true );
		
		
		if(get_option('livechat_license') != null)
		{
			wp_enqueue_script('livechat', plugin_dir_url( __FILE__ ) . 'js/livechat.js', array('jquery', 'landing-cookies'), $this->version, true );
			 wp_add_inline_script( 'livechat', 'function livechat_license(){return '.esc_html(get_option('livechat_license')).';}', 'before' );
			
		}
		

		if(is_singular())
		{
			if(has_shortcode( $post->post_content, 'contact-form-7') || Cf7pd_Public::shortcode_widget('contact-form-7'))
			{
				wp_dequeue_script('google-recaptcha');
					
				wp_enqueue_script('cf7pdJS', plugin_dir_url( __FILE__ ) . 'js/cf7pd-public.js', array('jquery'), $this->version, true );				
				
				wp_enqueue_script('cf7pd-recaptcha', 'https://www.google.com/recaptcha/api.js', array('jquery', 'cf7pdJS'), $this->version, false );
				
			}			
		}
	}
	
	public static function validate_recaptcha($result, $tag)
	{
				
		if(isset($_POST['response']) && isset($_POST['remote-ip']))
		{
			
			$data = array();
			$data['secret'] = sanitize_text_field(get_option('captcha_secret_key'));
			$data['response'] = sanitize_text_field($_POST['response']);
			$data['remoteip'] = sanitize_text_field($_POST['remote-ip']);
			
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$verify = curl_init();
			curl_setopt($verify, CURLOPT_URL, $url);
			curl_setopt($verify, CURLOPT_POST, true);
			curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
			$verify_response = json_decode(curl_exec($verify), true);			
						
			if($verify_response['success'] == false)
			{
				$error_code = __('Invalid Recaptcha', 'cf7pd').': '.implode(", ", $verify_response['error-codes']);
				$result->invalidate('PIPEDRIVE_PERSON_email', esc_html($error_code));
				$result->invalidate('PIPEDRIVE_PERSON_name', esc_html($error_code));
			}	
			
		}
		else
		{
			$error_code = __('remote-ip or response not fount in form', 'cf7pd');			
			$result->invalidate('PIPEDRIVE_PERSON_email', esc_html($error_code));
			$result->invalidate('PIPEDRIVE_PERSON_name', esc_html($error_code));			
		}
		return $result;
	}
	
	public static function enable_shortcodes($form)
	{
		$form = do_shortcode( $form );
		return $form;		
	}
	
	public static function recaptcha()
	{	
		$site_key = '';
		$output = '';
		
		if(get_option('captcha_site_key') != '')
		{
			$site_key = get_option('captcha_site_key');
		}
		
		$output .= '<button class="g-recaptcha strong uppercase pure-button pure-button pure-button-primary" data-badge="bottomleft" data-sitekey="'.esc_html($site_key).'" data-callback="pipedrive_submit">'.esc_html(__('Send Request', 'cf7pd')).'</button>';
		
		$output .= '<div class="hidden"><input type="text" name="response" /><input type="text" name="remote-ip" value="'.esc_html(Cf7pd_Public::get_client_ip()).'" /></div>';
		
		return $output;
		
	}
	
	public static function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}	
	
	public static function shortcode_widget($shortcode)
	{
		global $wp_registered_sidebars;
		$count = 0;
		
		
		//die(var_dump($wp_registered_sidebars));
		
		
		foreach($wp_registered_sidebars as $k => $v)
		{
			$sidebar = $v;
			$sidebar_id = $v['id'];
			
			ob_start();
			dynamic_sidebar($sidebar_id);
			$sidebar_content = ob_get_contents();
			ob_end_clean();
			
			return true;
			
			if(has_shortcode($sidebar_content, $shortcode))
			{
				$count++;
			}	
			
		}
		
		if($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}	

	public static function url()
	{
		echo '<script>function cf7pd_url(){return "'.esc_url(plugin_dir_url( __FILE__ )).'";}</script>';
	}	
	
	public static function pipedrive_submit($form)
	{
		$output = array();
		$deals = array();
		$notes = null;
						
		$submission = WPCF7_Submission::get_instance();
		$subject = WPCF7_ContactForm::get_current();
		$subject = $subject->prop('mail');
		$subject = $subject['subject'];
		
		if ($submission) 
		{
			$posted_data = $submission->get_posted_data();
		}
						
		foreach($posted_data as $key => $value)
		{	
		
			$subject = str_replace($key, $value, $subject);
			$clean_key = preg_replace('/PIPEDRIVE\_PERSON\_/i', '', $key);
			$clean_key = preg_replace('/PIPEDRIVE\_DEAL\_/i', '', $key);

			if($key != '_wpcf7_is_ajax_call' && $key != '_wpcf7' && $key != '_wpcf7_version' && $key != '_wpcf7_locale' && $key != '_wpcf7_unit_tag' && $key != 'g-recaptcha-response')
			{
				if(preg_match('/PIPEDRIVE\_PERSON\_/i', $key))
				{
					$output[$key] = $value;
				}
				elseif(preg_match('/PIPEDRIVE\_DEAL\_/i', $key))
				{
					$output[$key] = $value;
				}
				else
				{
					if(!preg_match('/\_submit/i', $clean_key))
					{
						$notes .= $key.': '.$value.' | ';
					}
				}
			}
		}
				
		$notes .= 'URL: '.esc_url($submission->get_meta('url'));	
		$output['notes'] = $notes;
		$output['PIPEDRIVE_DEAL_title'] = $subject;
		
		Cf7pd_Curl::new_person($output);
		//Cf7pd_Public::debug_log($output);
	}
	
	public static function debug_log($log)
	{
		if ( is_array( $log ) || is_object( $log ) ) 
		{
			error_log( print_r( $log, true ) );
		}
		else
		{
			error_log( $log );
		}
	}
	
	public static function remove_ajax_loader()
	{
		remove_filter( 'wpcf7_ajax_loader', 'filter_wpcf7_ajax_loader', 10, 1 );
	}
	public static function modal_response( $output, $class, $content, $instance)
	{
		return '<div class="modal-container hidden large strong uppercase"><div class="modal-content"><div class="modal-header text-right"><span class="modal-close pointer"><i class="fas fa-times"></i></span></div>'.$output.'</div></div>';
	}
}
