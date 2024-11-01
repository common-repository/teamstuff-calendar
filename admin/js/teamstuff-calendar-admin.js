(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $( window ).ready(function() {

		 // retrieve the input element
		 var ts_enable = $('#teamstuff-calendar_enable');
		 var ts_apikey = $('#teamstuff-calendar_apikey');
		 var ts_hidescores = $('#teamstuff-calendar_hidescores');

		 // callback that enables/disables the fields based on the enable setting
		 var update_enable = function() {
			 var disabled = ts_enable.attr('checked') == 'checked' ? false : true;
			 ts_apikey.prop('disabled', disabled);
			 ts_hidescores.prop('disabled', disabled);
		 };

		 ts_enable.click(update_enable); // bind click handler of checkbox to update enable
		 update_enable(); // update states initially
	 });

})( jQuery );
