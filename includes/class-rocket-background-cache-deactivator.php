<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Rocket_Background_Cache
 * @subpackage Rocket_Background_Cache/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Rocket_Background_Cache
 * @subpackage Rocket_Background_Cache/includes
 * @author     Derrick Hammer <derrick@derrickhammer.com>
 */
class Rocket_Background_Cache_Deactivator {

	/**
	 * Purge Cache
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {
		rocket_background_cache()->get_loader()->add_action( 'deactivated_plugin', rocket_background_cache(), 'purge_cache' );
		rocket_background_cache()->get_loader()->run();
	}

}
