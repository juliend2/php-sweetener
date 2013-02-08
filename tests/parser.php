<?php

require_once('simpletest/autorun.php');
include "../phpsweetener.php";

class TestParser extends UnitTestCase {
  function setUp() {
    $this->var = 'joie';
  }

  function testLoggingInIsLogged() {
    $this->assertEqual($this->var, 'joie');
  }
}


