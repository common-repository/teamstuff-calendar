(function( $, globals ) {
	'use strict';

  function TeamstuffCalendar(widget) {
  	this.widget = widget;
  	this.template = widget.children('.ts-calendar-row.hidden').clone().removeClass('hidden'); // create a visible copy of the row template for use later
  	this.eventRows = widget.children('#ts-calendar-event-rows');
  	this.locale = widget.children('#ts-calendar-locale').val();
		this.url = widget.children('#ts-calendar-rest-url').val();
  	this.teamName = widget.find("#ts-calendar-team-name").text();
  	this.cache = {};
  	this.cacheRefreshMs = 1000*60; // data is valid for 1 minute
  }

  TeamstuffCalendar.getEventState = function(startTime, nowTime, durationMs) {
  	var state = "future";

		var startTimeMs = startTime.valueOf();
		var nowTimeMs = nowTime.valueOf();
		var endTimeMs = startTimeMs + durationMs;
  	if(nowTimeMs >= startTimeMs) {
  	 if(nowTimeMs > endTimeMs) {
  		 state = "past";
  	 } else {
  		 state = "current";
  	 }
  	}
  	return state;
  };

  TeamstuffCalendar.createRow = function(template, rowData, locale, now) {
  	var row = template.clone();

  	if(rowData.bye) {
  		// Add styling for a bye
  		row.addClass('ts-calendar-row-bye');

  		// Remove relevant contents
  		row.find("#ts-calendar-time").parent().html('BYE');
  		row.find("#ts-calendar-team-us").parent().html(rowData.team.name);
  		row.find("#ts-calendar-location").parent().html('');
  		row.find("#ts-calendar-score-win").parent().html('');
  	}
  	else {

			// get times into UTC
	  	var time = moment(rowData.whistle_time).utc();
			var now = moment(now).utc();

  		var eventState = TeamstuffCalendar.getEventState(time, now, rowData.duration_in_seconds * 1000);
			var showTime = true;
  		switch(eventState) {
  			case "future":
  				row.addClass('ts-calendar-row-future');
  				break;
  			case "current":
					row.addClass('ts-calendar-row-current');
					row.find("#ts-calendar-time-now").removeClass('hidden');
					// remove the elements related to time
					row.find("#ts-calendar-time").remove();
					row.find("#ts-calendar-time-suffix").remove();
					row.find("#ts-calendar-time-user").remove();
					row.find("#ts-calendar-time-suffix-user").remove();
  				break;
  			case "past":
  				row.addClass('ts-calendar-row-past');
  				break;
  		}

			// game time in the timezone it's being played in (so display as UTC)
			var timeStringSplit = time.format('LT').split(' ');
			row.find("#ts-calendar-time").text(timeStringSplit[0]);
			if(timeStringSplit.length > 1) {
				row.find('#ts-calendar-time-suffix').text(timeStringSplit[1]);
			}
			row.find("#ts-calendar-date").text(time.format('D MMM	YYYY'));

			// game time in the viewer's timezone -- if it's different
			var userOffset = moment().utcOffset();
			var gameOffset = rowData.timezone_offset / 60;
			if(gameOffset != userOffset && eventState == "future") {
				row.find('.ts-calendar-time-user-container').removeClass('hidden');

				var effectiveOffset = userOffset - gameOffset;
				var userTime = time.clone().utcOffset(effectiveOffset);

				timeStringSplit = userTime.format('LT').split(' ');
				row.find("#ts-calendar-time-user").text(timeStringSplit[0]);
  			if(timeStringSplit.length > 1) {
  				row.find('#ts-calendar-time-suffix-user').text(timeStringSplit[1]);
  			}

				// display the date as well if it is different
				if(userTime.day() != time.day()) {
					row.find("#ts-calendar-date-user").text(userTime.format('D MMM	YYYY'));
				}
			}

  		// apply teams
  		row.find("#ts-calendar-team-us").text(rowData.team.name);
  		row.find("#ts-calendar-team-them").text(rowData.opposition);

  		// apply location and home/away
  		var locationText = rowData.location_name;
  		if(locationText == null || locationText.length < 1) locationText = rowData.address;

  		row.find("#ts-calendar-location").text(locationText);
  		if(rowData.away_game) {
  			row.find('#ts-calendar-homeaway').addClass('ts-calendar-away').text('A');
  		} else {
  			row.find('#ts-calendar-homeaway').addClass('ts-calendar-home').text('H');
  		}

  		// apply map link
  		var mapLink = encodeURIComponent(rowData.address) + "/@" + encodeURIComponent(rowData.location_data);
  		row.find('#ts-calendar-maplink').click(function(e) {
				window.open("https://www.google.com.au/maps/place/" + mapLink);
				e.preventDefault();
			});

  		// apply scores
  		if(row.find('#ts-calendar-score-us').length > 0) { // if displaying scores is enabled in the plugin
  			if(rowData.our_score === null || rowData.their_score === null) {
  				row.find("#ts-calendar-score-us").parent().html(""); // clear the score column (to remove the colon)
  			} else {
  				row.find("#ts-calendar-score-us").text(rowData.our_score);
  				row.find("#ts-calendar-score-them").text(rowData.their_score);
  			}
  		}

  		// apply win indicator if relevant
  		if(rowData.result == 'win') {
  			row.find('#ts-calendar-score-win').removeClass('hidden');
  		}
  	}

  	return row;
  };

  TeamstuffCalendar.getCacheKey = function(date, direction) {
  	return moment(date).utc().format('DD/MM/YY') + direction;
  };

  TeamstuffCalendar.prototype.getTeamGames = function(startDate, endDate, teamName) {
  	// perform request to load the data
  	var params = { startDate: '0000-01-01' }; // workaround because endpoint without either parameters only gives future games
  	if(startDate) {
      startDate = moment(startDate);
  		params['startDate'] = startDate.format('YYYY-MM-DD');
  	}
  	if(endDate) {
      endDate = moment(endDate);
  		params['endDate'] = endDate.format('YYYY-MM-DD');
  	}
  	var req = $.getJSON(this.url + '/events-range', params)
  		.done(function(data) {
  			// filter the games by the team
  			data.games = data.games.filter(function(game) {
  				return game.team.name == teamName;
  			});

  			// sort the games
  			data.games.sort(function(a, b) {
  				if(a.whistle_time == "") { return 1; }
  				if(b.whistle_time == "") { return -1; }
  				return Date.parse(a.whistle_time) - Date.parse(b.whistle_time);
  			});
  		});

  	return req;
  };

  TeamstuffCalendar.prototype.getDateGames = function(date, direction) {
  	// check the cache
  	var cacheKey = TeamstuffCalendar.getCacheKey(date, direction);
  	var cacheEntry = this.cache[cacheKey];
  	var promise;
  	if(cacheEntry && cacheEntry.retrieved + this.cacheRefreshMs > Date.now()) {
  		// wrap cache data in a promise
  		var deferred = $.Deferred()
  		deferred.resolve(cacheEntry.data);
  		promise = deferred.promise();
  	} else {
  		var self = this;
  		// trigger request to retrieve data from API
			var params = { date: moment(date).format('YYYY-MM-DD') };
			if(direction) {
				params.direction = direction;
			}
  		promise = jQuery.getJSON(this.url + '/events', params)
  			.done(function(data) {
  				// sort the games
  				data.games.sort(function(a, b) {
  					if(a.whistle_time == "") { return 1; }
  					if(b.whistle_time == "") { return -1; }
  					return Date.parse(a.whistle_time) - Date.parse(b.whistle_time);
  				});

  				// update the cache
  				self.cache[cacheKey] = { retrieved: Date.now(), data: data };
  			});
  	}

  	return promise;
  };

  TeamstuffCalendar.prototype.loadTeam = function(startDate, endDate) {
  	// based on startDate and endDate, make one of the date range labels visible
  	var dateRange;
  	if(startDate && endDate) {
  		dateRange = this.widget.find("#ts-calendar-date-range");
  	} else if(startDate) {
  		dateRange = this.widget.find("#ts-calendar-date-range-from");
  	} else if(endDate) {
  		dateRange = this.widget.find("#ts-calendar-date-range-to");
  	} else {
  		dateRange = this.widget.find("#ts-calendar-date-range-all");
  	}
  	dateRange.removeClass('hidden');

  	// set the date range indicators
  	if(startDate) {
  		startDate = moment(startDate).utc().locale(this.locale);
  		dateRange.find("#ts-calendar-date-from").text(startDate.format('D MMM YYYY'));
  	}
  	if(endDate) {
  		endDate = moment(endDate).utc().locale(this.locale);
  		dateRange.find("#ts-calendar-date-to").text(endDate.format('D MMM YYYY'));
  	}

  	// perform request to load the data
  	var self = this;
  	var req = this.getTeamGames(startDate, endDate, this.teamName)
  		.done(function(data) {
  			// hide the loading row
  			self.widget.children('#ts-calendar-loading-row').addClass('hidden');

  			// clear the old rows
  			self.eventRows.empty();
  			self.eventRows.removeClass('hidden');

  			// update views with event data or display empty row
  			if(data.games.length == 0) {
  				self.widget.children('#ts-calendar-empty-row').removeClass('hidden');
  			} else {
  				self.populateRows(data);
  			}
  		})
  		.fail(function(err) {
  			self.widget.children('#ts-calendar-error-row').removeClass('hidden');
  		})
  		.always(function() {
  			self.widget.children('#ts-calendar-loading-row').addClass('hidden');
  		});
  }

  TeamstuffCalendar.prototype.preloadDate = function(date) {
  	// create container for preload requests if need be
  	this.requestPreload = this.requestPreload || {};

  	var directions = [ 'next', 'previous'];
  	var self = this;
  	$.each(directions, function(i, direction) {
  		// if we have a current preloading request for this direction, abort it
  		if(self.requestPreload[direction] && self.requestPreload[direction].abort)
  			self.requestPreload[direction].abort();

  		self.requestPreload[direction] = self.getDateGames(date, direction)
  			.always(function() {
  				self.requestPreload[direction] = null;
  			})
  			.done(function(data) {
  				// if we preload but get no games (no more events in that direction)
  				if(data.games.length == 0) {
  					// disable the appropriate arrow
  					if(direction == 'previous')
  						self.widget.find('#ts-calendar-date-prev').addClass('hidden');
  					else if(direction == 'next')
  						self.widget.find('#ts-calendar-date-next').addClass('hidden');
  				}
  			});
  	});
  };

  TeamstuffCalendar.prototype.changeDate = function(currDate, direction) {
  	// if a request exists (that is actually a request -- has .abort), abort it
  	if(this.request && this.request.abort)
  		this.request.abort();

  	// hide the old events
  	this.eventRows.addClass('hidden');

  	// hide the error row
  	this.widget.children('#ts-calendar-error-row').addClass('hidden');
  	// hide the empty row
  	this.widget.children('#ts-calendar-empty-row').addClass('hidden');

  	// display the loading row and the direction buttons
  	this.widget.children('#ts-calendar-loading-row').removeClass('hidden');
  	this.widget.find('#ts-calendar-date-prev').removeClass('hidden');
  	this.widget.find('#ts-calendar-date-next').removeClass('hidden');

  	var self = this;
  	this.request = this.getDateGames(currDate, direction)
  		.fail(function(err) {
  			// display the error row
  			self.widget.children('#ts-calendar-error-row').removeClass('hidden');
  		})
  		.always(function() {
  			self.widget.children('#ts-calendar-loading-row').addClass('hidden');
  			self.request = null;
  		})
  		.done(function(data) {

  			if(!data || !data.games) {
  				// display the error row
  				self.widget.children('#ts-calendar-error-row').removeClass('hidden');
  			} else {

  				// make sure the navigation arrows are visible
  				self.widget.find('#ts-calendar-date-prev').removeClass('hidden');
  				self.widget.find('#ts-calendar-date-next').removeClass('hidden');

  				// extract the date from the data (data used by the calendar is UTC)
  				var newDate = moment(data.current_date).utc().locale(self.locale);
  				self.date = newDate;

  				// trigger fetching of previous/next from this new date
  				self.preloadDate(newDate);

  				// update the header date and year
  				self.widget.find('#ts-calendar-date').text(newDate.format('dddd D MMMM'));
  				self.widget.find('#ts-calendar-year').text(newDate.format('YYYY'));

  				// if we tried to change date but got no more games (no more events)
  				if(direction != null && data.games.length == 0) {
  					// disable the appropriate arrow
  					if(direction == 'previous')
  						self.widget.find('#ts-calendar-date-prev').addClass('hidden');
  					else if(direction == 'next')
  						self.widget.find('#ts-calendar-date-next').addClass('hidden');

  					// re-display the existing events for this date
  					self.eventRows.removeClass('hidden');
  				} else {
  					// clear the old rows
  					self.eventRows.empty();
  					self.eventRows.removeClass('hidden');

  					// update views with event data or display empty row
  					if(data.games.length == 0) {
  						self.widget.children('#ts-calendar-empty-row').removeClass('hidden');
  					} else {
  						self.populateRows(data);
  					}
  				}
  			}
  		});
  };

  TeamstuffCalendar.prevDate = function(e) {
  	this.changeDate(this.date, 'previous');
		e.preventDefault();
  };

  TeamstuffCalendar.nextDate = function(e) {
  	this.changeDate(this.date, 'next');
		e.preventDefault();
  };

  TeamstuffCalendar.prototype.populateRows = function(events) {
  	var self = this;
  	var now = Date.now()
  	$.each(events.games, function() {
  		var newRow = TeamstuffCalendar.createRow(self.template, this, self.locale, now);
  		self.eventRows.append(newRow);
  	});
  };

  TeamstuffCalendar.initialise = function(widget) {
  	var ts = new TeamstuffCalendar(widget);

  	if(ts.teamName != "") {
  		var fromDate = moment(widget.find("#ts-calendar-param-date-from").val());
  		var toDate = moment(widget.find("#ts-calendar-param-date-to").val());
  		ts.loadTeam(
  			fromDate.isValid() ? fromDate : null,
  			toDate.isValid() ? toDate : null
  		);
  	} else {
  		// attach click handlers to prev/next
  		widget.find('#ts-calendar-date-prev').click(TeamstuffCalendar.prevDate.bind(ts));
  		widget.find('#ts-calendar-date-next').click(TeamstuffCalendar.nextDate.bind(ts));

  		// initialise calendar with the current date
  		var now = new Date();
  		ts.changeDate(now);
  	}
  };

  globals.TeamstuffCalendar = TeamstuffCalendar;
})( jQuery, function() {return this;}.call() );
