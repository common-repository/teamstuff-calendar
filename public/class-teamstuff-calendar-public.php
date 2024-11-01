<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.teamstuff.com
 * @since      1.0.0
 *
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/public
 * @author     Tom Wright <tom.wright@shineproducts.com.au>
 */
class Teamstuff_Calendar_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The URL to which GET requests are sent to retrieve events
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $events_url    The URL for club games to be retrieved from
	 */
	private $events_url = "http://teamstuff.com/data/club-games/";

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Teamstuff_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Teamstuff_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/teamstuff-calendar-public.css', array(), $this->version, 'all' );

		// bootstrap
		wp_enqueue_style( 'bootstrap-css', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css', array(), '3.3.6', 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Teamstuff_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Teamstuff_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/teamstuff-calendar.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-hook', plugin_dir_url( __FILE__ ) . 'js/teamstuff-calendar-public.js', array( 'jquery' ), $this->version, false );

		// bootstrap
		wp_enqueue_script( 'bootstrap-js', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', array( 'jquery' ), '3.3.6', false );

		// moment.js
		wp_enqueue_script( 'moment-js', plugin_dir_url(__FILE__ ) . 'js/moment-with-locales.js', array( 'jquery' ), '2.14.1', false);
	}

	/**
	 * Registers the API endpoints used by the client served by this plugin
	 *
	 * @since    1.0.0
	 */
	public function register_api() {

		// register the events endpoint for single dates
		register_rest_route( $this->plugin_name, '/events/', array(
        'methods' => 'GET',
        'callback' => array($this, 'rest_get_events'),
    ) );

		// register the events endpoint for date range
		register_rest_route( $this->plugin_name, '/events-range/', array(
        'methods' => 'GET',
        'callback' => array($this, 'rest_get_events_range'),
    ) );
	}

	/**
	 * Handles requests to the 'events' endpoint, performing a GET request to retrieve events
	 *
	 * @since    1.0.0
	 */
	public function rest_get_events($req) {
		// prepare parameters for the request
		$api_key = get_option($this->plugin_name . '_apikey');
		$url = $this->events_url . $api_key;
		$params = $req->get_params();
		$date = $params["date"];
		if($date) {
			$url .= '/' . $date;
		}
		$dir = $params["direction"];
		if($dir) {
			$url .= '/' . $dir;
		}

		// prepare the request
		global $wp_version;
		$args = array(
		    'user-agent'  => 'WordPress/' . $wp_version . ' ' . 'TeamstuffCalendar/' . $this->version,
				'timeout' 		=> 15,
		    'headers'     => array(
					'Accept' => 'application/vnd.teamstuff.com.v10+json, application/json'
				)
		);

		// perform the request
		$resp = wp_remote_get($url, $args);
		$status = 200;

		// handle failure cases and success case
		if ( is_wp_error( $resp ) ) {
		  $error_message = $resp->get_error_message();
			$resp = array('error' => $error_message);
			$status = 500;
		} else {
			$status = $resp['response']['code'];
			if( $status != 200) {
				$resp = array('error' => $resp['response']['message'], 'version' => $this->version);
			} else {
				$resp = json_decode($resp['body']);
			}
		}

		// correctly wrap the response and set status code
		$resp = rest_ensure_response($resp);
		$resp->set_status($status);
		return $resp;
	}

	/**
	 * Handles requests to the 'events-range' endpoint, performing a GET request to retrieve events for a date range
	 *
	 * @since    1.0.4
	 */
	public function rest_get_events_range($req) {
		// prepare base url using the API key
		$api_key = get_option($this->plugin_name . '_apikey');
		$url = $this->events_url . $api_key;

		// extract start_date and end_date params into an array
		$req_params = $req->get_params();
		$start_date = $req_params["startDate"];
		$end_date = $req_params["endDate"];
		$params = array();
		if($start_date) {
			$params[] = 'start_date=' . $start_date;
		}
		if($end_date) {
			$params[] = 'end_date=' . $end_date;
		}

		// concatenate params onto the url as a valid query string
		$num_params = count($params);
		for($i = 0; $i < $num_params; $i++) {
			if($i == 0) {
				$url .= '?';
			}
			$url .= $params[$i];
			if($i < $num_params-1) {
				$url .= '&' ;
			}
		}

		// prepare the request
		global $wp_version;
		$args = array(
		    'user-agent'  => 'WordPress/' . $wp_version . ' ' . 'TeamstuffCalendar/' . $this->version,
				'timeout' 		=> 15,
		    'headers'     => array(
					'Accept' => 'application/vnd.teamstuff.com.v10+json, application/json'
				)
		);

		// perform the request
		$resp = wp_remote_get($url, $args);
		$status = 200;

		// handle failure cases and success case
		if ( is_wp_error( $resp ) ) {
		  $error_message = $resp->get_error_message();
			$resp = array('error' => $error_message);
			$status = 500;
		} else {
			$status = $resp['response']['code'];
			if( $status != 200) {
				$resp = array('error' => $resp['response']['message'], 'version' => $this->version);
			} else {
				$resp = json_decode($resp['body']);
			}
		}

		// correctly wrap the response and set status code
		$resp = rest_ensure_response($resp);
		$resp->set_status($status);
		return $resp;
	}

	/**
	 * Renders the teamstuff calendar widget from its template
	 *
	 * @return	HTML render of the Teamstuff calendar
	 * @since    1.0.0
	 */
	public function render_calendar_widget($params) {

		$teamName = is_array($params) ? $params['team'] : "";

		ob_start();
		if( $teamName != "") {
			$fromDate = is_array($params) ? $params['from'] : "";
			$toDate = is_array($params) ? $params['to'] : "";
			include('partials/teamstuff-calendar-widget-team.php');
		}
		else {
			include('partials/teamstuff-calendar-widget.php');
		}
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

}
