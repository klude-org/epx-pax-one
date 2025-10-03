<?php namespace _;

class ui extends \stdClass implements \ArrayAccess {
    
    use \_\i\singleton__t;
    private static $_ = [];
    public readonly \_\ui\theme $theme;
    
    protected function __construct(){  
        $this->theme = ($_ENV['UI']['THEME'] ?? \w__theme::class)::_();
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
    
    // public function __get($n){
    //     return $this[$n];
    // }
    
    public function __call($n, $args){
        $expr = $args[0] ?? "";
        if(\class_exists($c = \_\backslashes($this->theme::class."/$expr"))){
            $i = (method_exists($c, '_') ? $c::_() : new $c(array_slice($args,1)));
            if($n == 'i'){
                return $o;
            } else {
                return $this->$n = $i;
            }
        }
    }
    
    public function view(mixed $expr){
        if(\is_string($expr)){
            if($expr === '.'){
                if($f = \_\get_caller()['file'] ?? null){
                    if(\is_file($f = \dirname($f)."/-v.php")){
                        $file = $f;
                    }
                }
                //$file = \stream_resolve_include_path("{$_REQUEST->_['panel']}/-v.php");
            } else if((($expr[1] ?? null) == ':' || ($expr[0] ?? null) == '/')){
                if(\str_ends_with($expr,'-v.php')){
                    $file = \realpath($expr);
                } else {
                    $file = \realpath("{$expr}-v.php")
                        ?: \realpath("{$expr}/-v.php")
                    ;
                }
            } else {
                if(\str_starts_with($expr, '#/')){
                    $expr = \substr($expr,2);
                } else if(\str_starts_with($expr, '#panel/')){
                    $expr = \trim("{$_REQUEST->_['panel']}".\substr($expr,6),'/');
                } else if(\str_starts_with($expr, '#theme/')){
                    $expr = \trim($this->theme::class.\substr($expr,6),'/');
                } else if(\str_starts_with($expr, './')){
                    if($f = \_\get_caller(-1)['file'] ?? null){
                        $expr = \_\f(\dirname($f))->tsp_path(\substr($expr,1));
                    }
                } else {
                    $expr = "{$_REQUEST->_['panel']}/{$expr}";
                }
                $file = \stream_resolve_include_path("{$expr}-v.php")
                    ?: \stream_resolve_include_path("{$expr}/-v.php")
                ;
            }
            if($file){
                return \_\view::_($file);
            } else {
                throw new \Exception("View not found: {$expr}");
            }        
        } else {
            return \_\view::_($expr);
        }
    }
    
    public function file(){
        
    }
}