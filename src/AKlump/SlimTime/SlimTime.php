<?php
/**
 * @file
 * Defines a SlimTime object.
 *
 * This object is designed to mimick the js class as much as possible and is
 * provided for server side validation of the widget when needed.
 *
 * For server side validation you use something like the following:
 *
 * @code
 *   $obj = new SlimTime;
 *   if ($obj->validate($input)) {
 *     ...
 *   }
 * @endcode
 *
 * You can pass options as constructor arguments:
 *
 * @code
 *   $obj = new SlimTime(array('required' => TRUE));
 *   if ($obj->validate($input)) {
 *     ...
 *   }
 * @endcode
 *
 * To convert a rough value, use the following snippet:
 *
 * @code
 *   $obj = new SlimTime;
 *   $cleaned_up = $obj->parse($input)->join();
 * @endcode
 *
 * @ingroup slim_time
 * @{
 */
namespace AKlump\SlimTime;

/**
 * Represents a SlimTime object.
 */
class SlimTime {

  public $options;
  public $parsed      = array();
  protected $original = '';

  public function __construct($options = array()) {
    $options = (object) $options;
    $this->options = (array) $options + array(
      'fuzzy' => TRUE,
      'required' => FALSE,
      'default' => 12,
      'assume' => 'am',
      'seconds' => FALSE,
      'colon' => 'optional',
    );
    $this->options = (object) $this->options;
    
    // Option validation.
    foreach (array('fuzzy', 'required', 'seconds') as $option) {
      if (!is_bool($this->options->{$option})) {
        throw new \InvalidArgumentException("Option \"$option\" must be boolean.");
      }
    }
  }

  /**
   * A modified isset function to help filter empty non 0 values.
   *
   * @param  mixed $value
   *
   * @return bool
   */
  public static function undefined($value) {
    return empty($value) && !is_numeric($value);
  }

  /**
   * Parses time strings using the options.
   *
   * @param  string $string
   *
   * @return $this
   */
  public function parse($string) {
    $this->original = $string;
    $this->parsed   = array();
    
    // Build up our regex that is used to parse the incoming string.
    $regex = '(?:([0,1,2]?\d+)\:?(\d{2})\:?(\d{2})|([0,1,2]?\d+)\:?(\d{2})()|([0,1,2]?\d+)()())(am|pm|a|p)?';

    if ($this->options->colon === 'required') {
      $regex = str_replace('\:?', '\:', $regex);
    }

    if (!$this->options->fuzzy) {
      $regex = "^{$regex}$";
    }

    // If we can't match then we're done.
    if (!preg_match("/{$regex}/", $string, $parts)) {
      return $this;
    }
    $parts = array_pad($parts, 11, NULL);

    // If colon is required, make sure that we capture the same thing when
    // no colons are present in the regex, otherwise the first capture
    // did not acuratly capture the whole string correctly.
    // This makes sure that 615 doesn't get mistaken as 6 and loose the mins.
    if ($this->options->colon === 'required' && !strpos($parts[0], ':')) {
      $colonRegex = str_replace('\:', '', $regex);
      preg_match("/{$colonRegex}/", $string, $temp);
      if ($temp[0] !== $parts[0]) {
        return $this;
      }
    }
    
    // If we can't figure out the hour then we need to bail.
    if (self::undefined($hour = $parts[1])
      && self::undefined($hour = $parts[4])
      && self::undefined($hour = $parts[7])) {
      return $this;
    }
    $hour *= 1;

    // Define the secs or 0.
    if (self::undefined($min = $parts[2])
      && self::undefined($min = $parts[5])
      && self::undefined($min = $parts[8])) {
      $min = 0;
    }

    // Define the secs or 0.
    if (self::undefined($sec = $parts[3])
      && self::undefined($sec = $parts[6])
      && self::undefined($sec = $parts[9])) {
      $sec = 0;
    }

    // Figure out the suffix if discovered.
    $suffix = $this->options->assume;
    $suffixPresent = FALSE;
    if (isset($parts[10])) {
      $suffixPresent = TRUE;
      $suffix = $parts[10];
      if ($suffix === 'a' || $suffix === 'p') {
        $suffix .= 'm';
      }      
    }
    if ($hour > 23 || $min > 59) {
      return $this;
    }

    // am/pm
    if ($this->options->default === 12) {
      if ($hour > 12) {
        $hour -= 12;
        $suffix = 'pm';
      }
      else if ($hour === 0) {
        $hour = 12;
        $suffix = 'am';
      }
    }

    // Military
    if ($this->options->default === 24) {
      if ($hour === 12 && $suffix === 'am' && $suffixPresent) {
        $hour = 0;
      }
      elseif ($hour < 12 && $suffix === 'pm') {
        $hour += 12;
      }
      $suffix = '';

      if ($hour < 10) {
        $hour = "0{$hour}";
      }
    }

    $hour = (string) $hour;
    $min  = $min == 0 ? '00' : ($min < 10 ? $min = '0' . (int) $min : (string) $min);
    $sec  = $sec == 0 ? '00' : ($sec < 10 ? $sec = '0' . (int) $sec : (string) $sec);

    $this->parsed = array($hour, $min, $suffix);
    if ($this->options->seconds) {
      $this->parsed[] = $sec;
    }

    return $this;
  }

