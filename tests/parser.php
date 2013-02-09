<?php

require_once('simpletest/autorun.php');
include realpath(dirname(__FILE__)."/../phpsweetener.php");

class TestParser extends UnitTestCase {
  function setUp() {
    $this->parser = new Parser();
  }

  function testComments() {
    $this->assertEqual(t_parse($this, '# comment'), '# comment');
    $this->assertEqual(t_parse($this, '// comment'), '// comment');
  }
  function testVariable() {
    $this->assertEqual(t_parse($this, '$str'), '$str;');
    $this->assertEqual(t_parse($this, '$variable = 2'), '$variable = 2;');
    $this->assertEqual(t_parse($this, '$str = "Joie"'), '$str = "Joie";');
  }
  function testConstants() {
    $this->assertEqual(t_parse($this, 'TWENTYTWO = 22'), 'define("TWENTYTWO", 22);');
    $this->assertEqual(t_parse($this, 'HOME = "/home/julien/"'), 'define("HOME", "/home/julien/");');
  }
  function testIncrementation() {
    $this->assertEqual(t_parse($this, '$i += 1'), '$i += 1;');
    $this->assertEqual(t_parse($this, '$hello .= " World"'), '$hello .= " World";');
  }
  function testInstanciation() {
    $this->assertEqual(t_parse($this, '$obj = new Object()'), '$obj = new Object();');
    $this->assertEqual(t_parse($this, '$obj = new Object(2)'), '$obj = new Object(2);');
    $this->assertEqual(t_parse($this, '$obj = new Object(2, "joy")'), '$obj = new Object(2, "joy");');
  }
  function testPropertyAccessor() {
    $this->assertEqual(t_parse($this, '$obj->prop1'), '$obj->prop1;');
    $this->assertEqual(t_parse($this, '$obj->prop1->prop2'), '$obj->prop1->prop2;');
    $this->assertEqual(t_parse($this, '$obj->prop1->prop2->method()'), '$obj->prop1->prop2->method();');
    $this->assertEqual(t_parse($this, '$obj->prop1->method()'), '$obj->prop1->method();');
    $this->assertEqual(t_parse($this, '$obj->method()->prop1'), '$obj->method()->prop1;');
    $this->assertEqual(t_parse($this, '$obj->method()->method2()->prop1'), '$obj->method()->method2()->prop1;');
    $this->assertEqual(t_parse($this, 
'$obj
->prop1'), 
'$obj
->prop1;');
$this->assertEqual(t_parse($this, 
'$obj
->prop1
->prop2'), 
'$obj
->prop1
->prop2;');
$this->assertEqual(t_parse($this, 
'$obj
->prop1
->prop2
->method()'), 
'$obj
->prop1
->prop2
->method();');
$this->assertEqual(t_parse($this, 
'$obj
->prop1
->method()'), 
'$obj
->prop1
->method();');
$this->assertEqual(t_parse($this, 
'$obj
->method()
->prop1'), 
'$obj
->method()
->prop1;');
$this->assertEqual(t_parse($this, 
'$obj
->method()
->method2()
->prop1'), 
'$obj
->method()
->method2()
->prop1;');
  }
  function testMethodAccessor() {
    $this->assertEqual(t_parse($this, '$obj->method()'), '$obj->method();');
    $this->assertEqual(t_parse($this, '$obj->method()->method2()'), '$obj->method()->method2();');
    $this->assertEqual(t_parse($this, '$obj->method()->method2()->method3()'), '$obj->method()->method2()->method3();');
    $this->assertEqual(t_parse($this, 
'$obj->
method()->
method2()->
method3()'), 
'$obj->
method()->
method2()->
method3();');
    $this->assertEqual(t_parse($this, 
'$obj
->method()
->method2()
->method3()'), 
'$obj
->method()
->method2()
->method3();');
    $this->assertEqual(t_parse($this, 
'$obj
->method()
->method2()
->method3()'), 
'$obj
->method()
->method2()
->method3();');
  }

  function testClasses() {
    $this->assertEqual(t_parse($this, 
'class Post
  function __construct()
    return "Something"'), 
'class Post
{
  function __construct()
  {
    return "Something";
  }
}
');
    $this->assertEqual(t_parse($this, 
'class Thing
  function __construct()
    return "Something"
  function get_thing()
    return $this->thing
  '), 
'class Thing
{
  function __construct()
  {
    return "Something";
  }
  function get_thing()
  {
    return $this->thing;
  }
}
');
  }

}


