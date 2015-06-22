/**
 * Slim Time jQuery JavaScript Plugin v1.4
 * http://www.intheloftstudios.com/packages/jquery/jquery.slim_time
 *
 * A minimal jquery time widget for textfields with server-side support.
 *
 * Copyright 2013, Aaron Klump
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: Mon Jun 22 15:27:34 PDT 2015
 *
 * @license
 */
;(function($, window, document, undefined) {
"use strict";

function SlimTime(element, options) {
  this.element = element;
  this.options = $.extend( {}, $.fn.slimTime.defaults, options) ;
  this.options.default *= 1;
  this.init();
}

/**
 * Validate an element fireing callbacks as needed and returning result.
 *
 * @param  {jQuery} $element
 *
 * @return {bool}
 */
SlimTime.prototype.validate = function ($element) {
  var valid = true;
  var value = $element.val();
  var parsed;

  if (value) {
    if (!(parsed = this.parse(value))) {
      valid = false;
    }
  }
  else {
    // An empty value is true unless required.
    valid = !this.options.required;
  }

  if (valid && typeof this.options.pass === 'function') {
    this.options.pass(value, parsed, $element, this);
  }

  if (!valid && typeof this.options.fail === 'function') {
    this.options.fail(value, parsed, $element, this);
  }

  return valid;
};

/**
 * Tries to parse a time string into hour/min/suffix.
 *
 * @param  {string} string
 *
 * @return {array||false}
 *   - 0 int Hour
 *   - 1 int Minute
 *   - 2 string Suffix either am or pm.
 */
SlimTime.prototype.parse = function (string) {
  var parts, hour, min, sec, suffix, suffixPresent, parsed, temp;

  var regex = '(?:([0,1,2]?\\d+)\\:?(\\d{2})\\:?(\\d{2})|([0,1,2]?\\d+)\\:?(\\d{2})()|([0,1,2]?\\d+)()())(am|pm|a|p)?';

  if (this.options.colon === 'required') {
    regex = regex.replace(/\\\:\?/g, '\\:');
  }

  if (!this.options.fuzzy) {
    regex = '^' + regex + '$';
  }

  // If we can't match then we're done.
  var match = new RegExp(regex);
  if (!(parts = match.exec(string))) {
    return false;
  }

  // If colon is required, make sure that we capture the same thing when
  // no colons are present in the regex, otherwise the first capture
  // did not acuratly capture the whole string correctly.
  // This makes sure that 615 doesn't get mistaken as 6 and loose the mins.
  if (this.options.colon === 'required' && parts[0].indexOf(':') === -1) {
    var colonRegex = new RegExp(regex.replace(/\\\:/g, ''));
    temp = colonRegex.exec(string);

    if (temp[0] !== parts[0]) {
      return false;
    }
  }  

  // If we can't figure out the hour then we need to bail.
  if (typeof (hour = parts[1]) === 'undefined' && typeof (hour = parts[4]) === 'undefined' && typeof (hour = parts[7]) === 'undefined') {
    return false;
  }
  hour *= 1;

  // Define the secs or 0.
  if (typeof (min = parts[2]) === 'undefined' && typeof (min = parts[5]) === 'undefined' && typeof (min = parts[8]) === 'undefined') {
    min = 0;
  }

  // Define the secs or 0.
  if (typeof (sec = parts[3]) === 'undefined'  && typeof (sec = parts[6]) === 'undefined'  && typeof (sec = parts[9]) === 'undefined') {
    sec = 0;
  }

  // Figure out the suffix if discovered.
  suffix = this.options.assume;
  suffixPresent = false;
  if (typeof parts[10] !== 'undefined') {
    suffixPresent = true;
    suffix = parts[10];
    if (suffix === 'a' || suffix === 'p') {
      suffix += 'm';
    }      
  }

  if (hour > 23 || min > 59) {
    return false;
  }  

  // am/pm
  if (this.options.default === 12) {
    if (hour > 12) {
      hour -= 12;
      suffix = 'pm';
    }
    else if (hour === 0) {
      hour = 12;
      suffix = 'am';
    }
  }

  // Military
  if (this.options.default === 24) {
    if (hour === 12 && suffix === 'am' && suffixPresent) {
      hour = 0;
    }
    else if (hour < 12 && suffix === 'pm') {
      hour += 12;
    }
    suffix = '';

    if (hour < 10) {
      hour = '0' + hour;
    }    
  }

  hour = hour.toString();
  min  = min === 0 ? '00' : (min < 10 ? min = '0' + (min * 1) : min.toString());
  sec  = sec === 0 ? '00' : (sec < 10 ? sec = '0' + (sec * 1) : sec.toString());

  parsed = [hour, min, suffix];
  if (this.options.seconds) {
    parsed.push(sec);
  }

  return parsed;
};

/**
 * Joins a parsed time array into a string.
 *
 * @param  {array} parsed
 *
 * @return {string}        
 *
 * @see  SlimTime.prototype.parse().
 */
SlimTime.prototype.join = function (parsed) {
  var colon = this.options.colon === 'none' ? '' : ':';
  var c;
  if (typeof parsed === 'undefined' || !(c = parsed.length) || c < 3 || c > 4) {
    return '';
  }

  var joined = parsed.slice(0,2);
  if (c === 4) {
    joined.push(parsed[3]);
  }

  return joined.join(colon) + parsed[2];


  // var colon = this.options.colon === 'none' ? '' : ':';
  // return parsed && parsed.length === 3 ? parsed[0] + colon + parsed[1] + parsed[2] : '';
};

SlimTime.prototype.init = function () {
  var st = this;
  $(this.element)
  .addClass(this.options.cssPrefix + 'processed')
  .blur(function () {
    st.validate($(this));
  });

  if (this.options.required) {
    $(this.element).addClass(this.options.cssPrefix + 'required');
  }
};

$.fn.slimTime = function(options) {
  return this.each(function () {
    $.data(this, 'plugin_slimTime', new SlimTime(this, options));
  });
};

$.fn.slimTime.defaults = {

  // Should the time from a string like 'the time is 12:05 today.' be allowed?
  // Set this to false to deny the above and require that the exact "12:05" be
  // entered.  Think of this as extract time or not?
  "fuzzy"     : true,

  // Is a value entered into this field required for validation?
  "required"  : false,

  // Set to 12 or 24.  When 12 military time will be converted to 12 hour time
  // appending the correct suffix, am or pm.
  "default"   : 12,

  // When suffix is missing and time is < 13 assume the following.
  "assume"    : "am",

  // Should we support seconds; set to false and seconds will be ignored.
  "seconds"   : false,

  // Define the colon handler for output: optional, none, required.  When colon
  // is required, 6:15 is valid input but not 615, when it is optional 615 and
  // 6:15 are valid and the output includes colon, when it is none, 615 and 6:15
  // are valid input and the output does not include the colon.
  
  // | option value | valid input | output format |
  // |--------------|-------------|---------------|
  // | none         | 6:15 or 615 | 615am         |
  // | optional     | 6:15 or 615 | 6:15am        |
  // | required     | 6:15        | 6:15am        |
  "colon"     : "optional",

  // Callback for when the time passes validation.
  "pass"      : function(entered, parsed, $element, slimTime) {
    $element
    .val(slimTime.join(parsed))
    .removeClass(slimTime.options.cssPrefix + 'error');
  },

  // Callback for when the time fails validation.
  "fail"      : function(entered, parsed, $element, slimTime) {
    $element.addClass(slimTime.options.cssPrefix + 'error');
  },
  
  // A prefix for all css classes
  "cssPrefix"         : "slim-time-"
};

$.fn.slimTime.version = function() { return '1.4'; };

})(jQuery, window, document);