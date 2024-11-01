<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.teamstuff.com
 * @since      1.0.0
 *
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/admin
 * @author     Tom Wright <tom.wright@shineproducts.com.au>
 */
class Teamstuff_Calendar_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/teamstuff-calendar-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/teamstuff-calendar-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Teamstuff Calender Settings', $this->plugin_name ),
			__( 'Teamstuff Calendar', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'render_options_page' )
		);

	}

	/**
	 * Renders the HTML for the plugin options page from a partial
	 *
	 * @since    1.0.0
	 */
	public function render_options_page() {
		include_once 'partials/teamstuff-calendar-admin-display.php';
	}

	/**
	 * Register the settings page for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		// create the settings section
		add_settings_section(
			$this->plugin_name . '_general',
			__( 'General', $this->plugin_name ),
			array( $this, 'render_settings_info'),
			$this->plugin_name
		);

		// add the opt-in field
		add_settings_field(
			$this->option_name . '_enable',
			__( 'Enable Teamstuff Calendar', $this->plugin_name ),
			array( $this, 'render_enable_field' ),
			$this->plugin_name,
			$this->plugin_name . '_general'
		);

		// add the API key setting field
		add_settings_field(
			$this->option_name . '_apikey',
			__( 'Teamstuff Club Token', $this->plugin_name ),
			array( $this, 'render_apikey_field' ),
			$this->plugin_name,
			$this->plugin_name . '_general'
		);

		// add the display scores setting field
		add_settings_field(
			$this->option_name . '_hidescores',
			__( 'Hide scores for past games', $this->plugin_name ),
			array( $this, 'render_hidescores_field' ),
			$this->plugin_name,
			$this->plugin_name . '_general'
		);

		// register the API key and enable settings
		register_setting( $this->plugin_name, $this->plugin_name . '_apikey', array( $this, 'sanitise_apikey' ) );
		register_setting( $this->plugin_name, $this->plugin_name . '_enable', array( $this, 'sanitise_checkbox' ) );
		register_setting( $this->plugin_name, $this->plugin_name . '_hidescores', array( $this, 'sanitise_checkbox' ) );
	}

	/**
	 * Render an admin notice about an unsupported version. Rendering is conditional on a transient value that is set during activation, or being on the settings page for Teamstuff Calendar
	 *
	 * @since    1.0.6
	 */
	public function render_rest_support_notice() {
		$screen = get_current_screen();

		// TODO: clean up this conditional -- detecting the settings page by the id feels brittle
		if(!function_exists('register_rest_route') &&
			(get_transient($this->plugin_name . '_rest_transient') || $screen->id == 'settings_page_' . $this->plugin_name)) {
			echo '<div class="error notice"><p>' . __('Required support for REST APIs not found on your WordPress installation. Teamstuff Calendar will not operate correctly. Please update WordPress to at least v4.5.0 or install the \'WP REST API v2\' plugin.', $this->plugin_name) . '</p></div>';
		}
	}

	/**
	 * Renders some HTML-formatted info that resides under the title of the settings section.
	 *
	 * @since    1.0.0
	 */
	public function render_settings_info() {
		echo '<p>' . __('Please adjust the settings accordingly.', $this->plugin_name) . '</p>';
	}

	/**
	 * Renders the API key setting field
	 *
	 * @since    1.0.0
	 */
	public function render_apikey_field() {
		$api_key = get_option( $this->plugin_name . '_apikey' );
		echo '<input type="text" name="' . $this->plugin_name . '_apikey' . '" id="' . $this->plugin_name . '_apikey' . '" value="' . $api_key . '" ' . disabled('', $enable, false) .' />';
	}

	/**
	 * Renders the shortcode enable setting field
	 *
	 * @since    1.0.0
	 */
	public function render_enable_field() {
		$enable = get_option( $this->plugin_name . '_enable' );
		echo '<input type="checkbox" name="' . $this->plugin_name . '_enable' . '" id="' . $this->plugin_name . '_enable' . '" ' . checked( 'on', $enable, false ) . ' /> <span>' . __('Note that by enabling the Teamstuff Calendar here you opt in to any associated Teamstuff branding of the widget.', $this->plugin_name) . '</span>';
	}

	/**
	 * Renders the display scores setting field
	 *
	 * @since    1.0.2
	 */
	public function render_hidescores_field() {
		$display_scores = get_option( $this->plugin_name . '_hidescores' );
		echo '<input type="checkbox" name="' . $this->plugin_name . '_hidescores' . '" id="' . $this->plugin_name . '_hidescores' . '" ' . checked( 'on', $display_scores, false ) . ' />';
	}

	/**
	 * Strips out any non-alphanumeric characters
	 *
	 * @param api_key string The raw API key string as entered by the user
	 * @return The cleaned, safe API key to be persisted in the settings
	 * @since    1.0.0
	 */
	public function sanitise_apikey($api_key) {
		return preg_replace("/[^a-zA-Z0-9_\-]+/", "", $api_key);
	}

	/**
	 * Ensures a checkbox setting is 'on' or ''
	 *
	 * @param enable string The raw checkbox setting generated by the form
	 * @return The cleaned, safe checkbox field to be saved in the settings
	 * @since    1.0.2
	 */
	public function sanitise_checkbox($enable) {
		return $enable == "on" ? "on" : "";
	}
}
