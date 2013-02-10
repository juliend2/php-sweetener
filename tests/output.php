<?php

require_once('simpletest/autorun.php');
include realpath(dirname(__FILE__)."/../phpsweetener.php");

class TestOutput extends UnitTestCase {
  function setUp() {
    # create an instance of Output or Parser
  }
  function tearDown() {
    # clean up the tests/tmp directory
  }
  # my tests:
  function testCompile() {
    `bin/phpsweeten examples/miniprog.phps -o tests/tmp/miniprog.php`;
    $compiled_code = file_get_contents('tests/tmp/miniprog.php');
    $this->assertEqual($compiled_code, 
'<?php

$myvar = 2;
$myothervar = 3;
echo $myvar + $myothervar;

'
);
  }
  function testOutput() {
  }
}
