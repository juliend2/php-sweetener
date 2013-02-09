<?php

class Parser {
  function __construct($sweet_code='') {
    $this->indent = '  ';
    $this->set_code($sweet_code);
  }

  public function set_code($sweet_code) {
    $this->sweet_code = $sweet_code;
    return $this;
  }

  public function parse() {

    $lines = explode("\n", $this->sweet_code);

    $semicol_lines = $this->add_semicolons($lines);
    $define_lines = $this->add_defines($semicol_lines);

    $lines = $this->without_empty_lines($define_lines);
    $lines[] = "\n";

    $brace_lines = $this->add_braces($lines);

    # remove 3 empty lines in a row
    $lines = split("\n", join("\n\n", split("\n\n\n", join("\n", $brace_lines))));

    $lines = $this->remove_lines_before_elses($lines);

    $outputter = new Outputter($lines);
    return $outputter->get_string();
  }

  private function add_braces($lines) {
    $brace_lines = array();
    $level = 0;
    foreach ($lines as $key => $line) {
      $new_level = $this->indent_level_for($line);

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
    return $this->indent_braces($lines);
  }

  private function indent_braces($lines) {
    $brace_lines = array();
    $level = 0;
    $new_level = $level;
    foreach ($lines as $key => $line) {
      # opening brace means upper lever
      if (preg_match('/\{/', trim($line))) { 
        $new_level += 1;
      } elseif (preg_match('/\}/', trim($line))) {
        $new_level -= 1;
      }

      # Indent
      if ($new_level > $level) {
        $brace_lines[] = str_repeat($this->indent, $level) . $line;

      # Dedent
      } elseif ($new_level < $level) {
        $brace_lines[] = str_repeat($this->indent, $new_level) . $line;
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
    return $brace_lines;
  }

  private function add_defines($lines) {
    $define_lines = array();
    foreach ($lines as $key => $line) {
      if (preg_match('/^([_A-Z][_A-Z]*)\s?=\s?(.*);$/', $line, $matches)) {
        $define_lines[] = 'define("'.$matches[1].'", '.$matches[2].');';
      } else {
        # as is
        $define_lines[] = $line;
      }
    }
    return $define_lines;
  }

  private function add_semicolons($input_lines) {
    $semicol_lines = array();
    foreach ($input_lines as $key => $line) {
      if (
         trim($line) == '' # is empty
      || preg_match('/^(class|while|function|if|else|elseif|public|private|static|abstract)\s?/', trim($line)) # starts with a keyword that precedes something that doesnt end with a semicolon
      || preg_match('/\.$/', trim($line)) # ends with a dot
      || preg_match('/^#/', trim($line)) # starts with a #
      || preg_match('|^//|', trim($line)) # starts with a //
      || preg_match('/->$/', trim($line)) # ends with an arrow
      || (!empty($input_lines[$key+1]) && preg_match('/^->/', trim($input_lines[$key+1]))) # next line starts with an arrow
      ) { 
        # as is
        $semicol_lines[] = $line;

      # put a ';' at the end
      } else {
        $semicol_lines[] = $line . ';';

      }
    }
    return $semicol_lines;
  }


  private function indent_level_for($line) {
    $line_indent = preg_match('/^(\s*)\S/', $line, $matches);
    if ($line_indent) {
      $indent_count = substr_count($line, $this->indent);
      if ($this->indent) {
        return $indent_count;
      } else {
        throw new WrongIndentationKind(
          "The indentation is set to '".$this->indent."', but I found '".$matches[1]."'.");
        return 0;
      }
    } else {
      return 0;
    }
  }

  private function without_empty_lines($lines) {
    $new_lines = array();
    foreach ($lines as $line) {
      if (trim($line) != '') $new_lines[] = $line;
    }
    return $new_lines;
  }

  private function remove_lines_before_elses($lines) {
    $new_lines = array();
    foreach ($lines as $key => $line) {
      if (!empty($lines[$key+1]) && 
      (
        trim($lines[$key+1]) == 'else'
        || preg_match('/^elseif /', $lines[$key+1])
      )) {
        # dont add this line
      } else {
        $new_lines[] = $line;
      }
    }
    return $new_lines;
  }

}
