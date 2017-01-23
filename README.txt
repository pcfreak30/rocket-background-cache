=== Plugin Name ===
Contributors: pcfreak30
Donate link: https://www.paypal.me/pcfreak30
Tags: wp-rocket, cache
Requires at least: 4.5
Tested up to: 4.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin to create page cache on demand in the background via wp-cron to reduce load. Extends WP-Rocket

== Description ==

This plugin will defer all cache pre-loading to wp-cron. If a page request comes and a page is not cached yet, wp-rocket will be disabled. However WP-Rocket functions using filters for example CDN will still function. Anything that uses WP-Rockets output buffering via rocket_buffer` will be disabled though.

The cron time is currently set via filter defaulting to per minute.

It is **recommended** to run wp-cron via unix cron either as a web fetch, WPCLI, or another means.

This plugin works with logged in users as well by simulating the current users session cookies when requesting a page to cache.

If you need dedicated/professional assistance with this plugin or just want an expert to get your site to run the fastest it can be, you may hire me at [Codeable](https://codeable.io/developers/derrick-hammer/?ref=rvtGZ)

== Installation ==

1. Upload `rocket-background-cache.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Optionally configure wp-cron with unix. Talk to your host/system administrator if required.

== Changelog ==

## 0.1.4 ##

* Bug: Don't set DONOTCACHEPAGE if it already exists

## 0.1.3 ##

* Bug: Only queue if we are on an actual post
* Enhancement: Add wp-engine hosting compatibility

## 0.1.2 ##

* Bug: If show_on_front is not set, only purge home, not all user cache
* Bug: Only pass cookies if logged in

## 0.1.1 ##

* Bug: Add trailing slash on home url as wp-rocket seems to not cache the page otherwise

## 0.1.0 ##

* Initial Release