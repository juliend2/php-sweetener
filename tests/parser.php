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
//     $this->assertEqual(t_parse($this, 
// 'interface iTemplate
//   public function setVariable($name, $var)
//   public function getHtml($template)
//   '), 
// '
// interface iTemplate
// {
//   public function setVariable($name, $var);
//   public function getHtml($template);
// }
// ');
  }

  function testWhile() {
    $this->assertEqual(t_parse($this, 
'
while (true)
  something("infinitely")
'), 
'while (true)
{
  something("infinitely");
}
');
  }
  function testIfs() {
    $this->assertEqual(t_parse($this, 
'
if (true)
  something("once")
'), 
'if (true)
{
  something("once");
}
');

    $this->assertEqual(t_parse($this, 
'if (true)
  something("once")
else
  something("else")
'), 
'if (true)
{
  something("once");
}
else
{
  something("else");
}
');

    $this->assertEqual(t_parse($this, 
'if (true)
  something("once")
elseif (false)
  something("falsey")
else
  something("else")
'), 
'if (true)
{
  something("once");
}
elseif (false)
{
  something("falsey");
}
else
{
  something("else");
}
');

    $this->assertEqual(t_parse($this, 
'if (true)
  $var = something("once")
  $var->method()
elseif (false)
  something("falsey")
  do("something else")
else
  something("else")
  another("else")
'), 
'if (true)
{
  $var = something("once");
  $var->method();
}
elseif (false)
{
  something("falsey");
  do("something else");
}
else
{
  something("else");
  another("else");
}
');

    $this->assertEqual(t_parse($this, 
'if (true)
  $var = something("once")
elseif (false)
  something("falsey")
elseif (1)
  something("truthy")
else
  something("else")
'), 
'if (true)
{
  $var = something("once");
}
elseif (false)
{
  something("falsey");
}
elseif (1)
{
  something("truthy");
}
else
{
  something("else");
}
');
  }

  function testForAndForeach() {
    $this->assertEqual(t_parse($this, 
'for ($i = 0; $i < count($arr); $i++)
  print $arr[$i]
'), 
'for ($i = 0; $i < count($arr); $i++)
{
  print $arr[$i];
}
');
    $this->assertEqual(t_parse($this, 
'foreach ($hash as $key => $value)
  print $hash[$key]
'), 
'foreach ($hash as $key => $value)
{
  print $hash[$key];
}
');
  }

}


