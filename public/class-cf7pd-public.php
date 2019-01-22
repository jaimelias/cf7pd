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
		
		if(is_array($post))
		{
			if(array_key_exists('post_content', $post))
			{
				if(has_shortcode( $post->post_content, 'contact-form-7'))
				{
					wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7pd-public.css', array(), time(), 'all' );
					Cf7pd_Public::datepickerCSS();
				}
			}
			
		}
		if(Cf7pd_Public::shortcode_widget('contact-form-7'))
		{
			Cf7pd_Public::datepickerCSS();
		}		
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
		
		wp_enqueue_script('landing-cookies', plugin_dir_url( __FILE__ ) . 'js/cookies.js', array('jquery'), time(), true );
		
		if(is_array($post))
		{
			if(array_key_exists('post_content', $post))
			{
				if(has_shortcode( $post->post_content, 'contact-form-7'))
				{
					Cf7pd_Public::script_list();
				}
			}			
		}
		
		
		if(Cf7pd_Public::shortcode_widget('contact-form-7'))
		{
			Cf7pd_Public::script_list();
		}
		
		$ipgeolocation_api = null;
		
		if(get_option('ipgeolocation') != null)
		{
			$ipgeolocation_api = get_option('ipgeolocation');
		}
		wp_add_inline_script( 'cf7pdJS', 'function ipgeolocation_api(){ return "'.esc_html($ipgeolocation_api).'";}', 'before' );		
		
	}
	
	public static function script_list()
	{
		//recaptcha
		wp_dequeue_script('google-recaptcha');
		wp_enqueue_script('invisible-recaptcha', 'https://www.google.com/recaptcha/api.js', array('jquery'), 'async_defer', true );
		
		//datepicker
		Cf7pd_Public::datepickerJS();

		//public.js
		wp_enqueue_script('cf7pdJS', plugin_dir_url( __FILE__ ) . 'js/cf7pd-public.js', array('invisible-recaptcha', 'jquery'), time(), true );
		
		wp_add_inline_script('cf7pdJS', 'function cf7pd_url(){return "'.esc_url(plugin_dir_url( __FILE__ )).'";}', 'before');
		
	}
	
	public static function datepickerCSS()
	{
		wp_enqueue_style( 'picker-css', plugin_dir_url( __FILE__ ) . 'css/picker/default.css', array(), 'jetcharters', 'all' );
		wp_add_inline_style('picker-css', self::get_inline_css('picker/default.date'));
		wp_add_inline_style('picker-css', self::get_inline_css('picker/default.time'));		
	}		
	
	public static function datepickerJS()
	{
		//pikadate
		wp_enqueue_script( 'picker-js', plugin_dir_url( __FILE__ ) . 'js/picker/picker.js', array('jquery'), '3.5.6', true);
		wp_enqueue_script( 'picker-date-js', plugin_dir_url( __FILE__ ) . 'js/picker/picker.date.js', array('jquery', 'picker-js'), '3.5.6', true);
		wp_enqueue_script( 'picker-time-js', plugin_dir_url( __FILE__ ) . 'js/picker/picker.time.js',array('jquery', 'picker-js'), '3.5.6', true);	
		wp_enqueue_script( 'picker-legacy', plugin_dir_url( __FILE__ ) . 'js/picker/legacy.js', array('jquery', 'picker-js'), '3.5.6', true);

		$picker_translation = 'js/picker/translations/'.substr(get_locale(), 0, -3).'.js';

		if(file_exists(get_template_directory().$picker_translation))
		{
			wp_enqueue_script( 'picker-time-translation', plugin_dir_url( __FILE__ ). $picker_translation, array('jquery', 'picker-js'), '3.5.6', true);
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
	
	public static function recaptcha($attr)
	{	
		$site_key = '';
		$output = '';
		$label = __('Send request', 'cf7pd');
		$classes = 'g-recaptcha';
		
		if(get_option('captcha_site_key') != '')
		{
			$site_key = get_option('captcha_site_key');
		}
		
		if(is_array($attr))
		{	
			if(count($attr) > 1)
			{
				
				
				if(!preg_match('/class/', end($attr)))
				{
					$label = end($attr);
				}
				
				for($x = 0; $x < count($attr); $x++)
				{
					$classes .= ' '.str_replace('class:', '', $attr[$x]);
				}				
			}
		}

		
		$output .= '<button class="'.$classes.'" data-badge="bottomleft" data-sitekey="'.esc_html($site_key).'" data-callback="pipedrive_submit">'.esc_html($label).'</button>';
		
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
			$posted_data['_post_title'] = get_the_title($posted_data['_wpcf7_container_post']);
		}
						
		foreach($posted_data as $key => $value)
		{	
		
			if(is_array($value))
			{
				$value = implode(", ", $value);
			}
		
			$subject = str_replace($key, $value, $subject);
			$clean_key = preg_replace('/PIPEDRIVE\_PERSON\_/i', '', $key);
			$clean_key = preg_replace('/PIPEDRIVE\_DEAL\_/i', '', $key);

			if($key != '_wpcf7_is_ajax_call' && $key != '_wpcf7' && $key != '_wpcf7_version' && $key != '_wpcf7_locale' && $key != '_wpcf7_unit_tag' && $key != 'g-recaptcha-response' && $key != 'response' && $key != '_wpcf7_container_post')
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
						$notes .= '<strong>'.$key.':</strong> '.$value.'<br/>';
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
	
	public static function footer()
	{
		echo '<div id="cf7pd-datepicker"></div><div id="cf7pd-timepicker"></div>';
	}
	public static function get_inline_css($file)
	{
		ob_start();
		require_once(dirname( __FILE__ ) . '/css/'.$file.'.css');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;			
	}		
}