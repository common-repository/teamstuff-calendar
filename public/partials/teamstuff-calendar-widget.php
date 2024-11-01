<?php

/**
 * Provide the client-side view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.teamstuff.com
 * @since      1.0.0
 *
 * @package    Teamstuff_Calendar
 * @subpackage Teamstuff_Calendar/public/partials
 */
?>

<div id="ts-calendar-widget" class="ts-calendar-widget">
  <input id="ts-calendar-locale" type="hidden" value="<?php echo get_locale(); ?>">
  <input id="ts-calendar-rest-url" type="hidden" value="<?php echo function_exists('rest_url') ? rest_url($this->plugin_name) : ''; ?>">
  <div class="text-center" style="padding:10px;">
    <a href="http://www.teamstuff.com"><img alt="<?php echo __( 'Powered by Teamstuff', $this->plugin_name ) ?>" src="<?php echo plugins_url('../img/TS_poweredby.png', __FILE__ ); ?>"></a>
  </div>
  <!-- header -->
  <div id="ts-calendar-header" class="panel panel-default">
    <div class="panel-body">
      <div class="container-fluid">
        <div class="row text-center">
          <div class="col-xs-1 ts-calendar-header-button" ><a id="ts-calendar-date-prev" href="#"><img alt="<?php echo __( 'Previous Day', $this->plugin_name ) ?>" src="<?php echo plugins_url('../img/arrow_back.png', __FILE__ ); ?>"></a></div>
          <div class="col-xs-10"><span id="ts-calendar-date"></span> <span id="ts-calendar-year"></span></div>
          <div class="col-xs-1 ts-calendar-header-button"><a id="ts-calendar-date-next" href="#"><img alt="<?php echo __( 'Next Day', $this->plugin_name ) ?>" src="<?php echo plugins_url('../img/arrow_forward.png', __FILE__ ); ?>"></a></div>
        </div>
      </div>
    </div>
  </div>

  <!-- container for the event rows -->
  <div id="ts-calendar-event-rows">
  </div>

  <!-- loading row -->
  <div id="ts-calendar-loading-row" class="panel panel-default">
    <div class="panel-body text-center">
      <?php echo __( 'Loading', $this->plugin_name ) ?>...
    </div>
  </div>

  <!-- no games row -->
  <div id="ts-calendar-empty-row" class="panel hidden panel-default">
    <div class="panel-body text-center">
      <?php echo __( 'No games scheduled today.', $this->plugin_name ) ?>
    </div>
  </div>

  <!-- error row -->
  <div id="ts-calendar-error-row" class="panel hidden panel-danger text-danger">
    <div class="panel-body text-center">
      <strong><?php echo __( 'Oops!', $this->plugin_name ) ?></strong> <?php echo __( 'We failed to fetch the calendar from Teamstuff. Please try again or contact the site administrator.', $this->plugin_name ) ?>
    </div>
  </div>

  <div class="text-center" style="padding:10px;">
    <a href="http://www.teamstuff.com"><img alt="<?php echo __( 'Powered by Teamstuff', $this->plugin_name ) ?>" src="<?php echo plugins_url('../img/TS_poweredby.png', __FILE__ ); ?>"></a>
  </div>

  <!-- event row template -->
  <div class="ts-calendar-row panel panel-default hidden">
    <div class="container-fluid">
      <div class="row">
        <!-- time -->
        <div class="ts-calendar-col ts-calendar-col-time ts-calendar-col-time-xl text-center"><div class="ts-calendar-col-content">
          <span id="ts-calendar-time-now" class="hidden"><?php echo __( 'NOW', $this->plugin_name ) ?></span><span id="ts-calendar-time"></span><small><span id="ts-calendar-time-suffix"></span></small>
          <div class="ts-calendar-time-user-container hidden">
            <span id="ts-calendar-date-user"></span>
            <span id="ts-calendar-time-user"></span><small><span id="ts-calendar-time-suffix-user"></span></small><br/>
            <small><?php echo __( 'MY TIME', $this->plugin_name ) ?></small>
          </div>
        </div></div>

        <div class="container-fluid ts-calendar-inner-row">
          <div class="row">
            <!-- teams -->
            <div class="col-xs-5 ts-calendar-col ts-calendar-col-teams text-left">
              <span id="ts-calendar-team-us">[teamUs]</span> vs <span id="ts-calendar-team-them">[teamThem]</span>
            </div>
            <!-- location -->
            <div class="col-xs-4 ts-calendar-col ts-calendar-col-location text-left">
              <div id="ts-calendar-homeaway" class="ts-calendar-homeaway"></div>
              <div style="display: inline-block;">
                <span id="ts-calendar-location">[location]</span><br/>
                <a id="ts-calendar-maplink" href="#">map</a>
              </div>
            </div>
            <!-- score -->
            <div class="col-xs-3 ts-calendar-col ts-calendar-col-score text-right">
              <img id="ts-calendar-score-win" class="<?php echo (get_option( $this->plugin_name . '_hidescores' ) == 'on' ? 'pull-right' : 'pull-left') ?> hidden" alt="Victory" src="<?php echo plugins_url('../img/icn_win.png', __FILE__ ); ?>">
              <?php if(get_option( $this->plugin_name . '_hidescores' ) != 'on') { ?>
                <div class="ts-calendar-col-content">
                  <span id="ts-calendar-score-us">[scoreUs]</span> : <span id="ts-calendar-score-them">[scoreThem]</span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
