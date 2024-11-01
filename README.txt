=== Teamstuff Calendar ===
Contributors: teamstuff
Tags: teamstuff, calendar, sports, fixture
Requires at least: 4.4.0
Tested up to: 4.5.3
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides a widget for your WordPress site that will display the fixtures and scores for your Teamstuff Club!

== Description ==

Are you a club that uses Teamstuff, and uses WordPress? If so, this plugin saves you the hassle of manually
keeping your fixtures and scores up-to-date, by providing a slick widget that you can easily embed into any page you like.

Note that this plugin is only usable by Teamstuff users, and that by installing this plugin you give it permission to retrieve calendar information from Teamstuff.

== Installation ==

To install and enable the Teamstuff Club Calendar, follow the steps below.

= Prepare your Teamstuff Calendar Token =
1. Login to your club on Teamstuff Clubs
2. Under the 'General Settings' tab (look for the gear icon), click 'Edit'
3. Tick 'Enable Wordpress Plugin'
4. Click 'Save'
5. Copy your Teamstuff Calendar Club Token for the WordPress plugin for use in the next steps

= Install and configure the WordPress Plugin =
1. Ensure your WordPress installation is at least version 4.4.0
2. Locate the Teamstuff Calendar plugin in the WordPress Plugin Directory and click 'Install Now'
3. Navigate to your Plugins page, and click 'Activate' below the Teamstuff Calendar plugin title
4. Navigate to the Settings -> Teamstuff Calendar page
5. Check the 'Enable Teamstuff Calendar' checkbox
6. Copy your Teamstuff Calendar Token into the 'Teamstuff Calendar Token' field
7. Click 'Save Settings'

= Add the Teamstuff Calendar to a page =
1. Start editing the page you want to insert the Teamstuff Calendar into
2. Insert the [teamstuff-calendar] shortcode where you want the Teamstuff Calendar to appear (ensure that the shortcode is on it's own line with whitespace above and below)
3. Save the page
4. The Teamstuff Calendar should will appear on the page you inserted it into.

= Add the Teamstuff Calendar to a page, to display fixtures for a single team =
1. Start editing the page you want to insert the Teamstuff Calendar into
2. Insert the [teamstuff-calendar team="your-team-name-here"] shortcode where you want the Teamstuff Calendar to appear. For example, if your team is called Wildcats U11 Boys, insert the following shortcode: [teamstuff-calendar team="Wildcats U11 Boys"]
3. The date range of the fixtures can be set using optional 'to' and 'from' parameters in the 'YYYY-MM-DD' format. For example, if your team is called Wildcats U11 Boys, and you want to display a fixture from 1st July 2016 to 31st December 2016, you would enter the following shortcode:
[teamstuff-calendar team="Wildcats U11 Boys" from="2016-07-31" to="2016-12-31"]

== Screenshots ==

1. Teamstuff Club Calendar, your fixtures and scores, directly on your website.
2. Teamstuff Club Calendar, your fixtures and scores, directly on your website.

== Changelog ==

= 1.1.0 =
* Bug fixes for improved support for timezones
* When games are viewed in a different timezone, start time in the user's timezone is displayed

= 1.0.7 =
* Increased timeout of requests to Teamstuff.com to 15 seconds

= 1.0.6 =
* Added support for non-pretty permalinks
* Added warning notice on plugin activation and plugin setting page when required REST support is not found

= 1.0.5 =
* Bug fix: widget links will no longer reset page scroll position

= 1.0.4 =
* Calendar can now be used to display full fixtures for a single team.

= 1.0.3 =
* Calendar now navigates to the next date that has games, skipping empty days.

= 1.0.2 =
* Added an option to hide scores on past games. If this option is checked, then the widget will not display the scores of past games (though the win/loss status is still indicated)

= 1.0.0 =
* First release. Please notify us of any bugs or issues you encounter.
