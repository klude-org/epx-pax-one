<?php namespace _;

class view {
    
    static $WRAPPER_COUNT = 0;
    static $PLICS = [];
    private static $VARS = [];
    
    public readonly mixed $OBJ;
    public readonly \Closure $FN;
    public ?view $wrapper = null;
    public ?view $inner = null;
    public string $inset = '';
    public array $params = [];

    public static function _($expr = null){ 
        return new static($expr, false);
    }
    
    public static function file_(string|null $expr = ''){ 
        $file = null;
        if($expr === ''){
            if($f = \_\get_caller(-1)['file'] ?? null){
                if(\is_file($f = \dirname($f)."/-v.php")){
                    $file = $f;
                }
            }
            //$file = \stream_resolve_include_path("{$_REQUEST->_['panel']}/-v.php");
        } else if((($expr[1] ?? null) == ':' || ($expr[0] ?? null) == '/')){
            $file = \realpath($expr,2);
        } else {
            if(\str_starts_with($expr, '#/')){
                $expr = \substr($expr,2);
            } else if(\str_starts_with($expr, '#')){
                $expr = \trim("{$_REQUEST->_['panel']}/".\substr($expr,1),'/');
            } else {
                $expr = "{$_REQUEST->_['panel']}/{$expr}";
            }
            $file = \stream_resolve_include_path("{$expr}-v.php")
                ?: \stream_resolve_include_path("{$expr}/-v.php")
            ;
        }
        if($file){
            return new static($file, true);
        } else {
            throw new \Exception("View not found: {$expr}");
        }
    }
    
