<?php namespace _\nav;

class url_tree {
    
    public readonly string $intfc;
    public readonly string $is_web;
    public readonly string $panel;
    private bool $add_nav__en;
    
    use \_\i\instance__t; 
    
    public function __construct(string $panel = null, string $intfc = \_\INTFC){
        $this->intfc = $intfc;
        $this->is_web = ($intfc == 'web');
        $this->panel = $panel ?? $_REQUEST->_['panel'];
    }
    
    function normalize_rpath(&$rpth, &$panel, &$path){
        if(($i = \strpos($rpth,'/')) == false){
            $panel = $rpth;
            $path = '';
            $rpth = \str_replace('_','-', $panel);
        } else {
            $panel = \substr($rpth,0,$i);
            $path = \substr($rpth,$i+1);
            $rpth = \str_replace('_','-', $panel).'/'.$path;
        }
    }
    
    function get_url($panel, $path, $suffix = null){
        $portal = \str_replace('_','-', $panel);
        $url =  \_\SITE_URL.(
            ($role = $_REQUEST->_['role'] ?? '')
                ? ($portal == '--' ? "/--.{$role}" : "/{$portal}.{$role}" )
                : ($portal == '--' ? "" : "/{$portal}")
        ).($path ? "/{$path}{$suffix}" : "{$suffix}");
        return $url;
    }
    
    function assemble_flat(string $baseDir): array {
        $pane_prefix = ($this->panel == '*') ?'__' : $this->panel.DIRECTORY_SEPARATOR;
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $baseDir,
                \FilesystemIterator::SKIP_DOTS
                | \FilesystemIterator::CURRENT_AS_FILEINFO
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $flat_menu = [];
        /** @var SplFileInfo $f */
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            $path = $f->getPathname();
            // Normalize once for key building and suffix test
            $rel = substr($path, strlen($baseDir) + 1);
            if(\str_starts_with($rel, $pane_prefix)){
                $rel = str_replace(DIRECTORY_SEPARATOR, '/', $rel);
                if (
                    str_ends_with($rel, "-@{$this->intfc}.php")
                    || str_ends_with($rel, '-@.php') 
                    || ($this->is_web && str_ends_with($rel, '-@.html'))
                ) {
                    $rpth = \trim(\substr($rel, 0, \strrpos($rel, '-@')),'/');
                    $this->normalize_rpath($rpth,$panel,$path);
                    $url = $this->get_url($panel,$path);
                    //$flat_menu[$path] = \_\fob($f);
                    $flat_menu[$rpth] ??= $url; //here urls are not overwritten
                } else if(
                    str_ends_with($rel, "/nav-\${$this->intfc}.php")
                    || str_ends_with($rel, "/nav-\$.php")
                ){
                    $rpth = \dirname($rel);
                    $this->normalize_rpath($rpth,$panel,$path);
                    //$flat_menu[$rpth] = ''; //remove common link
                    foreach((include $f) as $k => $url){
                        $flat_menu[$rpth.$k] = $url; //here urls are overwritten
                    }
                }
            }
        }
        
        return $flat_menu;
    }    
    
    function unflatten($flat_menu) {
        $assoc = [];
        foreach($flat_menu as $k => $v){
            $r =& $assoc;
            $y = array_values(array_filter(explode('/', $k), fn($p) => $p !== ''));;
            $c = \count($y);
            foreach($y as $x){
                if(!(--$c)){
                    $r[$x] = $v;
                } else {
                    if(!isset($r[$x])){ //* converts leafs to nodes if needed
                        $r[$x] = [];
                    } else if(\is_array($r[$x])) {
                        //* do nothing
                    } else {
                        $r[$x] = ['' => $r[$x]];
                    }
                    $r =& $r[$x];
                }
            }
        }
        return $assoc;
    }
    
    function get(){
        $flat_menu = [];
        foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $dir){
            $flat_menu += $this->assemble_flat($dir);
        }
        ksort($flat_menu);
        $tree = $this->unflatten($flat_menu);
        if(count($tree) == 1 && !\is_array($tree[$k = array_key_first($tree)])){
            $tree[\str_replace('_','-', $k)] = ['' => $tree[$k]];
        }
        unset($tree['--auth']);
        return ($this->panel == '*') ? $tree : $tree[\str_replace('_','-', $this->panel)] ?? [];
    }
    
    function render(\closure $node__fn = null, array $options = []){
        return $this->i__render_html($this->get(), $node__fn, $options);
    }
    
    private function i__render_html($arr, \closure $node__fn = null, array $options = []){
        $include_index = true;
        $tree_head = [];
        \extract($options);
        static $recurse = 0;
        try{  $recurse ++; ?>
            <?php if($recurse == 1): ?>
                <style>
                    .xui-url_tree ul.tree a {
                        text-decoration: none;
                    }

                    .xui-url_tree ul.tree {
                        font-family: monospace;
                        --tree-line-color: rgb(200, 100, 100);
                        --tree-dir-color: #aaa;
                        --tree-leaf-color: #fff;
                        padding: 10px;
                    }

                    .xui-url_tree ul.tree,
                    .xui-url_tree ul.tree ul {
                        list-style: none;
                        margin: 0;
                        padding: 0;
                    }

                    .xui-url_tree ul.tree ul {
                        margin-left: 7px;
                    }

                    .xui-url_tree ul.tree li {
                        margin: 0;
                        padding: 0 7px;
                        line-height: 20px;
                        color: var(--tree-dir-color);
                        border-left: 1px solid var(--tree-line-color);

                    }

                    .xui-url_tree ul.tree li:last-child {
                        border-left: none;
                    }

                    .xui-url_tree ul.tree li:before {
                        position: relative;
                        top: -0.3em;
                        height: 1em;
                        width: 6px;
                        color: var(--tree-leaf-color);
                        border-bottom: 1px solid var(--tree-line-color);
                        content: "";
                        display: inline-block;
                        left: -7px;
                    }

                    .xui-url_tree ul.tree li:last-child:before {
                        border-left: 1px solid var(--tree-line-color);
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
                            <li><?=$node__fn ? ($node__fn)($v[''], $k, true) : $k?><?php unset($v['']); $v AND $this->i__render_html($v, $node__fn); ?></li>
                        <?php else:?>
                            <li><?=$node__fn ? ($node__fn)('',$k,false) : $k?><?php $this->i__render_html($v, $node__fn); ?></li>
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