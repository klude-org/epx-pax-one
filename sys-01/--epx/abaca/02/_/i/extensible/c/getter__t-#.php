<?php namespace _\i\extensible\c;

trait getter__t {
    
    private $I__XT = [];
    public function __get($n){
        return ($this->I__XT[$k = \strtolower($n)] 
            ?? ($this->I__XT[$k] = \_\i\type::_(static::class)->nest()->type_h('_\\'.$k)?->instantiate($this) ?? false)
        ) ?: null;
    }
    
}