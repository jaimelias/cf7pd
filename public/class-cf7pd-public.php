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
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		 
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


	public function enqueue_scripts() {


		global $post;
		self::cf7_dequeue_recaptcha();		
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
		$dep = array('jquery');
		
		//datepicker
		Cf7pd_Public::datepickerJS();

		//public.js
		wp_enqueue_script('cf7pdJS', plugin_dir_url( __FILE__ ) . 'js/cf7pd-public.js', $dep, time(), true );
		wp_add_inline_script('cf7pdJS', 'function cf7pd_url(){return "'.esc_url(plugin_dir_url( __FILE__ )).'";}', 'before');
		wp_add_inline_script('cf7pdJS', self::get_inline_js('cf7pd-func'), 'after');
		
	}
	public static function cf7_dequeue_recaptcha()
	{
		$dequeu = true;
		
		if(is_singular())
		{
			global $post;
			
			if(has_shortcode($post->post_content, 'contact-form-7'))
			{
				$dequeu = false;
			}
		}
		
		if($dequeu === true)
		{
			wp_dequeue_script('google-recaptcha');
		}
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
				
		if(file_exists(dirname( __FILE__ ).'/'.$picker_translation))
		{
			wp_enqueue_script( 'picker-time-translation', plugin_dir_url( __FILE__ ).$picker_translation, array('jquery', 'picker-js'), '3.5.6', true);
		}		
	}	
	
	public static function shortcode_widget($shortcode)
	{
		global $wp_registered_sidebars;
		$count = 0;		
		
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
		
			$excluded = array('_wpcf7_is_ajax_call', '_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post', 'g-recaptcha-response');
			$subject = str_replace($key, $value, $subject);
			$clean_key = preg_replace('/PIPEDRIVE\_PERSON\_/i', '', $key);
			$clean_key = preg_replace('/PIPEDRIVE\_DEAL\_/i', '', $key);

			if(!in_array($key, $excluded))
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
					$notes .= '<strong>'.$key.':</strong>&nbsp;'.$value.'<br/>';
				}
			}
		}
				
		$notes .= 'URL: '.esc_url($submission->get_meta('url'));	
		$output['notes'] = $notes;
		$output['PIPEDRIVE_DEAL_title'] = $subject;
		
		Cf7pd_Curl::new_person($output);
		//write_log($output);
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
	public static function get_inline_js($file)
	{
		ob_start();
		require_once(dirname( __FILE__ ) . '/js/'.$file.'.js');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;			
	}	
}