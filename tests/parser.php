<?php

require_once('simpletest/autorun.php');
include realpath(dirname(__FILE__)."/../phpsweetener.php");

class TestParser extends UnitTestCase {
  function setUp() {
    $this->parser = new Parser();
  }

  function testBasic() {
    $this->assertEqual($this->parser->set_code('$variable = 2')->parse(), '<?php

$variable = 2;

');
  }
}


