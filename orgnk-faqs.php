<?php
/*
Plugin Name:    Organik FAQs
Description:    Create and manage FAQs
Version:        1.0.0
Author:         Organik Web
Author URI:     https://www.organikweb.com.au/
License:        GNU General Public License v2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Current plugin version
 */
define( 'ORGNK_FAQS_VERSION', '1.0.0' );

/**
 * Register activation hook
 * This action is documented in inc/class-activator.php
 */
function orgnk_faqs_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-activator.php';
	Organik_FAQs_Activator::activate();
}
register_activation_hook( __FILE__, 'orgnk_faqs_activate_plugin' );

/**
 * Register deactivation hook
 * This action is documented in inc/class-activator.php
 */
function orgnk_faqs_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-activator.php';
	Organik_FAQs_Activator::deactivate();
}
register_deactivation_hook( __FILE__, 'orgnk_faqs_deactivate_plugin' );

/*
 * Load dependencies
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-cpt-faqs.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/class-tax-faqs-categories.php';

/**
 * Load helper functions
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/helpers.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/schema.php';

/**
 * Run the main instance of this plugin
 */
Organik_FAQs::instance();
