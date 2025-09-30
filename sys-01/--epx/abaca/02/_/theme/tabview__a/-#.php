<?php namespace _\theme;

class tabview__a {
    
    private $search_prefix = null;
    private $list = [];
    private $i__html_id__panel;
    private $i__html_id__navs;
    
    public function html_id__panel($id = null){
        if(func_num_args()){
            $this->i__html_id__panel = $id;
            return $this;
        } else {
            return $this->i__html_id__panel ?? $this->i__html_id__panel = \uniqid('id');
        }
    }
    
    public function html_id__navs($id = null){
        if(func_num_args()){
            $this->i__html_id__navs = $id;
            return $this;
        } else {
            return $this->i__html_id__navs ?? $this->i__html_id__navs = \uniqid('id');
        }
    }
    
    
    public function base($d){
        $this->search_prefix = $d;
        return $this;
    }
    
    public function list(callable $fn){
        ($fn)($this->list);
        return $this;
    }
    
    public function prt(){
        $this->prt_navs();
        $this->prt_panel();
        return $this;
    }
    
    public function tab_file($k){
        return "{$this->search_prefix}/tab__{$k}-v.php";
    }
    
    public function prt_navs(){
        include 'navs-v.php';
        return $this;
    }
    
    public function prt_panel(){
        include 'panel-v.php';
        return $this;
    }
    
}