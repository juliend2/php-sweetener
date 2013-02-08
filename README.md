PHP Sweetener
=============

"PHP Sweetener" is a source-to-source compiler that compiles "Sweet" PHP code into PHP code. 

"Sweet PHP" is a PHP-compatible language that contains no semicolons at end of
lines, uses indentations as block delimiter, and introduce a simplified syntax
for defining constants.

Example "Sweet" PHP program
---

Here is an example of a "Sweet PHP" program:

    class Human
      function __construct($name)
        $this->name = $name
      function get_name()
        return $this->name

    class Julien extends Human
      function __construct()
        parent::__construct("Julien")

      function get_name()
        return "Hello, my name is ".$this->name
    
    $me = new Julien()
    print $me->get_name()

Compiling a Sweet PHP program
---

    php phpsweetener.php ./path/to/prog.phps

