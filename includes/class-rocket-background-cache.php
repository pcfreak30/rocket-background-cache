<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Rocket_Background_Cache
 * @subpackage Rocket_Background_Cache/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Rocket_Background_Cache
 * @subpackage Rocket_Background_Cache/includes
 * @author     Derrick Hammer <derrick@derrickhammer.com>
 */
class Rocket_Background_Cache {

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Rocket_Background_Cache $_instance Instance singleton.
	 */
	protected static $_instance;
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Rocket_Background_Cache_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $rocket_background_cache The string used to uniquely identify this plugin.
	 */
	protected $rocket_background_cache;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->_load_dependencies();
		if ( $this->_check_wprocket_loaded() ) {
			$this->_define_public_hooks();
		}
		$this->loader->run();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *     *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function _load_dependencies() {

		$this->loader = new Rocket_Background_Cache_Loader();

	}

	private function _check_wprocket_loaded() {
		if ( did_action( 'deactivate_' . plugin_basename( plugin_dir_path( plugin_dir_path( __FILE__ ) ) . ROCKET_BACKGROUND_CACHE_SLUG . '.php' ) ) ) {
			return false;
		}
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$error         = false;
		$wprocket_name = 'wp-rocket/wp-rocket.php';
		if ( validate_plugin( $wprocket_name ) ) {
			$error = true;
			$this->loader->add_action( 'admin_notices', $this, '_activate_error_no_wprocket' );
		} else {
			if ( ! function_exists( 'rocket_init' ) ) {
				activate_plugins( $wprocket_name );
			}
		}
		if ( $error ) {
			deactivate_plugins( dirname( dirname( __FILE__ ) ) . '/' . ROCKET_BACKGROUND_CACHE_SLUG . '.php' );
		}

		return ! $error;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function _define_public_hooks() {
		remove_action( 'after_rocket_clean_post', 'run_rocket_bot_after_clean_post' );
		remove_action( 'admin_bar_menu', 'rocket_admin_bar' );
		add_action( 'admin_bar_menu', 'rocket_admin_bar', PHP_INT_MAX - 1 );
		$this->loader->add_filter( 'cron_schedules', $this, 'register_cron_schedule' );
		$this->loader->add_action( 'rocket_background_cache_time_event', $this, 'run_cron' );
		$this->loader->add_action( 'admin_bar_menu', $this, 'update_admin_bar_menu', PHP_INT_MAX );
		if ( ! is_admin() ) {
			$this->loader->add_action( 'init', $this, 'check_cron' );
			$this->loader->add_action( 'wp', $this, 'check_page_cache' );
		}
	}

	/**
	 * Get instance of main class
	 *
	 * @since     0.1.0
	 * @return Rocket_Background_Cache
	 */
	public static function get_instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Will search for wp-rocket and use its functions to purge
	 *
	 * @return bool
	 */
	public static function purge_cache() {
		$wprocket_name = 'wp-rocket/wp-rocket.php';
		$path          = trailingslashit( ABSPATH . PLUGINDIR ) . $wprocket_name;
		$mu_path       = trailingslashit( ABSPATH . WPMU_PLUGIN_DIR ) . $wprocket_name;
		if ( is_readable( $path ) ) {
			include_once $path;
		} else if ( is_readable( $mu_path ) ) {
			include_once $mu_path;
		} else {
			return false;
		}
		require_once( WP_ROCKET_FUNCTIONS_PATH . 'admin.php' );
		require_once( WP_ROCKET_ADMIN_PATH . 'admin.php' );
		rocket_clean_domain();

		// Remove all minify cache files
		rocket_clean_minify();

		// Generate a new random key for minify cache file
		$options                   = get_option( WP_ROCKET_SLUG );
		$options['minify_css_key'] = create_rocket_uniqid();
		$options['minify_js_key']  = create_rocket_uniqid();
		remove_all_filters( 'update_option_' . WP_ROCKET_SLUG );
		update_option( WP_ROCKET_SLUG, $options );

		rocket_dismiss_box( 'rocket_warning_plugin_modification' );

		return true;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Rocket_Background_Cache_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Error handler if WP-Rocket is missing
	 */
	public function _activate_error_no_wprocket() {
		$info = get_plugin_data( plugin_dir_path( plugin_dir_path( __FILE__ ) ) . ROCKET_BACKGROUND_CACHE_SLUG . '.php' );
		_e( sprintf( '
	<div class="error notice">
		<p>Opps! %s requires WP-Rocket! Please Download at <a href="http://www.wp-rocket.me">www.wp-rocket.me</a></p>
	</div>', $info['Name'] ) );
	}

	public function register_cron_schedule( $schedules ) {
		$schedules['rocket_background_cache'] = array(
			'interval' => apply_filters( 'rocket_background_cache_schedule', MINUTE_IN_SECONDS ),
			'display'  => __( 'WP Rocket Background Cache', ROCKET_BACKGROUND_CACHE_SLUG )
		);

		return $schedules;
	}

	public function check_cron() {
		if ( 0 < (int) get_rocket_option( 'purge_cron_interval' ) && ! wp_next_scheduled( 'rocket_background_cache_time_event' ) ) {
			wp_schedule_event( time() + ( apply_filters( 'rocket_background_cache_schedule', MINUTE_IN_SECONDS ) ), 'rocket_background_cache', 'rocket_background_cache_time_event' );
		}
	}

	public function run_cron() {
		$posts   = get_posts( [
			'post_type'  => 'any',
			'meta_query' => [
				[
					'key'     => '_rocket_preload_status',
					'compare' => 'EXISTS'
				]
			],
			'nopaging'   => true
		] );
		$cookies = array();
		foreach ( $posts as $post ) {
			$status = (array) get_post_meta( $post->ID, '_rocket_preload_status', true );
			foreach ( $status as $user_id => $user_status ) {
				if ( ! isset( $user_status['status'] ) || 'queued' != $user_status['status'] ) {
					continue;
				}
				if ( empty( $cookies[ $user_id ] ) ) {
					$cookies[ $user_id ] = array();
				}
				if ( 0 < $user_id ) {
					$session = WP_Session_Tokens::get_instance( $user_id )->get( $user_status['session'] );

					if ( empty( $session ) ) {
						continue;
					}
					if ( empty( $cookies[ $user_id ][ LOGGED_IN_COOKIE ] ) ) {
						$cookies[ $user_id ][ LOGGED_IN_COOKIE ] = wp_generate_auth_cookie( $user_id, $session['expiration'], 'logged_in', $user_status['session'] );

					}
					if ( empty( $cookies[ $user_id ][ is_ssl() ? SECURE_AUTH_COOKIE : AUTH_COOKIE ] ) ) {
						$cookies[ $user_id ][ is_ssl() ? SECURE_AUTH_COOKIE : AUTH_COOKIE ] = wp_generate_auth_cookie( $user_id, $session['expiration'], is_ssl() ? 'secure_auth' : 'auth', $user_status['session'] );
					}
					if ( empty( $cookies[ $user_id ][ TEST_COOKIE ] ) ) {
						$cookies[ $user_id ][ TEST_COOKIE ] = 'WP Cookie check';
					}
				}
				wp_remote_get(
					get_the_permalink( $post->ID ),
					array(
						'timeout'   => 60,
						'blocking'  => false,
						'sslverify' => false,
						'cookies'   => $cookies[ $user_id ]
					)
				);
				if ( 0 == get_option( 'show_on_front' ) ) {
					rocket_clean_home();
					wp_remote_get(
						home_url( '/' ),
						array(
							'timeout'   => 60,
							'blocking'  => false,
							'sslverify' => false,
							'cookies'   => ( 0 < $user_id ) ? $cookies[ $user_id ] : array(),
						)
					);

				}
			}
		}
	}

	public function check_page_cache() {
		if ( ! is_admin() ) {
			if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				$user_id = get_current_user_id();
				$status  = (array) get_post_meta( get_the_ID(), '_rocket_preload_status', true );
				if ( false === strpos( $_SERVER['HTTP_USER_AGENT'], 'WordPress' ) && ( ( is_user_logged_in() && get_rocket_option( 'cache_logged_user' ) ) || ! is_user_logged_in() ) ) {
					if ( empty( $status[ $user_id ] ) || ! is_array( $status[ $user_id ] ) ) {
						$status[ $user_id ] = array();
					}
					if ( empty( $status[ $user_id ]['status'] ) || 'preloaded' == $status[ $user_id ]['status'] ) {
						$status[ $user_id ] = array(
							'status'  => 'queued',
							'session' => wp_get_session_token()
						);
						update_post_meta( get_the_ID(), '_rocket_preload_status', $status );
					}
					if ( 'queued' == $status[ $user_id ]['status'] ) {
						define( 'DONOTCACHEPAGE', true );
					}

				} else {
					$status[ $user_id ]['status'] = 'preloaded';
					update_post_meta( get_the_ID(), '_rocket_preload_status', $status );
				}
			}
		}
	}

	public function update_admin_bar_menu( $wp_admin_bar ) {
		/** @var WP_Admin_Bar $wp_admin_bar */
		$langlinks = array();
		if ( rocket_is_plugin_active( 'qtranslate/qtranslate.php' ) ) {
			$langlinks = get_rocket_qtranslate_langs_for_admin_bar();
		} else if ( rocket_is_plugin_active( 'qtranslate-x/qtranslate.php' ) ) {
			$langlinks = get_rocket_qtranslate_langs_for_admin_bar( 'x' );
		} else if ( rocket_is_plugin_active( 'polylang/polylang.php' ) ) {
			$langlinks = get_rocket_polylang_langs_for_admin_bar();
		}
		foreach ( $langlinks as $lang ) {
			$wp_admin_bar->remove_menu( 'preload-cache-' . $lang['code'] );
		}
		$wp_admin_bar->remove_menu( 'preload-cache' );
	}
}
