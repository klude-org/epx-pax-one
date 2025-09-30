<?php

final class _ extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    private static $_;
    
    public array $vars = [];
    public array $trace;
    public object $fn;
    
    public static function _() { static $I; return $I ?? ($I = new static); }
    
    private function __construct(){ 
        $this->fn = new \stdClass; 
    }
    
    public function __get($n){
        static $N =[];  return $N[$k = \strtolower($n)] ?? ($N[$k] = (static::class.'\\'.$k)::_());
    }
    
    public function __call($m,$args){
        return ($this->fn->$m)(...$args);
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
    
    public static function vars(string|array $n = null, mixed $args = null){
        static $V = [];
        if(($count = \func_num_args()) > 1){
            if(\is_scalar($n)){
                $V[$n] = $args;
            } else {
                throw new \Exception('Invalid Key Type');
            }
        } else if($count) {
            if(\is_scalar($n)){
                if($v = $V[$n] ?? null){
                    if(\is_callable($v)){
                        return ($v)();
                    } else {
                        return $v;
                    }
                }
            } else if(\is_array($n) || \is_object($n)){
                foreach($n as $k => $v){
                    static::vars($k, $v);
                }
            } else {
                throw new \Exception("Invalid var parameter \$n");
            }
            
        } else {
            return $V;
        }
    }    
    
    public static function view($expr = null){
        static $I; 
        return \func_num_args()
            ? \_\view::_($expr)
            : ($I ?? ($I = \_\view::_()))
        ;
    }
    
    public static function db(string $expr = null){
        static $I = []; 
        return \func_num_args()
            ? ($I[$expr ?? ''] ?? ($I[$expr ?? ''] = \_\db::_($expr)))
            : ($I[''] ?? ($I[''] = \_\db::_()))
        ;
    }
    
    public static function respond_json($response){
        while(ob_get_level() > \_\OB_OUT){ @ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }    

}