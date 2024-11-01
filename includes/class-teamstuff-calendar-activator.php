<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.teamstuff.com
 * @since      1.0.0
 *
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/includes
 * @author     Tom Wright <tom.wright@shineproducts.com.au>
 */
class Teamstuff_Calendar_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// if required support for REST routes isn't present, then set our transient flag to display our notice about REST support
		if(!function_exists('register_rest_route')) {
			set_transient('teamstuff-calendar_rest_transient', true, 5);
		}
	}

}
