<?php namespace _\nav\url_tree;

class html {
    
    use \_\i\instance__t;
    
    public $tree_head;
    
    public function tree_head($link){
        $this->tree_head = $link;
        return $this;
    }    
    
    public function render($arr, \closure $node__fn = null, array $options = []){
        $include_index = true;
        $tree_head = [];
        \extract($options);
        static $recurse = 0;
        try{  $recurse ++; ?>
            <?php if($recurse == 1): ?>
                <style>
                    ul.tree a {
                        text-decoration:none;
                    }
                    ul.tree {
                        font-family:monospace;
                        --tree-line-color: rgb(200,100,100);
                        --tree-dir-color: #aaa;
                        --tree-leaf-color: #fff;
                        padding: 10px;
                    }
                    ul.tree, 
                    ul.tree ul {
                        list-style: none;
                        margin: 0;
                        padding: 0;
                    } 
                    ul.tree ul {
                        margin-left: 7px;
                    }
                    ul.tree li {
                        margin: 0;
                        padding: 0 7px;
                        line-height: 20px;
                        color: var(--tree-dir-color);
                        font-size: bold;
                        border-left:1px solid var(--tree-line-color);

                    }
                    ul.tree li:last-child {
                        border-left:none;
                    }
                    ul.tree li:before {
                        position:relative;
                        top:-0.3em;
                        height:1em;
                        width:6px;
                        color: var(--tree-leaf-color);
                        border-bottom:1px solid var(--tree-line-color);
                        content:"";
                        display:inline-block;
                        left:-7px;
                    }
                    ul.tree li:last-child:before {
                        border-left:1px solid var(--tree-line-color);   
                    }
                </style>
                <?php if($this->tree_head ?? null): ?>
                    <div class="tree-head">
                        <?=$node__fn ? ($node__fn)($this->tree_head['url'] ?? '', $this->tree_head['name'] ?? '', true) : ''?>
                    </div>
                <?php endif ?>
            <?php endif ?>
            <div class="tree-body">
            <ul class="tree"><?php
            foreach($arr as $k => $v): 
                if(!$k){
                    if(!$include_index){
                        continue;
                    } else {
                        $k = 'home';
                    }
                }
                if(!\_\is_empty($v)): ?>
                    <?php if(is_array($v) OR is_object($v)):  $v = (array) $v; ?>
                        <?php if(isset($v[''])): ?>
                            <li><?=$node__fn ? ($node__fn)($v[''], $k, true) : $k?><?php unset($v['']); $v AND $this->render_html($v, $node__fn); ?></li>
                        <?php else:?>
                            <li><?=$node__fn ? ($node__fn)('',$k,false) : $k?><?php $this->render_html($v, $node__fn); ?></li>
                        <?php endif ?>
                    <?php else: ?>
                        <li><?=$node__fn ? ($node__fn)($v,$k,true) : $k ?></li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach;
            ?>
            </ul>
            </div>
            <?php
        } finally{
            $recurse --;
        }
    }    
}