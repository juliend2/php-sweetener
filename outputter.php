<?php

class Outputter {
  function __construct($lines) {
    $this->lines = $lines;
  }
  public function puts() {
    print $this->get_string($this->lines);
  }
  public function get_string() {
    $prog = "<?php \n\n";
    $prog .= join("\n", $this->lines);
    return $prog;
  }
}

