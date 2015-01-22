<?php
/**
 * @file
 * PHPUnit tests for the SlimTime class
 *
 * Most tests should be mimicked in ../qunit/test.js
 */
namespace AKlump\SlimTime;
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

class SlimTimeTest extends \PHPUnit_Framework_TestCase {

  public function testStandardizeAssumingPM() {
    $obj = new SlimTime(array(
      'assume' => 'pm',
    ));
    $this->assertSame('20:12:00+0000', $obj->standardize('8:12'));
    $this->assertSame('20:12:00+0000', $obj->standardize('812'));
  }

  public function testLocalizeWithPSTDatePrepended() {
    $obj = new SlimTime;
    $this->assertSame('6:40am', $obj->localize('2015-01-15 14:40:00+0000', 'America/Los_Angeles'));
    $this->assertSame('7:40am', $obj->localize('2015-08-15 14:40:00+0000', 'America/Los_Angeles'));
  }

  /**
   * @expectedException Exception
   */
  public function testLocalizeBadFormatException() {
    $obj = new SlimTime;
    $obj->localize('breakfast');
  }

  public function testLocalizeSwitchingTimeZones() {
    $obj = new SlimTime;
    $tz = new \DateTimeZone('America/Los_Angeles');

    // Assert that localizing a UTC date converts the time value.
    $control = '6:40am';
    if (($dst = (bool) date_create('now', $tz)->format('I'))) {
      $control = '7:40am';
    }
    $this->assertSame($control, $obj->localize('14:40:00+0000', $tz));
  }

  public function testLocalizeSameTimeZone() {
    $obj = new SlimTime;
    $this->assertSame('8:04am', $obj->localize('08:04:00+0000', 'UTC'));

    $tz = new \DateTimeZone('America/Los_Angeles');
    $control_offset = new \DateTime('now', $tz);
    $control_offset = $control_offset->format('O');    
    $this->assertSame('8:04am', $obj->localize('08:04:00' . $control_offset, 'America/Los_Angeles'));
  }

  public function testStandardize() {
    $obj = new SlimTime;

    // Default is in UTC
    $this->assertSame('08:04:00+0000', $obj->standardize('8:04am'));
    $this->assertSame('08:04:00+0000', $obj->standardize('8:04am'), 'UTC');

    // Check when in LA tz.
    $tz = new \DateTimeZone('America/Los_Angeles');
    $control_offset = new \DateTime('now', $tz);
    $control_offset = $control_offset->format('O');
    $this->assertSame('08:04:00' . $control_offset, $obj->standardize('8:04am', $tz)); 
  }

  public function testIntegarExpansion12HourDELETE() {
    $this->assertSlimTimePass('6:00pm', '6p');
    $this->assertSlimTimePass('6:00am', 6);
    $this->assertSlimTimePass('12:07am', '12:07');
    $this->assertSlimTimePass('6:00am', '6a');
  }


  public function testIntegarExpansionColonOptional() {
    $this->assertSlimTimePass('6:15am', '615');
    $this->assertSlimTimePass('6:15pm', '1815');
    $this->assertSlimTimePass('6:15am', '6:15');
    $this->assertSlimTimePass('6:15pm', '18:15');

    $obj = new SlimTime(array('colon' => 'optional'));
    $this->assertSlimTimePass('6:15am', '615', $obj);
    $this->assertSlimTimePass('6:15pm', '1815', $obj);
    $this->assertSlimTimePass('6:15am', '6:15', $obj);
    $this->assertSlimTimePass('6:15pm', '18:15', $obj);
  }

  public function testIntegarExpansionColonNone() {
    $obj = new SlimTime(array('colon' => 'none'));
    $this->assertSlimTimePass('615am', '615', $obj);
    $this->assertSlimTimePass('615pm', '1815', $obj);
    $this->assertSlimTimePass('615am', '6:15', $obj);
    $this->assertSlimTimePass('615pm', '18:15', $obj);
  }

  public function testIntegarExpansionColonRequired() {
    $obj = new SlimTime(array('colon' => 'required'));
    $this->assertSlimTimeFail('615', $obj);
    $this->assertSlimTimeFail('1815', $obj);
    $this->assertSlimTimePass('6:15am', '6:15', $obj);
    $this->assertSlimTimePass('6:15pm', '18:15', $obj);
  }

