<?php 
namespace Testfuel\DependencyInjection\Fixtures\FixtureServiceA;

use Exception;

class ServiceA
{
    protected $a = null;
    protected $b = null;
    protected $c = null;


    public function __construct($a, $b, $c)
    {
        if (! is_string($a) || empty($a)) {
            throw new Exception("bad value a");
        }
        $this->a = $a;

        if (! is_bool($b)) {
            throw new Exception("bad value b");
        }
        $this->b = $b;

        if (! is_int($c)) {
            throw new Exception("bad value c");
        }
        $this->c = $c;
    }

    public function getPropertyA()
    {
        return $this->a;
    }
     
    public function getPropertyB()
    {
        return $this->b;
    }

    public function getPropertyC()
    {
        return $this->c;
    }


}
