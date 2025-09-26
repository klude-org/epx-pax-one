<?php namespace _\i\type;

final class nest {
    
    use \_\i\instance__t;
    
    public readonly \_\i\type $TYPE;
    
    private function __construct(\_\i\type $type){
        $this->TYPE = $type;
    }
    
    public function type($p){
        return \_\i\type::_("{$this->TYPE->NAME}\\{$p}");
    }

    public function type_h($p){
        $class = null; 
        $par = $this;
        for($i = 0; $i <= 10; $i++){
            if($class = $par->type($p)){
                break;
            } else if(!($par = $par->TYPE->PARENT?->nest())){
                break;
            }
        }
        return $class;
    }
    
    public function type_hh($p){
        $class = null;
        $sub = \strtok($p,'/');
        $c = \strtok('');
        $par = $this->TYPE;
        for($i = 0;  $i <= 10; $i++){
            if($xc = \_\i\type::_("{$par->NAME}\\{$sub}")){
                if($c){
                    $par = $xc;
                    $sub = \strtok($c,'/');
                    $c = \strtok('');
                } else {
                    $class = $xc;
                    break;
                }
            } else if(!($par = $par->PARENT)){
                break;
            }
        }
        return $class;
    }
    
    public function file($p){
        return \_\i\file::from_tsp_(\str_replace('\\','/',"{$this->TYPE->NAME}/{$p}"));
    }
    
    public function file_h($p){
        $file = null; 
        $par = $this->TYPE;
        for($i = 0; $i <= 10; $i++){
            if($file = $par->file($p)){
                break;
            } else if(!($par = $par->PARENT) || !$c){
                break;
            }
        }
        return \_\i\file::_($file);
    }    
    
    public function file_hh($p){
        if($file = $this->file($p)){
            return \_\i\file::_($file);
        }
        $sub = \strtok($p,'/');
        $c = \strtok('');
        $par = $this->TYPE;
        for($i = 0;  $i <= 10; $i++){
            if($c){
                if($xc = \_\i\type::_("{$par->NAME}\\{$sub}")){
                    if($file = $par->file($c)){
                        return \_\i\file::_($file);
                    }
                    $par = $xc;
                    $sub = \strtok($c,'/');
                    $c = \strtok('');
                } else if(!($par = $par->PARENT)){
                    break;
                }
            } else {
                break;
            }
        }
    }
}