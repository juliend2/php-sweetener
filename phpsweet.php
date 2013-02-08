<?php

$sweet = file_get_contents("testprog.phps");

$semicol_lines = array();
$lines = explode("\n", $sweet);
foreach ($lines as $key => $line) {

  # ends with a ')'
  if (preg_match('/\)$/', trim($line)) 
  || trim($line) == ''
  || preg_match('/^class /', $line)) { 
    # as is
    $semicol_lines[] = $line;

  # put a ';' at the end
  } else {
    $semicol_lines[] = $line . ';';

  }
}

# Put Defines
$define_lines = array();
foreach ($semicol_lines as $key => $line) {
  if (preg_match('/^([_A-Z][_A-Z]*)\s?=\s?(.*);$/', $line, $matches)) {
    $define_lines[] = 'define("'.$matches[1].'", '.$matches[2].');';
  } else {
    # as is
    $define_lines[] = $line;
  }
}

print join("\n", $define_lines);
