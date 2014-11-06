/**
 * Slim Time jQuery JavaScript Plugin v0.1.3
 * http://www.intheloftstudios.com/packages/jquery/jquery.slim_time
 *
 * A minimal jquery time widget for textfields.
 *
 * Copyright 2013, Aaron Klump
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: Thu Nov  6 14:34:40 PST 2014
 *
 * @license
 */
!function($,i,t,s){"use strict";function e(i,t){this.element=i,this.options=$.extend({},$.fn.slimTime.defaults,t),this.options.default*=1,this.init()}e.prototype.validate=function(i){var t=!0,s=i.val(),e;return s?(e=this.parse(s))||(t=!1):t=!this.options.required,t&&"function"==typeof this.options.pass&&this.options.pass(s,e,i,this),t||"function"!=typeof this.options.fail||this.options.fail(s,e,i,this),t},e.prototype.parse=function(i){var t;if(t=i.match(this.options.fuzzy?/(\d{1,2})\:(\d{2})(am|pm)?/:/^(\d{1,2})\:(\d{2})(am|pm)?$/),!t||"undefined"==typeof t[1]||"undefined"==typeof t[2])return!1;var s=1*t[1],e=1*t[2],n=this.options.assume;return"undefined"!=typeof t[3]&&(n=t[3]),12===this.options.default&&(s>12?(s-=12,n="pm"):0===s&&(s=12,n="am")),24===this.options.default&&(12===s&&"am"===n?s=0:12>s&&"pm"===n&&(s+=12),n=""),0===e&&(e="00"),[s,e,n]},e.prototype.join=function(i){return i?i[0]+":"+i[1]+i[2]:""},e.prototype.init=function(){var i=this;$(this.element).addClass(this.options.cssPrefix+"processed").blur(function(){i.validate($(this))}),this.options.required&&$(this.element).addClass(this.options.cssPrefix+"required")},$.fn.slimTime=function(i){return this.each(function(){$.data(this,"plugin_slimTime",new e(this,i))})},$.fn.slimTime.defaults={fuzzy:!0,required:!1,"default":12,assume:"am",pass:function(i,t,s,e){s.val(e.join(t)).removeClass(e.options.cssPrefix+"error")},fail:function(i,t,s,e){s.addClass(e.options.cssPrefix+"error")},cssPrefix:"slim-time-"},$.fn.slimTime.version=function(){return"0.1.3"}}(jQuery,window,document);