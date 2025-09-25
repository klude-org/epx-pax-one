<?php namespace _;

class env extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    
    protected function __construct(){  }
    public function __get($n){
        return $this[$n];
    }
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, $this->_) ||  $this->offsetGet($n);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed {
        return $_ENV[$n];
    }
    public function jsonSerialize():mixed {
        return $_ENV;
    }
    
}