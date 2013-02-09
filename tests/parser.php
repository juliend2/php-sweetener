<?php

require_once('simpletest/autorun.php');
include realpath(dirname(__FILE__)."/../phpsweetener.php");

class TestParser extends UnitTestCase {
  function setUp() {
    $this->parser = new Parser();
  }

  function testVariable() {
    $this->assertEqual(t_parse($this, '$variable = 2'), '$variable = 2;');
  }
}


