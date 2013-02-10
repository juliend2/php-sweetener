<?php

class Outputter {
  function __construct($lines) {
    $this->lines = $lines;
  }
  public function puts() {
    print $this->get_string($this->lines);
  }
  public function get_string() {
    $prog = "<?php\n\n";
    $prog .= join("\n", $this->lines);
    return $prog;
  }
  public function compile_to($path) {
    $fp = fopen($path, 'w');
    fwrite($fp, $this->get_string());
    fclose($fp);
  }
}

