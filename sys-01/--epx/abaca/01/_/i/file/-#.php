<?php namespace _\i;

class file extends \SplFileInfo implements \JsonSerializable, \ArrayAccess {
    
    public readonly array $info;
    public array $meta = [];
    public static function _(string $path, array $info = []) { return new static($path, $info); }
    public function __construct(string $path, array $info = []){
        //! WARNING
        //* don't use resolver methodology here
        //* we need it for existing & non existing files
        parent::__construct($path);
        $this->meta = $info;
    }
    public function __get($n){
        if(\class_exists($c = static::class."\\{$n}")){
            return $c::_($this);
        }
    }
    public function offsetSet($n, $v):void { $this->meta[$n] = $v; }
    public function offsetExists($n):bool { return isset($this->meta[$n]) || isset($this->meta[$n]); }
    public function offsetUnset($n):void { unset($this->meta[$n]); }
    public function offsetGet($n):mixed { return $this->meta[$n] ?? $this->meta[$n] ?? null; }
    
    public function jsonSerialize(): mixed { return "file://".\str_replace('\\','/', $this->getRealPath()); }    
    public function exists(){ return \file_exists($this); }
    public function path(){ return \str_replace('\\','/', $this); }
    public function pathinfo(){  return \pathinfo($this); }
    public function realpath(){ return \str_replace('\\','/', $this->getRealPath()); }
    public function extension(){ return $this->getExtension(); }
    public function dirname(){ return $this->getDirectory(); }
    public function filename(){ return $this->getFilename(); }
    public function fpfx(){ return ($j = $this->pathinfo())['dirname'].'/'.$j['filename']; }
    public function x__name(){ return \is_uploaded_file($f) ? $this->meta['name'] : $this->getBasename(); }
    public function x__fpfx(){ return \filename($this->x__name()); }
    public function x__extension(){ return \pathinfo($this->x__name(), PATHINFO_EXTENSION); }
    public function ensure_dir(){
        \is_dir($d = \dirname($this)) OR \mkdir($d, 0777, true);
        return $this;
    }
    public function move_to($path):\_\f|bool{
        if(\is_uploaded_file($this)){
            \is_dir($d = \dirname($path)) OR \mkdir($d,0777,true);
            if(\move_uploaded_file($this, $path)){
                return static::_($path);
            }
        } else {
            \is_dir($d = \dirname($path)) OR \mkdir($d,0777,true);
            if(\rename($this['path'], $path)){
                return static::_($path);
            }
        }
        return false;
    }
    public function move_to_data($path):\_\f|bool{
        if(\str_ends_with($path,'.*')){
            if(\is_uploaded_file($this)){
                $extn = \pathinfo($this->meta['name'], PATHINFO_EXTENSION);
            } else {
                $extn = \pathinfo($this, PATHINFO_EXTENSION);
            }
            foreach(\glob(\_\DATA_DIR."/{$path}") as $f){
                \unlink($f);
            }
            $path = \trim($path,'.*');
            $target = \_\DATA_DIR."/{$path}.{$extn}";
            return $this->move_to($target);
        } else {
            $target = \_\DATA_DIR."/{$path}";
            return $this->move_to($target);
        }
    }
    public function copy_to($path):\_\f|bool{
        if(\copy($this['path'], $path)){
            return static::_($path);
        }
        return false;
    }
    public function delete():bool{
        if($this->isFile()){
            \unlink((string) $this);
            return true;
        } else if($this->isDir()) {
            foreach (
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator((string) $this, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                ) as $x
            ){
                $x->isDir() 
                    ? \rmdir($x->getRealPath()) 
                    : \unlink($x->getRealPath())
                ;
            }
            \rmdir($f);
            return true;
        } else if($this->isLink()) {
            //* not for now
            return false;
        }
        return false;
    }
    public function contents(string $contents, $append = false):string|null {
    	if($p = $this->getRealPath()){
            if(func_num_args()){
                \is_dir($d = \dirname($this->path())) OR \mkdir($d,0777,true);
                \file_put_contents($this, $contents);
                return null;
            } else {
                return $this->exists() ? \file_get_contents($this->path()) : null;
            }
        }
    }
    public function include(array $vars = [], object $context = null){
        $context ??= \_::_();
        return (function($f, $vars){
            \extract($vars);
            return include $f;
        })->bindTo($context,$context::class)($this->getRealPath(), $vars);
    }
    public function url(){
        $p = \str_replace('\\','/', $this->getRealPath());
        if(\str_starts_with($p, \_\ROOT_DIR)){
            return \_\i\url::_(o()->root_url.'/'.\substr($p, \strlen(\_\ROOT_DIR) + 1));
        }
    }
    public function tsp_path(){
        $p = $this->path();
        foreach(\explode(PATH_SEPARATOR,\get_include_path()) ?? [] as $tsp){
            if(\str_starts_with($p, $tsp)){
                $px = \substr($p, \strlen($tsp) + 1);
                return $url;
            }
        }        
    }
    
}