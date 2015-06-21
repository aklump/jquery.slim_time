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

  public function testIntegarExpansionColonNone() {
    $obj = new SlimTime(array('colon' => 'none'));
    $this->assertSlimTimePass('615am', '615', $obj);
    $this->assertSlimTimePass('615am', '6:15', $obj);
    $this->assertSlimTimePass('615am', '6:15am', $obj);
    $this->assertSlimTimePass('615pm', '1815', $obj);
    $this->assertSlimTimePass('615pm', '615pm', $obj);
  }

  public function testDefaults615() {
    $obj = new SlimTime;
    $this->assertSlimTimePass('6:15am', '615', $obj);
  }
  

  public function testFailsWhenFuzzyIsTrue() {
    $obj = new SlimTime(array('colon' => 'required'));
    $this->assertSlimTimeFail('The time is 1242 now', $obj);
    $this->assertSlimTimePass('12:42am', 'The time is 12:42 now', $obj);
    $this->assertSlimTimePass('12:00am', 'The time is 12 now', $obj);

    $obj = new SlimTime(array('colon' => 'optional'));
    $this->assertSlimTimePass('12:42am', 'The time is 1242 now', $obj);
    $this->assertSlimTimePass('12:42am', 'The time is 12:42 now', $obj);
    $this->assertSlimTimePass('12:00am', 'The time is 12 now', $obj);

    $obj = new SlimTime(array('colon' => 'none'));
    $this->assertSlimTimePass('1242am', 'The time is 1242 now', $obj);
    $this->assertSlimTimePass('1242am', 'The time is 12:42 now', $obj);
    $this->assertSlimTimePass('1200am', 'The time is 12 now', $obj);    
  }

  /**
   * Provides data for testSecondsOptions.
   *
   * @return 
   *   - 0 array options
   *   - 1 string|int input
   *   - 2 string output
   */
  public function secondsOptionsProvider() {
    $data = array();
      
    // @todo This one is invalid, maybe we should throw an exception, rather than assume pm?
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '18:15:23am', '6:15:23pm');

    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '18:15:23pm', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '18:15:23', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '18:15', '6:15:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '18', '6:00:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '06:15:23am', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '6:15:23am', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '6:15:23', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '6:15', '6:15:00am');

    #9
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '181523pm', '6:15:23pm');
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '181523', '6:15:23pm');
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '1815', '6:15:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '18', '6:00:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '06am', '6:00:00am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), '6am', '6:00:00am');
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '061523am', '6:15:23am');
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '61523am', '6:15:23am');
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '61523', '6:15:23am');
    $data[] = array(FALSE, array('seconds' => TRUE, 'colon' => 'required'), '615', '6:15:00am');

    #19
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '18:15:23pm', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '18:15:23', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '18:15', '6:15:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '18', '6:00:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '06:15:23am', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '6:15:23am', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '6:15:23', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '6:15', '6:15:00am');

    #27
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '181523pm', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '181523', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '1815', '6:15:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '18', '6:00:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '061523am', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '61523am', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '61523', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'optional'), '615', '6:15:00am');

    // Testing colon none; making sure regex is consistent.
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '18:15:23pm', '61523pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '18:15:23', '61523pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '18:15', '61500pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '18', '60000pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '06:15:23am', '61523am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '6:15:23am', '61523am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '6:15:23', '61523am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '6:15', '61500am');

    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '181523pm', '61523pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '181523', '61523pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '1815', '61500pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '18', '60000pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '061523am', '61523am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '61523am', '61523am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '61523', '61523am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'none'), '615', '61500am');

    // Testing seconds turned off.
    $data[] = array(TRUE, array('seconds' => FALSE), '18:15:23pm', '6:15pm');
    $data[] = array(TRUE, array('seconds' => FALSE), '18:15:23', '6:15pm');
    $data[] = array(TRUE, array('seconds' => FALSE), '18:15', '6:15pm');
    $data[] = array(TRUE, array('seconds' => FALSE), '18', '6:00pm');
    $data[] = array(TRUE, array('seconds' => FALSE), '06:15:23am', '6:15am');
    $data[] = array(TRUE, array('seconds' => FALSE), '6:15:23am', '6:15am');
    $data[] = array(TRUE, array('seconds' => FALSE), '6:15:23', '6:15am');
    $data[] = array(TRUE, array('seconds' => FALSE), '6:15', '6:15am');

    // Testing fuzzy.
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 18:15:23pm in the evening', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 18:15:23 in the evening', '6:15:23pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 18:15 in the evening', '6:15:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 18 in the evening', '6:00:00pm');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 06:15:23am in the evening', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 6:15:23am in the evening', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 6:15:23 in the evening', '6:15:23am');
    $data[] = array(TRUE, array('seconds' => TRUE, 'colon' => 'required'), 'It began at about 6:15 in the evening', '6:15:00am');

    return $data;
  }
  
  /**
   * @dataProvider secondsOptionsProvider 
   */
  public function testSecondsOptions($pass, $options, $in, $out) {
    $obj = new SlimTime($options);
    if ($pass) {
      $this->assertSlimTimePass($out, $in, $obj);
    }
    else {
      $this->assertSlimTimeFail($in, $obj);
    }
  }

  public function testIntegarExpansionColonRequired() {
    $obj = new SlimTime(array('colon' => 'required'));
    $this->assertSlimTimeFail('1815', $obj);
    $this->assertSlimTimeFail('615', $obj);
    $this->assertSlimTimePass('6:15am', '6:15', $obj);
    $this->assertSlimTimePass('6:15pm', '18:15', $obj);
  }  

  public function testIntegarExpansion24Hour() {
    $obj = new SlimTime(array('default' => 24));
    $this->assertSlimTimePass('12:07', '12:07', $obj);
    $this->assertSlimTimePass('06:00', 6, $obj);
    $this->assertSlimTimePass('06:00', '6a', $obj);
    $this->assertSlimTimePass('18:00', '6p', $obj);
  }

  public function testIntegarExpansion12HourDELETE() {
    $this->assertSlimTimePass('6:00pm', '6p');
    $this->assertSlimTimePass('6:00am', 6);
    $this->assertSlimTimePass('12:07am', '12:07');
    $this->assertSlimTimePass('6:00am', '6a');
  }

  /**
   * @expectedException \Exception
   * @expectedExceptionMessage Option "seconds" must be boolean.
   */
  public function testNonBoolOptionThrowsException() {
    $obj = new SlimTime(array('seconds' => 'true', 'colon' => 'required'));
  }

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

  public function testIntegarExpansion12Hour() {
    $this->assertSlimTimePass('6:00pm', '6p');
    $this->assertSlimTimePass('6:00am', 6);
    $this->assertSlimTimePass('12:07am', '12:07');
    $this->assertSlimTimePass('6:00am', '6a');
  }

  public function testFailsOutOfRangeMinute() {
    $this->assertSlimTimeFail('9:63am');
  }

  public function testFailsOutOfRangeHour() {
    $this->assertSlimTimePass('3:00am', '34:00am');
  }

  public function testFailsWhenFuzzyIsFalse() {
    $obj = new SlimTime(array('fuzzy' => FALSE));
    $this->assertSlimTimeFail('The time is 12:42 now', $obj);
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







  /**
   * Provides data for testColonOptional12Hour.
   */
  function colonOptional12Hour() {
    $source = $this->getJson();
    $data = array();

    foreach (array(FALSE, TRUE) as $seconds) {

      //
      //
      // default = 12
      // colon = optional
      //
      $default = 12;
      $options = array('default' => $default, 'colon' => 'optional', 'seconds' => $seconds);
      foreach ($source->base as $record) {
        $control = $this->getControlObject($record, $default, $seconds, $assume);
        
        // Choose the output based on the test settings.
        $output  = $control->output['colon'];

        // List valid inputs with and without suffix.
        foreach ($control->input as $input) {
          $data[]  = array(TRUE, $input, $output, $options);
          $data[]  = array(TRUE, $input . $control->suffix, $output, $options);
        }

        // List valid fuzzy inputs
        foreach ($source->fuzzy as $fuzzy) {
          foreach ($control->input as $input) {
            
            // Fuzzy strings without suffix.          
            $i = str_replace('@time', $input, $fuzzy);
            $data[]  = array(TRUE, $i, $output, $options);

            // Fuzzy strings with suffix.
            $i = str_replace('@time', $input . $control->suffix, $fuzzy);
            $data[]  = array(TRUE, $i, $output, $options);
          }
        }
      }

      //
      //
      // default = 24
      // colon = optional
      //
      $default = 24;
      $options = array('default' => $default, 'colon' => 'optional', 'seconds' => $seconds);
      foreach ($source->base as $record) {
        $control = $this->getControlObject($record, $default, $seconds, $assume);
        
        // Choose the output based on the test settings.
        $output  = $control->output['colon'];

        // List valid inputs with and without suffix.
        foreach ($control->input as $input) {
          $data[]  = array(TRUE, $input, $output, $options);
          $data[]  = array(TRUE, $input . $control->suffix, $output, $options);
        }

        // List valid fuzzy inputs
        foreach ($source->fuzzy as $fuzzy) {
          foreach ($control->input as $input) {
            
            // Fuzzy strings without suffix.          
            $i = str_replace('@time', $input, $fuzzy);
            $data[]  = array(TRUE, $i, $output, $options);

            // Fuzzy strings with suffix.
            $i = str_replace('@time', $input . $control->suffix, $fuzzy);
            $data[]  = array(TRUE, $i, $output, $options);
          }
        }
      }
    }

    return $data;
  }
  /**
   * @dataProvider colonOptional12Hour
   */
  public function testColonOptional12Hour($pass, $input, $out, $options) {
    $obj = new SlimTime($options);
    if ($pass) {
      $this->assertSlimTimePass($out, $input, $obj);
    }
    else {
      $this->assertSlimTimeFail($input, $obj);
    }
  } 
  /**
   * Provides data for testColonRequired12Hour.
   */
  function colonRequired12Hour() {
    $source = $this->getJson();
    $data = array();

    foreach ($source->base as $record) {
      $control = $this->getControlObject($record, 12, FALSE);
      
      // Choose the output based on the test settings.
      $output  = $control->output['colon'];

      // List valid inputs with and without suffix.
      $data[]  = array(TRUE, $control->input['colon'], $output);
      $data[]  = array(TRUE, $control->input['colon'] . $control->suffix, $output);

      // If the colon input does not contain a colon then this should pass
      // because that means it's a whole hour without min or sec.
      $noColonWillPass = strpos($control->input['colon'], ':') === FALSE; 
      $data[]  = array($noColonWillPass, $control->input['noColon'], $output);
      $data[]  = array($noColonWillPass, $control->input['noColon'] . $control->suffix, $output);

      // List valid fuzzy inputs
      foreach ($source->fuzzy as $fuzzy) {
          // Fuzzy strings without suffix.          
          $i = str_replace('@time', $control->input['colon'], $fuzzy);
          $data[]  = array(TRUE, $i, $output);

          // Fuzzy strings with suffix.
          $i = str_replace('@time', $control->input['colon'] . $control->suffix, $fuzzy);
          $data[]  = array(TRUE, $i, $output);

          // Fuzzy strings without suffix.          
          $i = str_replace('@time', $control->input['noColon'], $fuzzy);
          $data[]  = array($noColonWillPass, $i, $output);

          // Fuzzy strings with suffix.
          $i = str_replace('@time', $control->input['noColon'] . $control->suffix, $fuzzy);
          $data[]  = array($noColonWillPass, $i, $output);          
      }
    }

    return $data;
  }
  /**
   * @dataProvider colonRequired12Hour
   */
  public function testColonRequired12Hour($pass, $input, $out) {
    $obj = new SlimTime(array('default' => 12, 'colon' => 'required'));
    if ($pass) {
      $this->assertSlimTimePass($out, $input, $obj);
    }
    else {
      $this->assertSlimTimeFail($input, $obj);
    }
  } 
  /**
   * Provides data for testColonNone12Hour.
   */
  function colonNone12Hour() {
    $source = $this->getJson();
    $data = array();

    foreach ($source->base as $record) {
      $control = $this->getControlObject($record, 12, FALSE);
      
      // Choose the output based on the test settings.
      $output  = $control->output['noColon'];

      // 
      // seconds turned off.
      // List valid inputs with and without suffix.
      // 
      foreach ($control->input as $input) {
        $data[]  = array(TRUE, $input, $output);
        $data[]  = array(TRUE, $input . $control->suffix, $output);
      }

      // 
      // 
      // List valid fuzzy inputs
      // 
      foreach ($source->fuzzy as $fuzzy) {
        foreach ($control->input as $input) {
          
          // Fuzzy strings without suffix.          
          $i = str_replace('@time', $input, $fuzzy);
          $data[]  = array(TRUE, $i, $output);

          // Fuzzy strings with suffix.
          $i = str_replace('@time', $input . $control->suffix, $fuzzy);
          $data[]  = array(TRUE, $i, $output);
        }
      }
    }

    return $data;
  }
  /**
   * @dataProvider colonNone12Hour
   */
  public function testColonNone12Hour($pass, $input, $out) {
    $obj = new SlimTime(array('default' => 12, 'colon' => 'none'));
    if ($pass) {
      $this->assertSlimTimePass($out, $input, $obj);
    }
    else {
      $this->assertSlimTimeFail($input, $obj);
    }
  }
  




  /**
   * Return a control object for 24 Hour Time
   *
   * @param  [type] $record [description]
   * @param  [type] $secs [description]
   *
   * @return [type] [description]
   */
  protected function getControlObject($record, $default, $secs) {
    $control                    = new \stdClass;
    
    $control->input['colon']    = $record[1];
    $control->input['noColon']  = str_replace(':', '', $record[1]);
    $control->suffix            = $record[0];
    
    if ($default == 12) {
      $output                     = $secs ? $record[3] : $record[2];
    }
    else {
      $output                     = $secs ? $record[5] : $record[4];  
    }
    
    $control->output['colon']   = $output;
    $control->output['noColon'] = str_replace(':', '', $output);

    return $control;    
  }

  public function getJson() {
    return json_decode(file_get_contents(dirname(__FILE__) . '/../test_data.json'));
  }
  
}
