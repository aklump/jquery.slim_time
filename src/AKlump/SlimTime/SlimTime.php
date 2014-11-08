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
  public $parsed = array();
  protected $original = '';

  public function __construct($options = array()) {
    $options = (object) $options;
    $this->options = (array) $options + array(
      'fuzzy' => TRUE,
      'required' => FALSE,
      'default' => 12,
      'assume' => 'am',      
    );
    $this->options = (object) $this->options;
  }

  public function parse($string) {
    $this->original = $string;
    $this->parsed = array();

    $regex = '(\d{1,2})\:?(\d{2})?(am|pm|a|p)?';
    if (!$this->options->fuzzy) {
      $regex = "^{$regex}$";
    }

    if (!preg_match("/{$regex}/", $string, $parts) || !isset($parts[1])) {
      return $this;
    }

    if (!isset($parts[2])) {
      $parts[2] = 0;
    }

    $hour   = $parts[1] * 1;
    $min    = $parts[2] * 1;
    $suffix = $this->options->assume;

    if (isset($parts[3])) {
      $suffix = $parts[3];
      if ($suffix === 'a' || $suffix === 'p') {
        $suffix .='m';
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
      if ($hour === 12 && $suffix === 'am' && isset($parts[3])) {
        $hour = 0;
      }
      else if ($hour < 12 && $suffix === 'pm') {
        $hour += 12;
      }
      $suffix = '';

      if ($hour < 10) {
        $hour = "0{$hour}";
      }
    }

    if ($min === 0) {
      $min = '00';
    }
    elseif ($min < 10) {
      $min = "0{$min}";
    }

    $this->parsed = array($hour, $min, $suffix);
    
    return $this;
  }

  public function validate($value) {
    if (empty($value)) {
      return !$this->options->required;
    }
    $this->parse($value);

    return count($this->parsed) === 3;
  }

  /**
   * Joins the parsed time array into a string.
   *
   * @return string
   */
  public function join() {
    return count($this->parsed) === 3 ? $this->parsed[0] . ':' . $this->parsed[1] . $this->parsed[2] : $this->original;
  }
}