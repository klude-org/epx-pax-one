<?php namespace _;


final class o extends \stdClass {
    
    use \_\i\singleton__t;
    
    private final function __construct(){ }
    
    public function __get($n){
        return $this->$n = \class_exists($c = static::class.'\\'.$n)
            ? $c::_()
            : null
        ;
    }

}