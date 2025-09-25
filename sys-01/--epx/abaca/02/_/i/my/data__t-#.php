<?php namespace _\i\my;

trait data__t {
    
    public function my_data($path){
        return o()->data[static::class."/{$path}"];
    }
    
    
}