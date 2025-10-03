<?php namespace _;

class val extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    private static $_ = [];
    
    protected function __construct(){  }
    public function __get($n){
        return $this[$n];
    }
    public function offsetSet($n, $v):void { 
        if(\is_null($n)){
            static::$_[] = $v;
        } else {
            static::$_[$n] = $v;
        }
    }
    public function offsetExists($n):bool { 
        return isset(static::$_[$n]);
    }
    public function offsetUnset($n):void { 
        unset(static::$_[$n]);
    }
    public function &offsetGet($n):mixed {
        return static::$_[$n];
    }
    public function jsonSerialize():mixed {
        return ['members' => (array) $this, 'assoc' => static::$_];
    }
    
}