<?php 
namespace Testfuel\DependencyInjection\Fixtures\StaticService;

class StaticService
{
    protected $a = null;

    public function __construct($a)
    {
        $this->a = $a;
    }

    public function getPropertyA()
    {
        return $this->a;
    }
}
