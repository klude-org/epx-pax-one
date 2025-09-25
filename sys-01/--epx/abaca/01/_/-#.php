<?php

final class _ extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    private static $_;
    
    public array $vars = [];
    public array $trace;
    
    public static function _() { static $I; return $I ?? ($I = new static); }
    
    private function __construct(){  }
    
    public function __get($n){
        static $N =[];  return $N[$k = \strtolower($n)] ?? ($N[$k] = (static::class.'\\'.$k)::_());
    }
    
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, static::$_) ? true : !\is_null($this[$n]);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        if(!\array_key_exists($n, static::$_)){
            $k = "FW_{$n}";
            static::$_[$n] = 
                static::$_['ENV'][$n]
                ?? (\defined($k) ? \constant($k) : null)
                ?? ((($r = \getenv($k)) !== false) ? $r : null)
                ?? $_SERVER[$k]
                ?? $_SERVER["REDIRECT_{$k}"]
                ?? $_SERVER["REDIRECT_REDIRECT_{$k}"]
                ?? null
            ;
        }
        return static::$_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return static::$_;
    } 
    
    public static function file(string|null $n, array|string $suffix = null, $options = []){
        static $t__fn; $t__fn OR $t__fn = function ($p, $k, $t = null, $a = false){
            if($f = \stream_resolve_include_path($GLOBALS['_TRACE']['File Resolve'] = $p.$k)){
                if($t){
                    if($a){
                        return [ \_\i\file::_($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f)), $t, $p];
                    } else {
                        return \_\i\file::_($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                    }
                } else {
                    return \_\i\file::_($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                }
            }
        };
        
        if($p = \str_replace('\\','/', $n)){
            if(
                (($p[0]??'')=='/' || ($p[1]??'')==':') 
                || \str_starts_with($p,'./')
            ){
                if($f = \realpath($GLOBALS['_TRACE']['Realpath Resolve'] = $p)){
                    return \_\f($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                }
                
            } else {
                if(\is_null($suffix)){
                    if($r = ($t__fn)($p, '')){
                        return $r;
                    }
                } else if(\is_array($suffix)) {
                    foreach($suffix as $k => $t){
                        if($r = ($t__fn)($p, ($m = !\is_numeric($k)) ? $k : $t , $t, $m)){
                            return $r;
                        }
                    }
                } else if(\is_string($suffix)) {
                    if(\str_contains($suffix,'|')){
                        foreach(explode('|', $suffix) as $t){
                            if($r = ($t__fn)($p, $t)){
                                return $r;
                            }
                        }
                    } else {
                        if(
                            ($r = ($t__fn)($p, $suffix))
                            || ($r = ($t__fn)($p, "/{$suffix}"))
                        ){
                            return $r;
                        }
                    }
                }
            }
        }
    }
    
    public static function glob($p, $flags = 0){
        if(\is_string($p)){
            /* using gxp would not work on files */
            $p = \_\p($p);
            $list = [];
            foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
                foreach(\glob("{$d}/{$p}", $flags) as $f){
                    $list[] = \_\i\file::_($f);
                }
            }
            return $list;
        } else {
            return [];
        }
    }
    
}