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
