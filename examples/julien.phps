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
