<?php

class WrongIndentationKind extends Exception {};

function get_prog_string($lines) {
  $prog = "<?php \n\n";
  $prog .= join("\n", $lines);
  return $prog;
}

function print_prog($lines) {
  print get_prog_string($lines);
}

$indent = '  ';
$sweet = file_get_contents("testprog.phps");

$semicol_lines = array();
$lines = explode("\n", $sweet);
foreach ($lines as $key => $line) {

  if (
     trim($line) == '' # is empty
  || preg_match('/\)$/', trim($line)) # ends with a closing parenthesis
  || preg_match('/^class /', trim($line)) # starts with a class
  || preg_match('/^else$/', trim($line)) # has an else
  || preg_match('/^\.$/', trim($line)) # ends with a dot
  ) { 
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


# Put Braces

function indent_level($line) {
  global $indent;
  $line_indent = preg_match('/^(\s*)\S/', $line, $matches);
  if ($line_indent) {
    $indent_count = substr_count($line, $indent);
    if ($indent) {
      return $indent_count;
    } else {
      throw new WrongIndentationKind(
        "The indentation is set to '".$indent."', but I found '".$matches[1]."'.");
      return 0;
    }
  } else {
    return 0;
  }
}

function without_empty_lines($lines) {
  $new_lines = array();
  foreach ($lines as $line) {
    if (trim($line) != '') $new_lines[] = $line;
  }
  return $new_lines;
}

$lines = without_empty_lines($define_lines);
$lines[] = "\n";
$lines[] = "\n";

$brace_lines = array();
$level = 0;
foreach ($lines as $key => $line) {
  $new_level = indent_level($line);

  if ($new_level == $level) {
    // echo $new_level.' is equal to '.$level."\n";
    $brace_lines[] = $line;

  } elseif ($new_level > $level) {
    // echo $new_level.' is greater than '.$level."\n";
    $brace_lines[] = "{\n".$line;

  } elseif ($new_level < $level) {
    // echo $new_level.' is lower than '.$level."\n";
    $level_difference = $level - $new_level;
    $brace_lines[] = str_repeat("}\n", $level_difference) . $line;
  }
  $level = $new_level;
}

print_prog($brace_lines);
