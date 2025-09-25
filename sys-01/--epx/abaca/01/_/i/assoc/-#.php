<?php namespace _\i;

class assoc {
    
    public function key_by(string $key, array ...$assoc_list){
        $ret = [];
        foreach($assoc_list as $assoc){
            foreach($assoc as $a){
                if(!\is_null($x = $a[$key] ?? null)){
                    $ret[$x] = $a;
                }
            }
        }
        return $ret;
    }
    
    public static function set(array &$a, string $k, mixed $v): void{
        if(\is_null($k)){
            if(\is_array($v)){
                foreach($v as $kk => $vv){
                    $this->val__set($kk, $vv, $a);
                }
            }
        } else if(\str_starts_with($k,'[')){
            $ox =& $a;
            foreach(explode('[',$j = \str_replace(']','', \substr($k,1))) as $kk){
                if(!\is_array($ox[$kk] ?? null)){
                    $ox[$kk] = [];
                }
                $ox =& $ox[$kk];
            }
            $ox = $v;
        } else {
            $a[$k] = $v;
        }
    }    
    
    public static function get(array $a, string $k): mixed{
        if(\str_starts_with($k,'[')){
            $ox = $a;
            foreach(explode('[',$j = \str_replace(']','', \substr($k,1))) as $kk){
                if(!\is_array($ox= $ox[$kk] ?? null)){
                    return $ox;
                }
            }
            return $ox;
        } else {
            return $a[$k] ?? null;
        }
    }
    
    
    
    public static function flatten($assoc, $delimiter = '/', $prepend = '', $append = ''){
        $flatten__fn = function ($assoc, $prefix) use(&$flatten__fn, $delimiter, $prepend, $append){
            if(!\is_array($assoc)){
                yield "{$prefix}{$append}" => $assoc;
            }
            $prefix = ($prefix) ? "{$prefix}{$delimiter}" : $prepend;
            foreach($assoc as $k => $v){
                if(!\is_array($v)){
                    yield "{$prefix}{$k}{$append}" => $v;
                } else {
                    foreach($flatten__fn($v, "{$prefix}{$k}") as $k1 => $v1){
                        yield $k1 => $v1;
                    }
                }
            }
        };
        return iterator_to_array($flatten__fn($assoc, ''));
    }
    
}