  public function testIntegarExpansion12Hour() {
    $this->assertSlimTimePass('6:00pm', '6p');
    $this->assertSlimTimePass('6:00am', 6);
    $this->assertSlimTimePass('12:07am', '12:07');
    $this->assertSlimTimePass('6:00am', '6a');
  }

  public function testIntegarExpansion24Hour() {
    $obj = new SlimTime(array('default' => 24));
    $this->assertSlimTimePass('06:00', 6, $obj);
    $this->assertSlimTimePass('12:07', '12:07', $obj);
    $this->assertSlimTimePass('06:00', '6a', $obj);
    $this->assertSlimTimePass('18:00', '6p', $obj);
  }

  public function testFailsOutOfRangeMinute() {
    $this->assertSlimTimeFail('9:63am');
  }

  public function testFailsOutOfRangeHour() {
    $this->assertSlimTimeFail('34:00am');
  }

  public function testFailsWhenFuzzyIsFalse() {
    $obj = new SlimTime(array('fuzzy' => FALSE));
    $this->assertSlimTimeFail('The time is 12:42 now', $obj);
  }

  public function testFailsWhenFuzzyIsTrue() {
    $this->assertSlimTimePass('12:42am', 'The time is 12:42 now');
  }

  public function testPassThruMidnightIn12Hour() {
    $this->assertSlimTimePass('12:00am', '12:00am');
  }

  public function testPassThruMidnightIn24Hour() {
    $obj = new SlimTime(array('default' => 24));
    $this->assertSlimTimePass('00:00', '0:00', $obj);
  }

  public function testConvertsMidnightIn12Hour() {
    $this->assertSlimTimePass('12:00am', '0:00am');
  }

  public function testConvertsMidnightIn24Hour() {
    $this->assertSlimTimePass('12:00am', '0:00');
  }

  public function testConvertsTo24HourDefaultIs24() {
    $obj = new SlimTime(array('default' => 24));
    $this->assertSlimTimePass('18:15', '6:15pm', $obj);
    $this->assertSlimTimePass('06:15', '6:15am', $obj);
    $this->assertSlimTimePass('07:00', '7', $obj);
  }

  public function testAppendsSuffixUsingDefaults() {
    $this->assertSlimTimePass('6:15am', '6:15');
    $this->assertSlimTimePass('1:15pm', '13:15');
  }

  public function testAppendsSuffixPassingAmOption() {
    $obj = new SlimTime(array('assume' => 'am'));
    $this->assertSlimTimePass('6:15am', '6:15', $obj);
    $this->assertSlimTimePass('1:15pm', '13:15', $obj);
  }

  public function testAppendsSuffixPassingPmOption() {
    $obj = new SlimTime(array('assume' => 'pm'));
    $this->assertSlimTimePass('6:15pm', '6:15', $obj);    
    $this->assertSlimTimePass('6:15am', '6:15am', $obj);
    $this->assertSlimTimePass('1:15pm', '13:15', $obj);
  }

  public function testRequired() {
    $obj = new SlimTime;
    $this->assertTrue($obj->validate(''));

    $obj = new SlimTime(array('required' => TRUE));
    $this->assertFalse($obj->validate(''));
  }

  public function testUndefined() {
    $this->assertFalse(SlimTime::undefined('6'));
    $this->assertFalse(SlimTime::undefined(6));
    $this->assertFalse(SlimTime::undefined(0));
    $this->assertFalse(SlimTime::undefined('0'));
    $this->assertTrue(SlimTime::undefined(''));
    $this->assertTrue(SlimTime::undefined(NULL));
  }

  //
  // Custom assertions below here
  // 
  public function assertSlimTimePass($expected, $value, $obj = NULL) {
    $obj = $obj ? $obj : new SlimTime;
    $this->assertSame($expected, $obj->parse($value)->join());
    $this->assertTrue($obj->validate($value));
  } 
  
  public function assertSlimTimeFail($value, $obj = NULL) {
    $obj = $obj ? $obj : new SlimTime;
    $this->assertSame($value, $obj->parse($value)->join());
    $this->assertFalse($obj->validate($value));
  } 
}
