<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Rocket_Background_Cache
 * @subpackage Rocket_Background_Cache/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Rocket_Background_Cache
 * @subpackage Rocket_Background_Cache/includes
 * @author     Derrick Hammer <derrick@derrickhammer.com>
 */
class Rocket_Background_Cache_Activator {

	/**
	 * Purge Cache
	 *
	 * @since    0.1.0
	 */
	public static function activate() {
		rocket_background_cache()->get_loader()->add_action( 'activated_plugin', rocket_background_cache(), 'purge_cache' );
		rocket_background_cache()->get_loader()->run();
	}

}
