<?php namespace _\i;

trait solo__t {
    public static function _($name, ...$args) { 
        static $A = [];
        return $A[$name] ?? (function(&$a, $name, ...$args){
            $a[$name] = $i = new static($name, ...$args);
            $i->i__initialize($name,...$args);
            return $i;
        })($A, $name, ...$args);
    }
    private function __construct($name,...$args){ 
        $GLOBALS['_TRACE'][] = "Solo: {$name}: ".static::class; 
        $this->i__construct();
    }
    protected function i__construct(){ }
    protected function i__initialize(){ }
}