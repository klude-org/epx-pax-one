<?php namespace _\i\extensible\a;

trait getter__t {
    
    private $I__XT = [];

    public function __get($n){
        return ($this->I__XT[$k = \strtolower($n)] 
            ?? ($this->I__XT[$k] = $this->i__xt_resolve_hh($k) ?? false)
        ) ?: null;
    }
    
    public function i__xt_resolve_hh($k){
        $class = static::class;
        do{
            if(\class_exists($c = "{$class}\\{$k}")){
                return method_exists($c,'_')
                    ? $c::_($this)
                    : new $c($this)
                ;
            }
        } while($class = \get_parent_class($class));
    }
    
}