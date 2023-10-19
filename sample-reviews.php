<?php
/*
Plugin Name: Sample Reviews for WooCommerce
Description: Fast and easy way to add sample reviews to yor store.
Author: WPdot.org (formerly WPCodeMentors)
Author URI: https://wpdot.org
Text Domain: wpdsr
Requires at least: 5.6
Requires PHP: 7.0
Version: 1.0
*/

defined( 'ABSPATH' ) || exit;

// define

define( 'WPDSR_VERSION', '1.0' );
define( 'WPDSR_DIR', plugin_dir_path( __FILE__ ) ); // with /
define( 'WPDSR_URL', plugin_dir_url( __FILE__ ) ); // with /
define( 'WPDSR_FILE', plugin_basename( __FILE__ ) ); // with /

// init

require __DIR__ . '/classes/class-helper.php';
require __DIR__ . '/classes/class-options.php';
require __DIR__ . '/class-main.php';
$wpdsr = new Wpdsr\Main();
