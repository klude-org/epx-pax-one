<?php namespace _\i;

class path {
    
    public static function normalize(string $expr, int $levels = 0){
        return \str_replace('\\','/', $levels ? \dirname($expr , $levels) : $expr);
    }
    
    public static function is_rooted($expr){
        return ($expr[0]??'')=='/' || ($expr[1]??'')==':';
    }
    
    public static function abs($rel){
        $path = \_\slashes($rel);
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n = 1; $n > 0; $path = preg_replace($re, '/', $path, -1, $n)) {}
    
        return \_\slashes($path);
    }
    
    public static function relative($to, $from = \_\BASE_DIR){
        //credits: https://stackoverflow.com/a/51874346
        $separator = DIRECTORY_SEPARATOR;
        $from   = \str_replace(['/', '\\'], $separator, $from);
        $to     = \str_replace(['/', '\\'], $separator, $to);
    
        $arFrom = \explode($separator, \rtrim($from, $separator));
        $arTo = \explode($separator, \rtrim($to, $separator));
        while(\count($arFrom) && \count($arTo) && ($arFrom[0] == $arTo[0]))
        {
            \array_shift($arFrom);
            \array_shift($arTo);
        }
    
        return \str_pad("", \count($arFrom) * 3, '..'.$separator).\implode($separator, $arTo);
    }
    
    
    public static function trim_extension($file){
        if($file && ($y = \pathinfo($file))){
            return ($y['dirname'] == '.') ? $y['filename'] : $y['dirname'].'/'.$y['filename'];
        }
    }
    
    public static function swap_extension($file, $extn){
        if($file && ($y = \pathinfo($file))){
            return $y['dirname'].'/'.$y['filename'].'.'.$extn;
        }
    }
    
    public static function fpn($file){
        //* FNP stands for FILE NAME PATH meaning full path minus the extension;
        
        //* another entropy - PATHINFO_FILENAME is different than $this->getFileName();
        $pi = pathinfo($file);
        return ($file instanceof \SplFileInfo) 
            ? new \SplFileInfo("{$pi['dirname']}/{$pi['filename']}")
            : "{$pi['dirname']}/{$pi['filename']}"
        ;
    }
    
    public static function extension($path){
        if(is_string($path)){
            return pathinfo($path, PATHINFO_EXTENSION);
        } else if($path instanceof \SplFileInfo){
            return $path->getExtension();
        }
    }
    
    public static function filename($path){
        return pathinfo($path, PATHINFO_FILENAME);
    }
    
    public static function sys_slashes($p){
        return str_replace(['\\','/'],[DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR],$p);
    }
    
    public static function slashes($path){ 
        return str_replace('\\','/',$path); 
    }
    
    public static function backslashes($path){ 
        return str_replace('/','\\', $path); 
    }
    
    public static function fnmatch($pattern, $string, $flags){
        return fnmatch($pattern, $string, $flags);
    }
    
    
}