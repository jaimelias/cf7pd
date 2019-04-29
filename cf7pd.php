<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/jaimelias/cf7pd
 * @since             1.0.0
 * @package           Cf7pd
 *
 * @wordpress-plugin
 * Plugin Name:       CF7PD (Marketing Optimized CF7 to Pipedrive)
 * Plugin URI:        https://github.com/jaimelias/cf7pd
 * Description:       Create deals, contacts and track conversions from email marketing campaigns, organic referrals, Adwords and social media in Pipedrive.
 * Version:           1.0.0
 * Author:            jaimelias
 * Author URI:        https://jaimelias.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7pd
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cf7pd-activator.php
 */
function activate_cf7pd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7pd-activator.php';
	Cf7pd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cf7pd-deactivator.php
 */
function deactivate_cf7pd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7pd-deactivator.php';
	Cf7pd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cf7pd' );
register_deactivation_hook( __FILE__, 'deactivate_cf7pd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7pd.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf7pd() {

	$plugin = new Cf7pd();
	$plugin->run();

}
run_cf7pd();


if ( ! function_exists('write_log')) {
	function write_log ( $log )  {
		
		$path = sanitize_text_field($_SERVER['REQUEST_URI']);
		$user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
		$output = '';
		
		if(is_array( $log ))
		{
			$std = (array) $log;
			
			if(!empty($std))
			{
				$log = json_encode($log);
			}
			else
			{
				$log = json_encode($log);
			}
		}	
		else if(is_object( $log ))
		{
			$log = json_encode($log);
		}
		
		$output .= $log;
		$output .= ' '.$path;  
		$output .= ' '.$user_agent;
		error_log($output);
	}
}