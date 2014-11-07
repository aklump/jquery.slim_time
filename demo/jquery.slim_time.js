/**
 * Slim Time jQuery JavaScript Plugin v1.1
 * http://www.intheloftstudios.com/packages/jquery/jquery.slim_time
 *
 * A minimal jquery time widget for textfields.
 *
 * Copyright 2013, Aaron Klump
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: Fri Nov  7 09:38:13 PST 2014
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
  var parts;
  if (this.options.fuzzy) {
    parts = string.match(/(\d{1,2})\:?(\d{2})?(am|pm)?/);
  }
  else {
    parts = string.match(/^(\d{1,2})\:?(\d{2})?(am|pm)?$/);
  }

  if (!parts || typeof parts[1] === 'undefined') {
    return false;
  }

  if (typeof parts[2] === 'undefined') {
    parts[2] = 0;
  }

  var hour = parts[1] * 1;
  var min = parts[2] * 1;
  var suffix = this.options.assume;

  if (typeof parts[3] !== 'undefined') {
    suffix = parts[3];
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
    if (hour === 12 && suffix === 'am') {
      hour = 0;
    }
    else if (hour < 12 && suffix === 'pm') {
      hour += 12;
    }
    suffix = '';
  }

  if (min === 0 || typeof min === 'undefined') {
    min = '00';
  }

  if (hour > this.options.default || min > 59) {
    return false;
  }  

  return [hour, min, suffix];
};

/**
 * Joines a parsed time array into a string
 *
 * @param  {array} parsed
 *
 * @return {string}        
 *
 * @see  SlimTime.prototype.parse().
 */
SlimTime.prototype.join = function (parsed) {
  return parsed ? parsed[0] + ':' + parsed[1] + parsed[2] : '';
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
  "assume"    : 'am',

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
  "cssPrefix"         : 'slim-time-'  
};

$.fn.slimTime.version = function() { return '1.1'; };

})(jQuery, window, document);