// Most tests should be mimicked in ../phpunit/SlimTimeTest.php

//
// Colon None
// 
QUnit.test("Pass for colon none: 615", function(assert) {
  var val = $('#time').slimTime({"colon":"none"}).val('615').blur().val();
  assert.strictEqual(val, '615am');
});
QUnit.test("Pass for colon none: 6:15", function(assert) {
  var val = $('#time').slimTime({"colon":"none"}).val('6:15').blur().val();
  assert.strictEqual(val, '615am');
});
QUnit.test("Pass for colon none: 1815", function(assert) {
  var val = $('#time').slimTime({"colon":"none"}).val('1815').blur().val();
  assert.strictEqual(val, '615pm');
});
QUnit.test("Pass for colon none: 18:15", function(assert) {
  var val = $('#time').slimTime({"colon":"none"}).val('18:15').blur().val();
  assert.strictEqual(val, '615pm');
});
QUnit.test("Pass for colon optional: 615", function(assert) {
  var val = $('#time').slimTime().val('615').blur().val();
  assert.strictEqual(val, '6:15am');
});

//
// Colon required
// 
QUnit.test("Fail for colon required:615", function(assert) {
  var val = $('#time').slimTime({"colon":"required"}).val('615').blur().val();
  assert.ok($('#time').hasClass('slim-time-error'));
  assert.strictEqual(val, '615');
});
QUnit.test("Fail for colon required:1815", function(assert) {
  var val = $('#time').slimTime({"colon":"required"}).val('1815').blur().val();
  assert.ok($('#time').hasClass('slim-time-error'));
  assert.strictEqual(val, '1815');
});
QUnit.test("Pass for colon required:6:15", function(assert) {
  var val = $('#time').slimTime({"colon":"required"}).val('6:15').blur().val();
  assert.strictEqual(val, '6:15am');
});
QUnit.test("Pass for colon required:18:15", function(assert) {
  var val = $('#time').slimTime({"colon":"required"}).val('18:15').blur().val();
  assert.strictEqual(val, '6:15pm');
});

// QUnit.test("Pass for fuzzy is True", function(assert) {
//   var sets = [
//     ['18:15:23pm', '6:15:23pm'],
//     ['18:15:23',   '6:15:23pm'],
//     ['18:15',      '6:15:00pm'],
//     ['18',         '6:00:00pm'],
//     ['06:15:23am', '6:15:23am'],
//     ['6:15:23am',  '6:15:23am'],
//     ['6:15:23',    '6:15:23am'],
//     ['6:15',       '6:15:00am'],
//   ];
//   for (var i = sets.length - 1; i >= 0; i--) {
//     var val = $('#time').slimTime({seconds: true}).val(sets[i][0]).blur().val();
//     assert.strictEqual(val, sets[i][1]);
//   }
// });

// Seconds option
QUnit.test("Pass for seconds: 18:15:23", function(assert) {
  var sets = [
    ['18:15:23pm', '6:15:23pm'],
    ['18:15:23',   '6:15:23pm'],
    ['18:15',      '6:15:00pm'],
    ['18',         '6:00:00pm'],
    ['06:15:23am', '6:15:23am'],
    ['6:15:23am',  '6:15:23am'],
    ['6:15:23',    '6:15:23am'],
    ['6:15',       '6:15:00am'],
  ];
  for (var i = sets.length - 1; i >= 0; i--) {
    var val = $('#time').slimTime({seconds: true}).val(sets[i][0]).blur().val();
    assert.strictEqual(val, sets[i][1]);
  }
});
//
// Colon Optional
// 
QUnit.test("Pass for colon optional: 1815", function(assert) {
  var val = $('#time').slimTime().val('1815').blur().val();
  assert.strictEqual(val, '6:15pm');
});
QUnit.test("Pass for colon optional: 6:15", function(assert) {
  var val = $('#time').slimTime().val('6:15').blur().val();
  assert.strictEqual(val, '6:15am');
});
QUnit.test("Pass for colon optional: 18:15", function(assert) {
  var val = $('#time').slimTime().val('18:15').blur().val();
  assert.strictEqual(val, '6:15pm');
});
QUnit.test("Pass for colon optional: 615", function(assert) {
  var val = $('#time').slimTime({"colon":"optional"}).val('615').blur().val();
  assert.strictEqual(val, '6:15am');
});
QUnit.test("Pass for colon optional: 1815", function(assert) {
  var val = $('#time').slimTime({"colon":"optional"}).val('1815').blur().val();
  assert.strictEqual(val, '6:15pm');
});
QUnit.test("Pass for colon optional: 6:15", function(assert) {
  var val = $('#time').slimTime({"colon":"optional"}).val('6:15').blur().val();
  assert.strictEqual(val, '6:15am');
});
QUnit.test("Pass for colon optional: 18:15", function(assert) {
  var val = $('#time').slimTime({"colon":"optional"}).val('18:15').blur().val();
  assert.strictEqual(val, '6:15pm');
});