    private function __construct($obj, $byfile){
        $this->OBJ = $obj;
        $this->FN = ($byfile)
            ? function(){
                $this->params AND \extract($this->params, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
                $__INSET__ = $this->inset;
                include $this->OBJ;
            }
            : function(){
                $expr = $this->OBJ;
                if(!$expr && $expr != 0){
                    echo '';
                } else if(\is_scalar($expr)){
                    if(\str_starts_with($expr, '#/')){
                        static::file_($expr)();
                    } else {
                        echo $expr;
                    }
                } else if(\is_callable($expr)){
                    ($expr)();
                } else if($expr instanceof \SplFileInfo) {
                    static::file_($expr)();
                } else if(\is_array($expr)){
                    echo '<pre>'.\json_encode(
                        $expr, 
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    ).'<pre>';
                }
            }
        ;
        
    }
    
    public function params(array $params){
        $this->params[] = \array_replace($this->params, $params);
        return $this;
    }

    
    public function __invoke($return = false){
        $output = '';
        try{
            \ob_start(function($view_text) use(&$output){
                $output .= $view_text;
            });
            ($this->FN)();
            \ob_clean();
            if($this->wrapper){
                try{
                    $this->wrapper->inner = $this;
                    $this->wrapper->inset = $output;
                    $output = null;
                    ($this->wrapper)();
                } finally {
                    static::$WRAPPER_COUNT --;
                }
            }
        } finally {
            \ob_end_clean();
        }
        if($return) {
            return $output;
        } else {
            echo $output;
        }
    } 

    public function wrap_in($expr, array $params = []){
        if($this->wrapper){
            throw new \Exception("Duplicate Wrapper Settings");
        }
        if(static::$WRAPPER_COUNT > 10){
            throw new \Exception("View stack overflow");
        }
        static::$WRAPPER_COUNT++;
        $this->params = $params;
        $this->wrapper = static::file_($expr);
        return $this;
    }
    
    public function head($x){ static::plic('head', $x); return $this; }
    public function tail($x){ static::plic('tail', $x); return $this; }
    public function style($x){ static::plic('style', $x); return $this; }
    public function script($x){ static::plic('script', $x); return $this; }
    
    public static function once($key){
        static $keys = [];
        if($x instanceof \closure){
            $r = new \ReflectionFunction($x);
            $key = "{$r->getStartLine()}:{$r->getFileName()}";
        } else if(is_scalar($x)){
            $key = md5($x);
        } else if($x instanceof \SplFileInfo){
            $key = (string) $x;
        } else{
            throw new \Exception('Invalid Parameter for Style/Script');
        }
        if($keys[$key] ?? false){
            return false;
        } else {
            $keys[$key] = true;
            return true;
        }
    }
    
    public function style_once($x){ if(static::once($x)){ static::plic('style', $x); } return $this; }
    public function script_once($x){ if(static::once($x)){ static::plic('script', $x); } return $this; }
    
    public function plugin($expr, $attribs = []){
        $type = '';
        $url = '';
        if(!$expr){
            throw new \Exception("Invalid Argument: empty value for the \$expr");
        }
        if(\is_string($expr)){
            if($expr[0] == '@'){
                $region = \strtok($expr, '/');
                $path = \strtok('');
                throw new \Exception("Plugin resolve by anchor expressions are not supported");
            } else if(
                \str_starts_with($expr,'-asset/')
                || \str_ends_with(pathinfo($expr,PATHINFO_FILENAME),'-asset')
            ){
                $url = o()->ui->lib_url.'/'.$expr;
                $type = \pathinfo($url, PATHINFO_EXTENSION);
                if(\is_file($f = \_\LIB_DIR.'/'.$expr)){
                    $url .= '?i='.(filemtime($f));
                }
            } else if(\str_starts_with($expr,'/')){
                throw new \Exception("Plugin resolve by file is not Implemented");
            } else if(\str_starts_with($expr,'https://')){
                $url = $expr;
                $type = \pathinfo($url, PATHINFO_EXTENSION);
            } else if(\str_starts_with($expr,'http://')){
                $url = $expr;
                $type = \pathinfo($url, PATHINFO_EXTENSION);
            } else {
                throw new \Exception("Invalid plugin expression");
            }
        } else if($expr instanceof \_\ui\web\plugin_pack\__i) {
            throw new \Exception("Plugin pack is not Implemented");
        } else if($expr instanceof \SplFileInfo) {
            throw new \Exception("Plugin resolve by file is not Implemented");
        } else if(\is_array($expr)){
            throw new \Exception("Plugin array is not implemented");
        }
        
        $a = '';
        if($attribs){
            foreach($attribs as $k => $v){
                if($v){
                    $a.=' '.$k.'="'.\htmlspecialchars($v).'"';
                }
            }
        }        
        
        if($url){
            if($type){
                switch($type){
                    case 'js':
                    case 'text/javascript':
                    case 'script': {
                        static::plic(
                            ($is_pre ?? false) ? 'head_plugins' : 'tail_plugins',
                            "\t<script src=\"{$url}\" onerror=\"xui?.debug?.plugin_load_error('script',this,event)\"{$a}></script>\n"
                        );
                    } break;
                    case 'css':
                    case 'text/css':
                    case 'style': {
                        static::plic(
                            'head_plugins',
                            "\t<link href=\"{$url}\" rel=\"stylesheet\" onerror=\"xui?.debug?.plugin_load_error('style',this,event)\"{$a}>\n"
                        );
                    } break;
                    case 'ico':
                    case 'icon':{
                        static::plic(
                            'head',
                            "\t<link href=\"{$url}\" rel=\"icon\" onerror=\"xui?.debug?.plugin_load_error('icon',this,event)\"{$a}>\n"
                        );
                    } break;
                    default: 
                    static::plic(
                        'head_plugins',
                            "\t<link href=\"{$url}\" onerror=\"xui?.debug?.plugin_load_error('style',this,event)\"{$a}>\n"
                        );
                    break;
                }
            } else {
                static::plic(
                    'head_plugins', 
                    "\t<link href=\"{$url}\" onerror=\"xui?.debug?.plugin_load_error('style',this,event)\"{$a}>\n"
                );
            }
        } else {
            //\_\dx::report(__METHOD__.": Unable to locate plugin '{$expr}'");
            throw new \Exception("Unable to locate plugin '{$expr}'");
        }
    }
    
    public static function plic($k, $v = null){
        if(func_num_args() > 1){
            //* plic processes information immediately
            isset(static::$PLICS[$k]) OR static::$PLICS[$k] = '';
            static::$PLICS[$k] .= (\is_string($v)) ? $v : \_\texate($v);
        } else {
            return static::$PLICS[$k] ?? '';
        }
    }
    
    public static function var($n, $default = null){
        if(func_num_args() > 1){
            if(\is_scalar($n)){
                return static::$VARS[$n] ?? $default;
            } else if(\is_array($n) || \is_object($n)){
                foreach($n as $k => $v){
                    static::$VARS[$k] = $v;
                }
            } else {
                throw new \Exception("Invalid var parameter \$n");
            }
        } else {
            return static::$VARS[$n] ?? null;
        }
    }
    
    public static function on_action(string $action, callable $f){
        if($action = $_REQUEST['--action'] ?? null){
            ($f)();
            exit;
        }
    }
   
    public static function on_view($type, callable $f){
        ($f)();
    }
    
    public static function respond_json($response){
        while(ob_get_level() > \_\OB_OUT){ @ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    
    public static function render(mixed $expr, bool|array $params = [], bool $texate = false){
        if(\is_bool($params)){
            $texate = $params;
            $params = [];
        }
        if(!$expr && $expr != 0){
            if($texate){
                return '';
            } else {
                echo '';
                return;
            }
        } else if(\is_scalar($expr)){
            if($texate){
                return $expr;
            } else {
                echo $expr;
                return;
            }
        }
        
        try{ 
            $texate AND \ob_start();
            if(\is_array($expr) && ($params[0] ?? null) === true){
                foreach($expr as $v){
                    if(\is_scalar($v)){
                        echo $v;
                    }
                }
            } else if($expr instanceof \closure) {
                \is_array($params) ? ($expr)(...$params) : ($expr)();
            } else if($expr instanceof \SplFileInfo) {
                include $expr;
            } else if($expr instanceof \_\i\prt__i){
                \is_array($params) ? $expr->prt(...$params) : ($expr)();
            } else {
                echo '<pre>'.\json_encode($expr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'<pre>';
            }
        } finally { 
            if($texate){
                $d = \ob_get_contents(); \ob_end_clean();  
            }  
        }
        if($texate){
            return $d; //* if returned in finally exceptions get lost
        }
    }

    public static function texate(mixed $expr, array $params = []){
        return static::render($expr, $params, true);
    }
    
    public static function prt(){ ?>
        
    <?php }
    
    
}