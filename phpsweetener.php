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


$path = $argv[1];
if (empty($path)) {
  print "You need to provide a file name to compile.\n";
  die;
}

$indent = '  ';
$sweet = file_get_contents($path);

$semicol_lines = array();
$lines = explode("\n", $sweet);
foreach ($lines as $key => $line) {

  if (
     trim($line) == '' # is empty
  || preg_match('/^(class|while|function|if|else|elseif|public|private|static|abstract)\s?/', trim($line)) # starts with a keyword that precedes something that doesnt end with a semicolon
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

$brace_lines = array();
$level = 0;
foreach ($lines as $key => $line) {
  $new_level = indent_level($line);

  if ($new_level == $level) {
    $brace_lines[] = $line;

  } elseif ($new_level > $level) {
    $brace_lines[] = "{\n".$line;

  } elseif ($new_level < $level) {
    $level_difference = $level - $new_level;
    $brace_lines[] = str_repeat("}\n", $level_difference) . $line;
  }
  $level = $new_level;
}

$lines = split("\n", join("\n", $brace_lines));
$brace_lines = array();
$level = 0;
foreach ($lines as $key => $line) {
  # opening brace means upper lever
  if (preg_match('/\{/', trim($line))) { 
    $new_level += 1;
  } elseif (preg_match('/\}/', trim($line))) {
    $new_level -= 1;
  }

  # Indent
  if ($new_level > $level) {
    $brace_lines[] = str_repeat($indent, $level) . $line;

  # Dedent
  } elseif ($new_level < $level) {
    $brace_lines[] = str_repeat($indent, $new_level) . $line;
    # vertical whitespace
    if ($new_level == 0) {
      $brace_lines[] = "\n";
    }

  # Same level indent
  } else {
    $brace_lines[] = $line;
  }
  $level = $new_level;
}

# remove 3 empty lines in a row
$brace_lines = split("\n", join("\n\n", split("\n\n\n", join("\n", $brace_lines))));
print_prog($brace_lines);

