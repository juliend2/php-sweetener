<?php

function remove_extra_lines($code) {
  $code_lines = split("\n", $code);
  array_shift($code_lines);
  array_shift($code_lines);
  array_pop($code_lines);
  array_pop($code_lines);
  return join("\n", $code_lines);
}

# parser helper function for tests
function t_parse($test_instance, $code) {
  return remove_extra_lines($test_instance->parser->set_code($code)->parse());
}

