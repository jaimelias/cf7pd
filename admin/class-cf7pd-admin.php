<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://jaimelias.com/
 * @since      1.0.0
 *
 * @package    Cf7pd
 * @subpackage Cf7pd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7pd
 * @subpackage Cf7pd/admin
 * @author     jaimelias <escribeme@jaimelias.com>
 */
class Cf7pd_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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
		 if(Cf7pd_Admin::is_cf7_page())
		 {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7pd-admin.css', array(), $this->version, 'all' );		 
		 }

	}

	/**
	 * Register the JavaScript for the admin area.
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

		 if(Cf7pd_Admin::is_cf7_page())
		 {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7pd-admin.js', array( 'jquery' ), time(), false );		 
		 }

	}
	public static function is_cf7_page()
	{
		if(isset($_GET['page']))
		{
			if($_GET["page"] == 'pipedrive' || $_GET["page"] == 'wpcf7-integration' || $_GET['page'] == 'wpcf7' || $_GET['page'] == 'wpcf7-new')
			{
				return true;
			}
			else
			{
				return false;
			}			
		}
		else
		{
			return false;
		}
	}
	public static function javascript_PersonFields()
	{
		echo '<script>function cf7pd_fields(){return '.json_encode(array_merge(Cf7pd_Curl::filter_fields(Cf7pd_Curl::get_DealFields(), 'DEAL'), Cf7pd_Curl::filter_fields(Cf7pd_Curl::get_PersonFields(), 'PERSON'))).';}</script>';
		echo '<script>function cf7pd_url(){return "'.esc_url(plugin_dir_url( __FILE__ )).'";}</script>';
	}	

}
