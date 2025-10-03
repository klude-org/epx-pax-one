<?php namespace _;

class view {
    
    private static $WRAPPER_COUNT = 0;
    private static $PLICS = [];
    private static $VIEW_STACK = [];
    private static $VARS = [];
    
    private ?view $WRAPPER = null;
    private ?view $INNER = null;
    private string $INSET = '';
    private array $PARAMS = [];
    private readonly \Closure $FN;
    
    public object $o;

    public static function _($expr = null){ 
        static $I; 
        if(!\func_num_args()){
            return $I ?? ($I = new static());
        } else if($expr instanceof \SplFileInfo){
            return new static($expr);
        } else if(\is_string($expr)){
            if($expr === '.'){
                if($f = \_\get_caller()['file'] ?? null){
                    if(\is_file($f = \dirname($f)."/-v.php")){
                        $file = $f;
                    }
                }
                //$file = \stream_resolve_include_path("{$_REQUEST->_['panel']}/-v.php");
            } else if((($expr[1] ?? null) == ':' || ($expr[0] ?? null) == '/')){
                if(\str_ends_with($expr,'-v.php')){
                    $file = \realpath($expr);
                } else {
                    $file = \realpath("{$expr}-v.php")
                        ?: \realpath("{$expr}/-v.php")
                    ;
                }
            } else {
                if(\str_starts_with($expr, '#/')){
                    $expr = \substr($expr,2);
                } else if(\str_starts_with($expr, '#panel/')){
                    $expr = \trim("{$_REQUEST->_['panel']}".\substr($expr,6),'/');
                } else if(\str_starts_with($expr, './')){
                    if($f = \_\get_caller(-1)['file'] ?? null){
                        $expr = \_\f(\dirname($f))->tsp_path(\substr($expr,1));
                    }
                } else {
                    $expr = "{$_REQUEST->_['panel']}/{$expr}";
                }
                $file = \stream_resolve_include_path("{$expr}-v.php")
                    ?: \stream_resolve_include_path("{$expr}/-v.php")
                ;
            }
            if($file){
                return new static(new \SplFileInfo($file));
            } else {
                throw new \Exception("View not found: {$expr}");
            }            
        } else if(\is_scalar($expr)){
            return new static(function() use($expr){
                echo $expr;
            });
        } else if(\is_callable($expr)){
            return new static($expr);
        } else if(\is_array($expr)){
            return new static(function() use($expr){
                echo '<pre>'.\json_encode(
                    $expr, 
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                ).'<pre>';
            });
        } else {
            throw new \Exception("Invalid view expression");
        }
    }
    
    public static function text_(string $text){
        return new static(function() use($expr){
            echo $expr;
        });
    }
    
    private function __construct(callable|\SplFileInfo $fn = null){
        //$this->o = \func_num_args() ? static::_()->o : new \stdClass();
        if($fn instanceof \SplFileInfo){
            $__FILE__ = $fn;
            $this->FN = (function($__INSET__,$__VIEW__) use($__FILE__){
                \extract($__VIEW__->params(), EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
                include $__FILE__;
            })->bindTo(\_::_(),\_::class);
        } else {
            $this->FN = $fn ?? fn() => null;
        }
    }
    
    public function __invoke($return = false){
        return $this->prt($return);
    }

    public function params(array $params = null){
        if(\func_num_args()){
            $this->PARAMS[] = \array_replace($this->PARAMS, $params);
            return $this;
        } else {
            return $this->PARAMS;
        }
    }
    
    public function prt($return = false){  
        $output = '';
        try{
            \ob_start(function($view_text) use(&$output){
                $output .= $view_text;
            });
            try{
                \array_push(static::$VIEW_STACK, $this);
                ($this->FN)($this->INSET, $this);
            } finally {
                \array_pop(static::$VIEW_STACK);
            }
            \ob_clean();
            if($this->WRAPPER){
                try{
                    $this->WRAPPER->INNER = $this;
                    $this->WRAPPER->INSET = $output;
                    $output = null;
                    $this->WRAPPER->prt();
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
    
    public static function current_view(){
        return static::$VIEW_STACK[0] ?? null;
    }
    
    public static function wrap_in(string $expr, array $params = []){
        if(!static::$VIEW_STACK){
            throw new \Exception("Cannot use wrapper outside a view structure");
        }
        if(static::$VIEW_STACK[0]->WRAPPER){
            throw new \Exception("Duplicate Wrapper Settings");
        }
        if(static::$WRAPPER_COUNT > 10){
            throw new \Exception("View stack overflow");
        }
        static::$WRAPPER_COUNT++;
        static::$VIEW_STACK[0]->PARAMS = $params;
        return static::$VIEW_STACK[0]->WRAPPER = static::_($expr);
    }
    
    public static function feature(string $feature, &$store = null){
        return $store = \_\theme::_()->$feature;
    }
    
    public static function head($x){ 
        static::plic('head', $x);
    }
    
    public static function tail($x){ 
        static::plic('tail', $x); 
    }
    
    public static function style($x, $once = false){ 
        if($once){
            if(static::once($x)){ static::plic('style', $x); }
        } else {
            static::plic('style', $x);
        }
    }
    
    public static function script($x, $once = false){ 
        if($once){
            if(static::once($x)){ static::plic('script', $x); }
        } else {
            static::plic('script', $x);
        }
    }
    
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
    
    public static function plugin($expr, $attribs = []){
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
    
    public static function on_action(string $action, callable $f){
        if($action = $_REQUEST['--action'] ?? null){
            ($f)();
            exit;
        }
    }
    
    public static function on_view($type, callable $f){
        ($f)();
    }    
    
}