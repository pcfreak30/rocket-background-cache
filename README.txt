=== Plugin Name ===
Contributors: pcfreak30
Donate link: https://www.paypal.me/pcfreak30
Tags: wp-rocket, cache
Requires at least: 4.5
Tested up to: 4.5.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin to create page cache on demand in the background via wp-cron to reduce load. Extends WP-Rocket

== Description ==

This plugin will defer all cache pre-loading to wp-cron. If a page request comes and a page is not cached yet, wp-rocket will be disabled. However WP-Rocket functions using filters for example CDN will still function. Anything that uses WP-Rockets output buffering via rocket_buffer` will be disabled though.

The cron time is currently set via filter defaulting to per minute.

It is **recommended** to run wp-cron via unix cron either as a web fetch, WPCLI, or another means.

This plugin works with logged in users as well by simulating the current users session cookies when requesting a page to cache.

== Installation ==

1. Upload `rocket-background-cache.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Optionally configure wp-cron with unix. Talk to your host/system administrator if required.

== Changelog ==

## 0.1.0 ##

* Initial Release