//
//
//

QUnit.test("Passing 6p works in 12 hours", function(assert) {
  var val = $('#time').slimTime().val('6p').blur().val();
  assert.strictEqual(val, '6:00pm');  
});

QUnit.test("Pasing 6a works in 12 hours", function(assert) {
  var val = $('#time').slimTime().val('6a').blur().val();
  assert.strictEqual(val, '6:00am');  
});

QUnit.test("Passing 12:07 in 12 returns 12:07pm", function(assert) {
  var val = $('#time').slimTime().val('12:07').blur().val();
  assert.strictEqual(val, '12:07am');  
});

QUnit.test("Passing 6p works in 24 hours", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('6p')
  .blur()
  .val();
  assert.strictEqual(val, '18:00');  
});

QUnit.test("Pasing 6a works in 24 hours", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('6a')
  .blur()
  .val();
  assert.strictEqual(val, '06:00');  
});

QUnit.test("Passing 12:07 in 24 returns 12:07", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('12:07')
  .blur()
  .val();
  assert.strictEqual(val, '12:07');  
});

QUnit.test("Prepend the 0 to hours in 24 hour time < 12", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('7')
  .blur()
  .val();
  assert.strictEqual(val, '07:00');  
});

QUnit.test("Correctly handle 12:07", function(assert) {
  var val = $('#time')
  .slimTime()
  .val('12:07')
  .blur()
  .val();
  assert.strictEqual('12:07am', val);    
});

QUnit.test("Expands integar to full time", function(assert) {
  var val = $('#time')
  .slimTime()
  .val('6')
  .blur()
  .val();
  assert.strictEqual('6:00am', val);  
});

QUnit.test("Fails an out of range minute", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm","fuzzy":false})
  .val('9:63am')
  .blur()
  .val();
  assert.strictEqual('9:63am', val);
  assert.ok($('#time').hasClass('slim-time-error'));  
});

QUnit.test("Fails an out of range hour", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm","fuzzy":false})
  .val('34:00am')
  .blur()
  .val();
  assert.ok($('#time').hasClass('slim-time-error'));  
});

QUnit.test("Does not extract/adds error class from a sentence when fuzzy is true.", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm","fuzzy":false})
  .val('The time is 12:42 now')
  .blur()
  .val();
  assert.strictEqual('The time is 12:42 now', val);
  assert.ok($('#time').hasClass('slim-time-error'));
});

QUnit.test("Extracts from a sentence when fuzzy is true.", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm"})
  .val('The time is 12:42 now')
  .blur()
  .val();
  assert.strictEqual('12:42pm', val);
  assert.ok(!$('#time').hasClass('slim-time-error'));
});

QUnit.test("Invalid callback is fired.", function(assert) {
  var called = false;
  var args = {};
  var callback = function (entered, parsed, $element, slimTime) {
    called = true;
    args = [entered, parsed, $element, slimTime];
  };

  assert.strictEqual(false, called);

  $('#time')
  .slimTime({"fail": callback})
  .val('sore back')
  .blur();

  assert.strictEqual(true, called);
  assert.strictEqual('sore back', args[0]);
  assert.strictEqual('undefined', typeof args[1][0]);
  assert.strictEqual('undefined', typeof args[1][1]);
  assert.strictEqual('undefined', typeof args[1][2]);
  assert.equal('time', args[2].attr('id'));
  assert.ok(typeof args[3].parse === 'function');
});

QUnit.test("Valid callback is fired.", function(assert) {
  var called = false;
  var args = {};
  var callback = function (entered, parsed, $element, slimTime) {
    called = true;
    args = [entered, parsed, $element, slimTime];
  };

  assert.strictEqual(false, called);

  $('#time')
  .slimTime({"pass": callback})
  .val('12:28pm')
  .blur();

  assert.strictEqual(true, called);
  assert.strictEqual('12:28pm', args[0]);
  assert.strictEqual('12', args[1][0]);
  assert.strictEqual('28', args[1][1]);
  assert.strictEqual('pm', args[1][2]);
  assert.equal('time', args[2].attr('id'));
  assert.ok(typeof args[3].parse === 'function');
});

