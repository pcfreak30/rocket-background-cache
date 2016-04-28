<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             0.1.0
 * @package           Rocket_Background_Cache
 *
 * @wordpress-plugin
 * Plugin Name:       WP-Rocket Background Cache
 * Plugin URI:        https://github.com/pcfreak30/rocket-background-cache
 * Description:       WordPress plugin to create page cache on demand in the background via wp-cron to reduce load. Extends WP-Rocket
 * Version:           0.1.1
 * Author:            Derrick Hammer <derrick@derrickhammer.com>
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rocket-background-cache
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'ROCKET_BACKGROUND_CACHE_VERSION', '0.1.1' );
define( 'ROCKET_BACKGROUND_CACHE_SLUG', 'rocket-background-cache' );
/**
 * Activation hooks
 */
register_activation_hook( __FILE__, array( 'Rocket_Background_Cache_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Rocket_Background_Cache_Deactivator', 'deactivate' ) );

/**
 * Autoloader function
 *
 * Will search both plugin root and includes folder for class. Provides a filter for additional paths
 *
 * @param string $class_name
 */
if ( ! function_exists( 'rocket_background_cache_autoloader' ) ):
	function rocket_background_cache_autoloader( $class_name ) {
		$file      = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
		$base_path = plugin_dir_path( __FILE__ );

		$paths = apply_filters( 'rocket_background_cache_autoloader_paths', array(
			$base_path . $file,
			$base_path . 'includes/' . $file,
			$base_path . 'public/' . $file,
		) );
		foreach ( $paths as $path ) {

			if ( is_readable( $path ) ) {
				include_once( $path );

				return;
			}
		}
	}

	spl_autoload_register( 'rocket_background_cache_autoloader' );
endif;

if ( ! function_exists( 'rocket_background_cache_init' ) ):

	/**
	 * Function to initialize plugin
	 */
	function rocket_background_cache_init() {
		rocket_background_cache()->run();
	}

	add_action( 'plugins_loaded', 'rocket_background_cache_init', 11 );

endif;
if ( ! function_exists( 'rocket_background_cache' ) ):

	/**
	 * Function wrapper to get instance of plugin
	 *
	 * @return Rocket_Background_Cache
	 */
	function rocket_background_cache() {
		return Rocket_Background_Cache::get_instance();
	}

endif;

