<?php namespace _;

class data extends \stdClass implements \ArrayAccess {
    
    use \_\i\singleton__t;    

    public function __construct(){ }
    
    public function offsetSet($n, $v):void { 
        \is_dir($d = \dirname($f = $this->data_file_path($n))) or \mkdir($d, 0777, true);
        \file_put_contents($f, "<?php return ".\var_export($v,true).";");
    }

    public function offsetExists($n):bool { 
        return \is_file($f = $this->data_file_path($n))
            || ($f = $this->resolve_tsp_path($n))
        ;
    }

    public function offsetUnset($n):void { 
        if(\is_file($f = $this->data_file_path($n))){
            unlink($f);
        }
    }

    public function offsetGet($n):mixed { 
        if(
            \is_file($f = $this->data_file_path($n))
            || ($f = $this->resolve_tsp_path($n))
        ){
            return include $f ?: [];
        } else {
            return null;
        }
    }
    
    public function data_file_path($n){
        return \_\DATA_DIR."/".\str_replace('\\','/', $n)."-$.php";
    }
    
    public function resolve_tsp_path($n){
        return \stream_resolve_include_path(\str_replace('\\','/', $n)."-$.php");
    }
    
}