<?php namespace _\i;

trait instance__t {
    public final static function _(...$args) { ($i = new static(...$args))->i__construct(...$args); return $i; }
    private function __construct(){ $GLOBALS['_TRACE'][] = 'Instance: '.static::class; }
    protected function i__construct(){ }
}