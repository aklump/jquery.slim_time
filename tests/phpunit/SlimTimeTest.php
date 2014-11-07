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
 
  public function testIntegarExpansion() {
    $this->assertSlimTimePass('6:00am', 6);
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
    $this->assertSlimTimePass('0:00', '0:00', $obj);
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
    $this->assertSlimTimePass('6:15', '6:15am', $obj);
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