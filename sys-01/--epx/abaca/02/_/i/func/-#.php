<?php namespace _\i;

class func {
    
    private static $I = [];
    
    public static function _(string|\_\i\func $name){
        if($name instanceof \_\i\func){
            return $name;
        } else {
            $n = \str_replace('/','\\',\strtolower($name));
            return static::$I[$n] ?? (static::$I[$n] = \function_exists($n)
                ? new static($n)
                : null
            );
        }
    }
    
    public readonly string $NAME;
    
    protected \ReflectionFunction $reflection;
    
    public function __construct($name){
        $this->NAME = $name;
    }
    
    public function jsonSerialize():mixed {
        return "function:".$this->NAME;
    }
    
    public function reflection(){
        return $this->reflection ?? ($this->reflection = new \ReflectionClass($this->NAME));
    }
    
    public function __toString(){
        return $this->NAME;
    }
    
    public function argTypes(): ?array {
        
        $reflection = $this->reflection();
        $parameters = $reflection->getParameters();
        $argTypes = [];
    
        foreach ($parameters as $param) {
            $type = $param->getType();
            if ($type instanceof \ReflectionNamedType) {
                $argTypes[] = $type->getName();
            } else {
                // If no type or complex types are used (e.g., union types), add null
                $argTypes[] = null;
            }
        }
    
        return $argTypes;
    }

}