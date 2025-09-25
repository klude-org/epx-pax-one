<?php namespace _\i;

trait singleton__t {
    public static function _() { 
        static $i = []; return $I[static::class] ?? ($I[static::class] = new static());
    }
}