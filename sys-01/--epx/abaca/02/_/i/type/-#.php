<?php namespace _\i;

final class type implements \JsonSerializable {
    
    private static $I = [];
    
    public static function _(string|object $name){
        if($name instanceof \_\i\type){
            return $name;
        } else {
            $n = \str_replace('/','\\',\strtolower(
                (\is_object($name)
                    ? $name::class
                    : $name
                )
            ));
            return static::$I[$n] ?? (static::$I[$n] = \class_exists($n)
                ? new static($n)
                : null
            );
        }
    }

    public readonly string $NAME;
    
    public readonly ?\_\i\type $PARENT;
    
    protected \ReflectionClass $reflection;
    
    protected \_\i\type\nest $nest;
    
    public function __construct($name){
        $this->NAME = $name;
        if($p = \get_parent_class($this->NAME)){
            $this->PARENT = static::_($p);
        } else {
            $this->PARENT = null;
        }
    }
    
    public function jsonSerialize():mixed {
        return $this->NAME;
    }
    
    public function __toString(){
        return $this->NAME;
    }
    
    public function is_a(string $type){
        return \is_a($this->NAME, \_\i\node\__i::class, true);
    }
    
    public function has_method(string $method){
        return \method_exists($this->NAME, $method);
    }
    
    public function instantiate(...$args){
        if(\method_exists($this->NAME, '_')){
            return $this->NAME::_(...$args);
        } else {
            return new $this->NAME(...$args);
        }
    }

    public function instantiate_alt(\closure $init__fn, ...$args){
        if(\method_exists($this->NAME, '_')){
            $o = $this->NAME::_(...$args);
        } else {
            $o = new $this->NAME(...$args);
        }
        ($init__fn)($o);
        return $o;
    }
    
    public function nest(){
        return $this->nest ?? ($this->nest = \_\i\type\nest::_($this));
    }
 
    public function reflection(){
        return $this->reflection ?? ($this->reflection = new \ReflectionClass($this->NAME));
    }
    

    public static function is_loaded($n){
        return \class_exists($n, false);
    }
    
    public static function exists($n){
        return (static::$I[$n] ?? static::_($n)) ? true : false;
    }
    
}