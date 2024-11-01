<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.teamstuff.com
 * @since             1.0.0
 * @package           Teamstuff_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Teamstuff Calendar
 * Plugin URI:        http://www.teamstuff.com
 * Description:       Provides a widget for your WordPress site that will display the fixtures and scores for your Teamstuff Club!
 * Version:           1.1.0
 * Author:            Teamstuff
 * Author URI:        http://www.teamstuff.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       teamstuff-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define debug for development
//define( 'WP_DEBUG', true );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-teamstuff-calendar-activator.php
 */
function activate_teamstuff_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-teamstuff-calendar-activator.php';
	Teamstuff_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-teamstuff-calendar-deactivator.php
 */
function deactivate_teamstuff_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-teamstuff-calendar-deactivator.php';
	Teamstuff_Calendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_teamstuff_calendar' );
register_deactivation_hook( __FILE__, 'deactivate_teamstuff_calendar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-teamstuff-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_teamstuff_calendar() {

	$plugin = new Teamstuff_Calendar();
	$plugin->run();

}
run_teamstuff_calendar();