  public function validate($value) {
    if (empty($value)) {
      return !$this->options->required;
    }
    $this->parse($value);

    return $this->options->seconds ? count($this->parsed) === 4 : count($this->parsed) === 3;
  }

  /**
   * Joins the parsed time array into a string.
   *
   * @return string
   */
  public function join() {
    $colon = $this->options->colon === 'none' ? '' : ':';
    if (!($c = count($this->parsed)) || $c < 3 || $c > 4) {
      return $this->original;
    }

    $joined = array_slice($this->parsed, 0, 2);
    if ($c === 4) {
      $joined[] = $this->parsed[3];
    }

    return implode($colon, $joined) . $this->parsed[2];
  }

  /**
   * Construct a DateTime object using the parse method.
   *
   * A convenience so that you don't have to parse your time, and so that you
   * can send a string as the timezone name.
   *
   * @method getDateTime
   *
   * @param  string $time Any string that passes for self::parse
   * @param  string|\DateTimeZeon $timezone
   *
   * @return \DateTime
   *
   * @see  self::parse().
   */
  public function getDateTime($time, $timezone = 'UTC') {
    if (is_string($timezone)) {
      $timezone = new \DateTimeZone($timezone);
    }
    $time = $this->parse($time)->join();
    
    return new \DateTime($time, $timezone);  
  }

  /**
   * Standardizes the time format.
   *
   * @method standardize
   *
   * @param  string $time
   *   A string representation of time.
   * @param  string|DateTimeZone $timezone
   *   Defaults to 'UTC' when not provided. If a string, it must be a valid
   *   timezone name to use when constructing a \DateTimeZone object.
   *
   * @return string
   *   A string representation of the time in the indicated standard.
   *   - ISO8601 HH:MM:SSO, e.g., 07:45:15-0800.
   */
  public function standardize($time, $timezone = 'UTC') {
    return $this->getDateTime($time, $timezone)->format('H:i:sO');
  }

  /**
   * Localizes a standardized time.
   *
   * The standardized time does not contain a date value, HOWEVER if you need
   * to localize a time for a date that is not now, you may prepend a date
   * string + space in the ISO 8601 format (YYYY-MM-DD) to the value of $time and
   * daylight savings will be taken in to effect.  That would look like this:
   *
   * @code
   *   $this->localize('2015-01-22 09:02:00+0000', 'America/Los_Angeles');  
   * @endcode
   *
   * @method localize
   * @throws  \Exception If $time is improperly formatted, the string must
   *   end in the ISO8601 time format with offset.
   *
   * @param  string $time
   *   Must be in the format HH:MM:SS+-HHMM.
   * @param  string|\DateTimeZone $timezone
   *   The timezone name or object to localize into.  Defaults to 'UTC'.
   *
   * @return string
   *   The localized time string.
   */
  public function localize($time, $timezone = 'UTC') {
    if (!preg_match('/\d{2}:\d{2}:\d{2}[+-]\d{4}$/', $time)) {
      throw new \Exception("Time must be in the format HH:MM:SS+-HHMM, e.g., 08:49:15-0800", 1);
    }
    if (is_string($timezone)) {
      $timezone = new \DateTimeZone($timezone);
    }
    $time = new \DateTime($time);
    $time->setTimeZone($timezone);
    $local = $time->format('H:i:s');

    return $this->parse($local)->join();
  }
}