QUnit.test("Removes css error class when valid text on blur.", function(assert) {
  $('#time')
  .slimTime()
  .val('12:28pm')
  .addClass('slim-time-error');
  assert.ok($('#time').hasClass('slim-time-error'));

  var val = $('#time')
  .blur()
  .val();
  assert.strictEqual(val, '12:28pm');
  assert.ok(!$('#time').hasClass('slim-time-error'));
});

QUnit.test("Applies css error class when invalid text on blur.", function(assert) {
  var val = $('#time')
  .slimTime()
  .val('elephant')
  .blur()
  .val();
  assert.strictEqual(val, 'elephant');
  assert.ok($('#time').hasClass('slim-time-error'));
});

QUnit.test("Passthru midnight in 12 hour time", function(assert) {
  var val = $('#time')
  .slimTime({"default":12})
  .val('12:00am')
  .blur()
  .val();
  assert.strictEqual(val, '12:00am');
});

QUnit.test("Passthru midnight in military time", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('0:00')
  .blur()
  .val();
  assert.strictEqual(val, '00:00');
});

QUnit.test("Converts midnight in 12 hour time", function(assert) {
  var val = $('#time')
  .slimTime({"default":12})
  .val('0:00')
  .blur()
  .val();
  assert.strictEqual(val, '12:00am');
});

QUnit.test("Converts midnight in military time", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('12:00am')
  .blur()
  .val();
  assert.strictEqual(val, '00:00');
});

QUnit.test("Converts to military time when options.default = 24: > 12", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('6:15pm')
  .blur()
  .val();
  assert.strictEqual(val, '18:15');
});

QUnit.test("Converts to military time when options.default = 24: < 12", function(assert) {
  var val = $('#time')
  .slimTime({"default":24})
  .val('6:15am')
  .blur()
  .val();
  assert.strictEqual(val, '06:15');
});

QUnit.test("Uses options.assume for 6: default", function(assert) {
  var val = $('#time')
  .slimTime()
  .val('6:15')
  .blur()
  .val();
  assert.strictEqual(val, '6:15am');
});

QUnit.test("Uses pm default options.assume for 13:15", function(assert) {
  var val = $('#time')
  .slimTime()
  .val('13:15')
  .blur()
  .val();
  assert.strictEqual(val, '1:15pm');
});

QUnit.test("Uses options.assume for 6: options.assume = am", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"am"})
  .val('6:15')
  .blur()
  .val();
  assert.strictEqual(val, '6:15am');
});

QUnit.test("Uses pm default options.assume for 13:15: options.assume = am", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"am"})
  .val('13:15')
  .blur()
  .val();
  assert.strictEqual(val, '1:15pm');
});

QUnit.test("Uses options.assume for 6: options.assume = pm", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm"})
  .val('6:15')
  .blur()
  .val();
  assert.strictEqual(val, '6:15pm');
});

QUnit.test("Uses options.assume for 6: options.assume = pm", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm"})
  .val('6:15am')
  .blur()
  .val();
  assert.strictEqual(val, '6:15am');
});

QUnit.test("Uses pm default options.assume for 13:15: options.assume = pm", function(assert) {
  var val = $('#time')
  .slimTime({"assume":"pm"})
  .val('13:15')
  .blur()
  .val();
  assert.strictEqual(val, '1:15pm');
});

QUnit.test("css class is correct", function(assert) {
  $('#time').slimTime();
  assert.ok($('#time').hasClass('slim-time-processed'));
  
  $('#time').slimTime({"cssPrefix":"st-"});
  assert.ok($('#time').hasClass('st-processed'));
});

QUnit.test("Test required option", function(assert) {  
  
  // Is not required.
  $('#time').slimTime();
  $('#time').focus().blur();
  assert.ok(!$('#time').hasClass('slim-time-error'));

  // Is required.
  $('#time').slimTime({"required":true});
  assert.ok($('#time').hasClass('slim-time-required'));
  $('#time').focus().blur();
  assert.ok($('#time').hasClass('slim-time-error'));
});

QUnit.testStart(function () {
  $('body').append('<input id="time"/>');
});

QUnit.testDone(function () {
  $('#time').remove();
});