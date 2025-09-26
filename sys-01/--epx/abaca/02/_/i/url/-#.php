<?php namespace _\i;

final class url {
    
    private string $urp;
    
    private ?array $param = null;
    
    use \_\i\instance__t;
    
    public static function invalid_url_($path){
        return static::_(o()->site_url."/invalid-url?".\http_build_query(['path' => $path]));
    }
    
    private function __construct(string $urp){
        $this->urp = $urp;
    }
    
    private function set(array $param){
        $this->param = $param + ($this->param ?? []);
        return $this;
    }
    
    public function put(){
        echo $this;
    }
    
    public function __toString(){
        return $this->urp.(!empty($this->param) ? '?'.\http_build_query($this->param) : '');
    }
    
}