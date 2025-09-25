<?php

# ######################################################################################################################
#region Path
namespace _ { if(!\function_exists(p::class)){ function p(string $expr, int $levels = 0){
    return \str_replace('\\','/', $levels ? \dirname($expr , $levels) : $expr);
}}}
namespace _ { if(!\function_exists(p__is_rooted::class)){ function p__is_rooted($expr){
    return ($expr[0]??'')=='/' || ($expr[1]??'')==':';
}}}
namespace _ { if(!\function_exists(slashes::class)){ function slashes($path){ 
    return \str_replace('\\','/',$path); 
}}}
namespace _ { if(!\function_exists(backslashes::class)){ function backslashes($path){ 
    return \str_replace('/','\\', $path); 
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(get_caller::class)){ function get_caller($offset = 0){
    $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset);
    // Index 0: get_caller
    // Index 1: my_function
    // Index 2: caller of my_function (this is what we want)
    return $backtrace[2 + $offset] ?? null;
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(path_here::class)){ function path_here(string $path) {
    if($file = \_\get_caller(-1)['file'] ?? null){
        return \dirname($file)."/{$path}";
    }
}}}
#endregion
# ######################################################################################################################
#region Env
namespace _ { if(!\function_exists(e::class)){ function e(string $n){
    static $E = [];
    if(!\array_key_exists($n, $E)){
        $k = "FW_{$n}";
        $E[$n] = 
            $_ENV[$n]
            ?? (\defined($k) ? \constant($k) : null)
            ?? ((($r = \getenv($k)) !== false) ? $r : null)
            ?? $_SERVER[$k]
            ?? $_SERVER["REDIRECT_{$k}"]
            ?? $_SERVER["REDIRECT_REDIRECT_{$k}"]
            ?? null
        ;
    }
    return $E[$n];
}}}
#endregion
# ######################################################################################################################
#region File
namespace _ { if(!\function_exists(fob::class)){ function fob(string $path){
    static $I; $I OR $I = (\class_exists(\_\i\file::class)) ? \_\i\file::class : \SplFileInfo::class;
    return new $I($path);
}}}
namespace _ { if(!\function_exists(t::class)){ function t($expr = null){
    static $fn;
    static $remap = [];
    static $alt = [];
    static $ensure__fn; $ensure__fn OR $ensure__fn = function(string $new, string $extends, array $options = []){
        if($extends && \class_exists($extends)){
            $def = '';
            if(\preg_match("/((.*)\\\)?(\w+)$/", $new, $mx)){
                //* yeah evil-eval: but this is for dynamic use so please excuse me!!!
                $extends = \ltrim($extends,'\\');
                if($mx[2]){
                    eval("namespace {$mx[2]}; final class {$mx[3]} extends \\{$extends} { {$def} }");
                } else {
                    eval("final class {$mx[3]} extends \\{$extends} { {$def} }");
                }
                return true;
            } else {
                //* hey you can't be here!!! not possible!!! 
                throw new \Exception('Invalid Expression');
            }
        } else {
            return false;
        }
    };
    !\is_bool($expr) && \is_null($fn) AND \_\t(true);
    if(\is_string($expr)){
        $p = \str_replace('\\','/', $n);
        $resolve = ((($p[0]??'')=='/' || ($p[1]??'')==':'))
            ? 'realpath'
            : 'stream_resolve_include_path'
        ;
        if($remap){
            foreach($remap as $k => $v){ 
                if(\str_starts_with($p, $k)){
                    $p = $v.(\substr($p,\strlen($k)));
                    break;
                }
            }
        }
        if(!$suffix){
            if($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p)){
                return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));    
            }
        } else {
            foreach(\is_array($suffix) ? $suffix : explode('|', $suffix) as $k => $t){
                $x = ($m = !\is_numeric($k)) ? $k : $t;
                if($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p.$x)){
                    if($t){
                        if($a){
                            return [ \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f)), $t, $p];
                        } else {
                            return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                        }
                    } else {
                        return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                    }
                }
            }
        }
    } else if(\is_array($expr)){
        foreach($expr as $k => $v){
            $p = \str_replace('\\','/', $k);
            if(\is_string($v)){
                $remap[\str_replace('\\','/', $k)] = \str_replace('\\','/', $v);
                \krsort($remap);
            } else if($v instanceof \closure){
                $alt[$p] = $v;
            } else if(\is_array($v)) {
                if(isset($v['extends'])){
                    $alt[$p] = function() use($k, $v, $ensure__fn){
                        ($ensure__fn)($k, $v['extends'], $v);
                    };
                }
            }
        }
    } else if(\is_bool($expr)){
        if($expr == true && !$fn){ //is false or null or empty
            \spl_autoload_register($fn = function($n) use(&$alt,&$remap, $ensure__fn){
                $p = \str_replace('\\','/', $n);
                if($c = $alt[$p] ?? false){
                    ($c)();
                    return;
                } else if($f = \_\f($p,'#.php')) {
                    include (string) $f;
                    return;
                } else if(
                    \str_starts_with(($panel = \strtok($n,'\\')),'__')
                    && ($table = \strtok('\\'))
                    && ($extn = \strtok(''))
                    && \class_exists($component = "{$panel}\\{$table}")
                ){
                    $x = $component;
                    do{
                        if(($ensure__fn)($n, "{$x}\\{$extn}")){
                            return;
                        }
                    } while($x = \get_parent_class($x));
                }
            },true,false);
        } else if($fn){
            \spl_autoload_unregister($fn);
            $fn = false;
        }
    } else {
        throw new \Exception('Invalid Argument');
    }
}}}
namespace _ { if(!\function_exists(f::class)){ function f(string|array $n, array|string $suffix = ''){
    static $remap = [];
    if(!$n){
    
    } else if(\is_string($n)){
        $p = \str_replace('\\','/', $n);
        $resolve = ((($p[0]??'')=='/' || ($p[1]??'')==':'))
            ? 'realpath'
            : 'stream_resolve_include_path'
        ;
        if(!$suffix){
            if($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p)){
                return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));    
            }
        } else {
            foreach(\is_array($suffix) ? $suffix : explode('|', $suffix) as $k => $t){
                $x = ($a = !\is_numeric($k)) ? $k : $t;
                if(
                    ($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p.$x))
                    || ($f = $resolve($GLOBALS['_TRACE']['File Resolve'] = $p.'/'.$x))
                ){
                    if($t){
                        if($a){
                            return [ \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f)), $t, $p];
                        } else {
                            return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                        }
                    } else {
                        return \_\fob($GLOBALS['_TRACE']['File Found'] = \str_replace('\\','/',$f));
                    }
                }
            }
        }
    } else if(\is_array($n) && \count($p) == 2) {
        if(\is_class($class = \str_replace('/','\\', $n[0]))){
            $path = $n[1];
            do{
                $j = \str_replace('\\','/',$class).($path ? '/' : '');
                if($f = \_\f("{$j}{$path}", $suffix)){
                    return $f;
                }
            } while($class = \get_parent_class($class));
        } else if($v = $remap[$p] ?? null){
            if($f = \_\f($v.(\substr($p,\strlen($k))), $suffix)){
                return $f;    
            }
        } else if($f = \_\f(\implode('/',$n), $suffix)){
            return $f;
        }
    } else if(\is_object($n)) {
        if(isset($n->remap)){
            foreach($n->remap as $k => $v){
                $p = \str_replace('\\','/', $k);
                if($v instanceof \closure){
                    $alt[$p] = $v;
                } else if(\is_string($v)){
                    $remap[\str_replace('\\','/', $k)] = \str_replace('\\','/', $v);
                    \krsort($remap);
                }
            }
        }        
    } else if($n instanceof \SplFileInfo){
        return $n;
    }
}}}
namespace _ { if(!\function_exists(g::class)){ function g(string $p, int $glob_flags = 0, $expr ='{*,.*}', \closure $mapper = null){ 
    if(func_num_args() <= 2){
        /* using gxp would not work on files */
        $p = \_\p($p);
        $list = [];
        foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
            foreach(\glob("{$d}/{$p}", $glob_flags) as $f){
                $list[] = \_\fob($f);
            }
        }
        return $list;
    } else {
        static $level = 0;
        try {
            $level ++;
            $x = [];
            if($level === 1){
                $glob_flags |= (\strpos($expr,'{') !== false) ? GLOB_BRACE : 0;
            }
            $files = \glob("{$p}/{$expr}", GLOB_MARK | $glob_flags);
            foreach($files as $file){
                if($file[-1] !== DIRECTORY_SEPARATOR){
                    $x[($mapper) ? ($mapper)(basename($file),'key') : basename($file)] = ($mapper) ? ($mapper)($file,'val') : $file;
                }
            }
            $dirs = \glob("{$p}/{*,.*}", GLOB_ONLYDIR | GLOB_BRACE );
            foreach($dirs as $dir){
                $basename = basename($dir);
                if($basename !== '.' && $basename !== '..'){
                    if($r = g($dir, $glob_flags, $expr, $mapper)){
                        $x[($mapper) ? ($mapper)($basename,'dir') : $basename] = $r;
                    }
                }
            }
            return $x;
        } finally {
            $level --;
        }
    }
}}}
#endregion
# ######################################################################################################################
#region Url
namespace _ { if(!\function_exists(u::class)){ function u($path = null, $portal = null){
    if(\func_num_args() > 1){
        return \_\SITE_URL.(
            ($role = $_REQUEST->_['role'] ?? '') 
                ? ($portal ? "/{$portal}.{$role}" : "") //only portals have roles
                : ($portal ? "/{$portal}" : "") //only portals have roles
        ).($path ? "/{$path}" : "");
    } else {
        return \_\BASE_URL.($path ? "/{$path}" : "");
    }
}}}
#endregion
# ######################################################################################################################
#region Dx
namespace _ { if(!\function_exists(on_default::class)){ function on_default($on_default = null){
    if($on_default instanceof \Throwable){
        throw $on_default;
    } else if($on_default instanceof \closure){
        return ($on_default)();
    } else {
        return $on_default;
    }
}}}
#endregion
# ######################################################################################################################
#region ARRAY
namespace { if(!\function_exists(array_patch_recursive::class)){ function array_patch_recursive($array,...$patches){
    static $patcher;
    if(!$patcher){
        $patcher = function(&$array, $patch) use(&$patcher){
            foreach($patch as $k => $v){
                if(isset($array[$k])){
                    if(\is_array($v) && \is_array($array[$k]) && $k[0] != '.'){
                        ($patcher)($array[$k], $v);
                    } else {
                        $array[$k] = $patch[$k];
                    }
                } else {
                    $array[$k] = $patch[$k];
                }
            }
        };
    }
    foreach($patches as $patch){
        ($patcher)($array, $patch);
    }
    return $array;
}}}
namespace { if(!\function_exists(array_purge_recursive::class)){ function array_purge_recursive(&$array, $eq = null){
    foreach($array as $k => &$v){
        if(\is_array($v)){
            \array_purge_recursive($v,$eq);
        } else if($v === $eq){
            unset($array[$k]);
        }
    }
}}}
namespace { if(!\function_exists(array_purge::class)){ function array_purge(&$array, $eq = null){
    foreach($array as $k => &$v){
        if($v === $eq){
            unset($array[$k]);
        }
    }
}}}
# ######################################################################################################################
#region CANVAS
namespace _ { if(!\function_exists(clear::class)){ function clear($top){
    while(\ob_get_level() > \_\OB_OUT){ 
        @\ob_end_clean(); 
    }
    \ob_start();
}}}
namespace _ { if(!\function_exists(clean::class)){ function clean(callable $to = null, bool $restart = true, bool $till = \_\OB_OUT){
    ($till <= \_\OB_TOP) OR $till = \_\OB_TOP;
    if($to){
        $i = $till + 1;
        while(\ob_get_level() > $i){ 
            @\ob_end_clean(); 
        }
        $d = @\ob_get_clean();
        $restart AND \ob_start();
        if(\is_callable($to)){
            return ($to)($d);
        } else {
            return $d;
        }
    } else {
        while(\ob_get_level() > $till){ 
            @\ob_end_clean(); 
        }
        $restart AND \ob_start();
    }
}}}
namespace _ { if(!\function_exists(capture::class)){ function capture(callable|null $to = null){
    $d = \ob_get_contents(); 
    \ob_end_clean();  
    \ob_start();
    if(\is_callable($to)){
        return ($to)($d);
    } else {
        return $d;
    }
}}}
namespace _ { if(!\function_exists(render::class)){ function render(mixed $expr, bool|array $params = [], bool $texate = false){
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
}}}
namespace _ { if(!\function_exists(texate::class)){ function texate(mixed $expr, array $params = []){
    return \_\render($expr, $params, true);
}}}
namespace _ { if(!\function_exists(prt::class)){ function prt($o){
    \_\IS_HTML AND print("<pre>");
    echo \is_scalar($o) ? $o : \json_encode($o,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    if (\json_last_error() !== JSON_ERROR_NONE) {
        echo \json_encode(["PRT-ERROR" => \json_last_error().": ".\json_last_error_msg()]);
    }
    \_\IS_HTML AND print("</pre>");
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(once::class)){ function once($key){
    static $keys = [];
    if($keys[$key] ?? false){
        return false;
    } else {
        $keys[$key] = true;
        return true;
    }
}}}
namespace _ { if(!\function_exists(call_once::class)){ function call_once($key,\closure $fn){
    if(\_\once($key)){
        ($fn)();
    }
}}}
namespace _ { if(!\function_exists(owner_of::class)){ function owner_of(object $resource, object|bool $owner = null){
    static $c = []; 
    if(\is_null($owner)){
        return $c[\spl_object_id($resource)] ?? null;
    } else if(\is_bool($owner) && $owner == false){
        unset($c[\spl_object_id($resource)]);
    } else {
        return $c[\spl_object_id($resource)] = $owner;
    }
}}}
namespace _ { if(!\function_exists(shared_ref::class)){ function &shared_ref($name = null){
    static $_CACHE = [];
    if($name){
        if(!isset($_CACHE[$name])){
            $_CACHE[$name] = [];
        }
        return $_CACHE[$name];
    } else {
        return $_CACHE;
    }
}}}
namespace _ { if(!\function_exists(on_default::class)){ function on_default($on_default = null){
    if($on_default instanceof \Throwable){
        throw $on_default;
    } else if($on_default instanceof \closure){
        return ($on_default)();
    } else {
        return $on_default;
    }
}}}
namespace _ { if(!\function_exists(is_empty::class)){ function is_empty($obj, array $exclude = []){
    if(\is_object($obj)){
        if(!$exclude){
            foreach( $obj as $x ) return false;
        } else {
            foreach ($obj as $key => &$value) {
                if(!\in_array($key, $exclude)){
                    return false;
                }
            }
        }
    } else {
        return empty($obj);
    }
    return true;
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(php_file::class)){ function php_file(string $file, bool|callable $on_default = false):callable {
    if($__FILE__ = \_\f($path,'.php')){
        return (function (array $__PARAM__) use($__FILE__){
            $__PARAM__ AND \extract($__PARAM__, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
            return include $__FILE__;
        }); //->bindTo(\_::_(),\_::class) if needed do it outside
    }
    return \_\on_default($on_default ?? new \Exception("Unable to resolve: $file"));
}}}
namespace _ { if(!\function_exists(php_func::class)){ function php_func(string $func, bool|callable $on_default = false):callable {
    if(\function_exists($func)){
        return $func;
    }
    if($__FILE__ = \_\f($func,'-fdef.php')){
        include $__FILE__;
        if(!\function_exists($func)){
            return $func;
        }
    }
    return \_\on_default($on_default ?? new \Exception("Unable to resolve: $file"));
}}}
namespace _ { if(!\function_exists(fn_func::class)){ function fn_func(string $func, bool|callable $on_default = false):callable {
    static $fnf = [];
    if($__FILE__ = $fnf[$func] ?? ($fnf[$func] = \_\f($func,'-fn_func.php'))){
        return include $__FILE__; //->bindTo(\_::_(),\_::class) if needed do it outside
    }
    return \_\on_default($on_default ?? new \Exception("Unable to resolve: $file"));
}}}
#endregion
# ######################################################################################################################
#region Session
namespace _ { if(!\function_exists(session::class)){ function session(){
    return $_SESSION;
}}}
namespace _ { if(!\function_exists(session_var::class)){ function session_var($key,...$args){
    if(!$args){
        return $_SESSION['--var'][$key] ?? '';
    } else {
        $_SESSION['--var'][$key] = $args[0];
    }
}}}
namespace _ { if(!\function_exists(flash::class)){ function flash($key,...$args){
    if(!$args){
        return $GLOBALS['_']['FLASH'][$key];
    } else {
        $_SESSION['--flash'][$key] = $args[0];
    }
}}}
namespace _ { if(!\function_exists(runspan::class)){ function runspan(int $decimal = 6, $unit = 's'){
    return \number_format(((\microtime(true) - \_\MSTART)), $decimal).$unit;
}}}
#endregion
# ######################################################################################################################
#region Response
namespace _ { if(!\function_exists(abort::class)){ function abort(int $httpcode_or_level = 1, string $message = null){
    if($httpcode_or_level < 100){
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', $httpcode_or_level);
        exit();
    } else {
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
        \http_response_code($code);
        echo $message;
        exit();
    }
}}}
namespace _ { if(!\function_exists(redirect::class)){ function redirect(bool|string|null $url = null, bool|string|array|null $query = null){
    if(!$url){
        $goto = $_SERVER['REQUEST_URI'];
    } else if($url === true){
        $goto = \strtok($_SERVER['REQUEST_URI'],'?');
    } else if($url === '.'){
        $goto = \_\CTLR_URL ?? null ?: \_\BASE_URL;
    } else if($url[0] == '?') {
        //* note: URP - pure path
        $goto = \_\CTLR_URL.'/'.$url; 
    } else if($url[0] == '.') {
        $prefix = \_\CTLR_URL ?? null ?: \_\BASE_URL;
        if(($url[1] ?? '') == '/'){
            $goto = \rtrim($prefix.'/'.\substr($url,1), '/.');
        } else {
            $goto = \rtrim($prefix.'/'.$url,'.');
        }
    } else if($url[0] === '/') {
        $goto = $url;
    } else if(preg_match('/^http[s]?:/',$url)){
        $goto = $url;
    } else {
        $goto = \_\BASE_URL.'/'.$url;
    }
    
    if($goto){
        if($query == true){
            $goto .= "?0=".\_\MSTART;
        } else if(\is_array($query)){
            $goto .= "?".\http_build_query($query);
        }
    }
    
    // if($goto){
    //     $GLOBALS['--RESPONSE'] = (object)[
    //         //* note: by default the redirect is 302 i.e. temporary
    //         'type' => 'redirect',
    //         'headers' => ["Location: ". $goto],
    //     ];
    // }

    if($goto){
        \header("Location: ". $goto);
        exit();
    }
}}}
#endregion
# ######################################################################################################################
#region DOB
namespace _ { if(!\function_exists(dob::class)){ function dob(string $path, array $options = []){
    if(array_key_exists('value', $options)){
        $value = $options['value'];
        if(\str_ends_with($path,'-$.php')){
            $file = $path;
        } else if(($path[0]??'')=='/' || ($path[1]??'')==':'){
            $file = "{$path}-$.php";
        } else {
            $file = \_\DATA_DIR."/{$path}-$.php";
        }
        if(\is_null($value)){
            if(\is_file($file)){
                unlink($file);
            }
        } else {
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            if($options['patch'] ?? null){
                $o = \is_file($file) ? dob($file) : [];
                $write = $o ? \array_patch_recursive($o, (array) $value) : $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, "<?php return ".\var_export($write, true).';');
            } else {
                $write = (array) $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, "<?php return ".\var_export($write, true).';');
            }
        }
    } else { 
        if(
            \is_file($file = \_\DATA_DIR."/{$path}-$.php")
            || ($file = \_\f($path,'-$.php'))
        ){
            return (function($__FILE__){ return include $__FILE__; })($file);
        }
    }
}}}
namespace _\dob { if(!\function_exists(json::class)){ function json(string $path, array $options = []){
    if(array_key_exists('value', $options)){
        $value = $options['value'];
        if(($path[0]??'')=='/' || ($path[1]??'')==':'){
            $file = "{$path}-$.json";
        } else {
            $file = \_\DATA_DIR."/{$path}-$.json";
        }
        if(\is_null($value)){
            if(\is_file($file)){
                unlink($file);
            }
        } else {
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            if($options['patch'] ?? null){
                $o = \is_file($file) ? dob($file) : [];
                $write = $o ? \array_patch_recursive($o, (array) $value) : $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, \json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            } else {
                $write = (array) $value;
                if(isset($options['purge'])){
                    \array_purge_recursive($write, $options['purge']);
                }
                \file_put_contents($file, \json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            }
        }
    } else {
        if(
            \is_file($file = \_\DATA_DIR."/{$path}-$.json")
            || ($file = \_\f($path,'-$.json'))
        ){
            return \json_decode(\file_get_contents($file), $options['assoc'] ?? true);
        }
    }
}}}
#endregion
# ######################################################################################################################
#region Data db
namespace _ { if(!\function_exists(db::class)){ function db() {
    static $I; return $I ?? ($I = new class () extends \stdClass{
        public readonly ?\PDO $pdo;
        public function __construct(){
            if($database = \_\e('DB_DATABASE')){
                $hostname = \_\e('DB_HOSTNAME') ?? 'localhost';
                $database = \_\e('DB_DATABASE') ?? 'unknown_db';
                $username = \_\e('DB_USERNAME') ?? 'root';
                $password = \_\e('DB_PASSWORD') ?? 'pass';
                $charset  = \_\e('DB_CHAR_SET') ?? 'utf8mb4';
                $this->database = $database;
                try {
                    $this->pdo = new \PDO(
                        "mysql:host={$hostname};dbname={$this->database};charset={$charset}",
                        $username,
                        $password,
                        [
                            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, 
                            \PDO::ATTR_EMULATE_PREPARES   => false,
                        ]
                    );
                } catch (\PDOException $e) {
                    throw new \PDOException($e->getMessage(), (int)$e->getCode());
                }
            } else {
                $this->pdo = null;
            }
        }
        
        public function is_connected() : bool {
            return ($this->pdo ?? null) ? true : false;
        }
        
        public function __call($method,$args){
            return $this->pdo?->$method(...$args);
        }
        
        public function table__list(){
            $query = "SHOW TABLES";
            if($stmt = $this->pdo?->query($query)){
                return $stmt->fetchAll(\PDO::FETCH_COLUMN);
            }
        }
        
        public function table_column__list($table) {
            if($stmt = $this->pdo?->query("SHOW COLUMNS FROM `$table`")){
                return $stmt->fetchAll(\PDO::FETCH_COLUMN);
            }
        }
    });
}}}
#endregion
# ######################################################################################################################
#region Data D
namespace _ { if(!\function_exists(d::class)){ function d($path = null, $id = null, array $params = []){
    static $I; return ($I ?? $I = (function($path = null, $id = null, array $params = []){
        if(!($this->i ?? null)){
            $this->i = (object)[];   
            $this->i->LAST_QUERY = null;
            $this->i->TABLES = [];
            $this->i->PDO = null;
            $this->i->pdo_write__fn = function($sql, $data) {
                //! credits: https://stackoverflow.com/a/7716896/10753162
                $this->i->LAST_QUERY = \preg_replace_callback('/:([0-9a-z_]+)/i', function($m) use(&$data){
                    $v = $data[$m[1]];
                    if ($v === null) {
                        return "NULL";
                    }
                    if (!is_numeric($v)) {
                        $v = str_replace("'", "''", $v);
                    }
                    return "'". $v ."'";
                }, $sql);
                return $this->i->PDO->prepare($sql)->execute($data);
            };
            $this->i->table_info__fn = function($tblp) {
                if($x = $this->i->PDO->query("SHOW KEYS FROM `{$tblp}` WHERE Key_name = 'PRIMARY'")->fetch()){
                    $this->i->TABLES[$tblp]['idk'] = $x['Column_name'];
                }
                $fields = $this->i->PDO->query("DESCRIBE `{$tblp}`")->fetchAll(\PDO::FETCH_COLUMN);
                $this->i->TABLES[$tblp]['fields'] = \array_flip($fields);
                return $this->i->TABLES[$tblp];
            };
            $this->i->e__fn = function($k){
                global $_;
                $n = "FW_{$k}";
                return $_[$k]
                    ?? (\defined($n) ? \constant($n) : null)
                    ?? ((($r = getenv($n)) !== false) ? $r : null)
                    ?? $_SERVER[$n]
                    ?? $_SERVER["REDIRECT_{$n}"]
                    ?? null
                ;
            };
            if(\is_null($this->i->PDO)){
                $hostname = $_ENV['DB_HOSTNAME'] ?? 'localhost';
                $database = $_ENV['DB_DATABASE'] ?? '';
                $char_set = $_ENV['DB_CHAR_SET'] ?? 'utf8mb4';
                $username = $_ENV['DB_USERNAME'] ?? 'root';
                $password = $_ENV['DB_PASSWORD'] ?? '';
                if($database){
                    try {
                        $this->i->PDO = new \PDO(
                            "mysql:host={$hostname};dbname={$database};charset={$char_set}", 
                            $username,
                            $password,
                            [
                                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                                \PDO::ATTR_EMULATE_PREPARES   => false,
                            ]
                        );
                    } catch (\PDOException $e) {
                        throw new \PDOException($e->getMessage(), (int)$e->getCode());
                    }
                    foreach($this->i->PDO->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN) as $tblp){
                        $this->i->TABLES[$tblp] = [];
                    }
                } else {
                    $POD = false;
                }
            }                
        }
        if(!$path){
            return $this->i->PDO;
        }
        $tblp = \str_replace('\\','/',$path);
        if($tblp === '.'){
            if(\is_scalar($id)){
                return match($id){
                    'pdo' => $this->i->PDO,
                    'tables' => $this->i->TABLES,
                };
            } else {
                return null;
            }
        } else if(\is_scalar($id)){
            if(\is_string($params)){
                switch($params){
                    case 'delete':{ 
                        if(!$is_new){
                            $frec = \_\DATA_DIR."/0/{$tblp}/@{$id}/.$-json";
                            $drec = \dirname($frec);
                            if(\is_file($frec)){
                                \unlink($frec);
                            }
                            if(\is_dir($drec)){
                                foreach (
                                    new \RecursiveIteratorIterator(
                                        new \RecursiveDirectoryIterator($drec, \RecursiveDirectoryIterator::SKIP_DOTS),
                                        \RecursiveIteratorIterator::CHILD_FIRST
                                    ) as $x
                                ){
                                    $x->isDir() 
                                        ? \rmdir($x->getRealPath()) 
                                        : \unlink($x->getRealPath())
                                    ;
                                }
                                \rmdir($drec);
                            }
                            if($this->i->PDO && isset($this->i->TABLES[$tblp])){
                                $this->i->PDO->exec("DELETE FROM `{$tblp}` WHERE `id`='{$id}'");                     
                            }
                        }
                    } break;
                }
            } else if(\is_array($params)){
                $p__overwrite = false;
                $p__write = null;
                $params && \extract($params, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p_');
                $is_new = ($id == '+' || (\is_int($id) && $id < 0) || $id == '');
                $this->i->PDO 
                    AND isset($this->i->TABLES[$tblp]) 
                    AND !$this->i->TABLES[$tblp] 
                    AND ($this->i->table_info__fn)($tblp)
                ;
                if(\is_array($write = $p__write)) {
                    $files = $write['/'] ?? [];
                    $fsdob = $write['-'] ?? [];
                    unset($write['-'],$write['/']);
                    if(!\is_null($dbdob = ($this->i->PDO && isset($this->i->TABLES[$tblp])) ? [] : null)){
                        foreach($write as $k => $v){
                            if(isset($this->i->TABLES[$tblp]['fields'][$k])){
                                $dbdob[$k] = $v;
                            }
                        }
                    }
                    if($dbdob){
                        $idk = $this->i->TABLES[$tblp]['idk'];
                        $insert = (
                            $is_new
                            || !($this->i->PDO->query("SELECT `{$idk}` FROM `{$tblp}` WHERE `{$idk}`='{$id}'")
                                ->fetch(\PDO::FETCH_OBJ)
                            )
                        );
                    
                        if($insert){
                            if(!$is_new && $id){
                                $dbdob['id'] = $id;
                            } else {
                                unset($dbdob['id']);
                            }
                            if($dbdob){
                                $keys = array_keys($dbdob);
                                $l1 = '`'.implode('`, `',$keys).'`';
                                $l2 = ":".implode(', :',$keys);
                                $sql = "INSERT INTO `{$tblp}` ({$l1}) VALUES ({$l2})";
                            } else {
                                $sql = "INSERT INTO `{$tblp}` () VALUES();";
                            }
                            if(($this->i->pdo_write__fn)($sql, $dbdob)){
                                $id = $this->i->TABLES[$tblp]['last_insert_id'] = $this->i->PDO->lastInsertId();
                            }
                        } else {
                            unset($dbdob['id']);
                            if($dbdob){
                                $keys = \array_keys($dbdob);
                                $p1 = '`'.\implode('`=?, `',$keys).'`=?';
                                $sql = "UPDATE `{$tblp}` SET {$p1} WHERE `{$idk}`='{$id}'";
                                ($this->i->pdo_write__fn)($sql, \array_values($dbdob));
                            }
                        }
                    }
                    
                    if($id == '+' || (\is_int($id) && $id < 0)){
                        $id = (int) \microtime(true);
                    }
                    
                    $frec = \_\DATA_DIR."/0/{$tblp}/@{$id}/.$-json";
                    $drec = \dirname($frec);
                    \is_dir($drec) OR \mkdir($drec, 0777, true);    
                
                    if($p__overwrite){
                        \file_put_contents($frec,\json_encode($fsdob, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    } else {
                        \file_put_contents($frec, 
                            \json_encode(
                                \array_replace(
                                    \is_file($frec) ? \json_decode(\file_get_contents($frec),true) : [], 
                                    $fsdob
                                ), 
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                            )
                        );
                    }
        
                    if($p__overwrite){
                        if(\is_dir($drec)){
                            foreach (
                                new \RecursiveIteratorIterator(
                                    new \RecursiveDirectoryIterator($drec, \RecursiveDirectoryIterator::SKIP_DOTS),
                                    \RecursiveIteratorIterator::CHILD_FIRST
                                ) as $x
                            ){
                                $x->isDir() 
                                    ? \rmdir($x->getRealPath()) 
                                    : \unlink($x->getRealPath())
                                ;
                            }
                            //\rmdir($drec);
                        } 
                    }
                    
                    foreach($files as $field => $value){
                        $pi_k = \pathinfo($file_k = "{$drec}{$field}");
                        $fnp_k = "{$pi_k['dirname']}/{$pi_k['filename']}";
                        $extn_k = $pi_k['extension'] ?? '*';
                        if($value instanceof \SplFileInfo){
                            if(\file_exists($value)){
                                if(\is_uploaded_file($value)){
                                    $extn_v = pathinfo($value->details->name, PATHINFO_EXTENSION);
                                } else {
                                    $extn_v = $value->getExtension();
                                }
                                if($extn_k === '*'){
                                    foreach(\glob("{$fnp_k}{,.*}",GLOB_BRACE) as $f){
                                        \unlink($f);
                                    }
                                }
                                $filedest = (!$extn_v) ? $fnp_k :"{$fnp_k}.{$extn_v}";
                                \is_dir($dir = \dirname($filedest)) OR \mkdir($dir,0777,true);
                                \is_uploaded_file($value) ? \move_uploaded_file($value, $filedest) : \copy($value, $filedest);
                            } else {
                                //* todo - report error!
                            }
                        } else if(empty($value)) {
                            if($extn_k === '*'){
                                foreach(\glob("{$fnp_k}{,.*}",GLOB_BRACE) as $f){
                                    \unlink($f);
                                } 
                            } else {
                                \unlink("{$fnp_k}.{$extn_k}");
                            }
                        }
                    }
                } else if(!$is_new){
                    $frec = \_\DATA_DIR."/0/{$tblp}/@{$id}/.$-json";
                    $drec = \dirname($frec);
                    $rec = [];
                    if($this->i->PDO && isset($this->i->TABLES[$tblp])){
                        $idk = $this->i->TABLES[$tblp]['idk'];
                        $rec = $this->i->PDO->query("SELECT * FROM `{$tblp}` WHERE `{$idk}`='{$id}' LIMIT 1")
                            ->fetchAll(\PDO::FETCH_ASSOC)[0] ?? []
                        ;
                    }
                    if(\is_file($frec)){
                        $rec['-'] = \json_decode(\file_get_contents($frec),true);
                    }            
                    foreach(\glob("{$drec}/*", GLOB_BRACE) as $f){
                        if(!\is_dir($f)){
                            $pi = pathinfo($f);
                            $rec['/']["{$pi['filename']}"] = new \SplFileInfo(\str_replace('\\','/',$f));
                        }
                    }
                    return $rec;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else if(\is_callable($id)) {
            return (function($tblp, $q_fn, $params, $i){
                $this->origin = 'o';
                $this->tblp = $tblp;
                $this->params = $params;
                ($q_fn)($this, $params);
                $this->i = $i;
                $this->prepare__fn = function($sql){
                    $q__pre_exec = ($this->on__prepare__fn ?? null) instanceof \Cloaure 
                        ? $this->on__prepare__fn
                        : function(){ yield from []; }
                    ;
                    $stmt = $this->i->PDO->prepare($sql);
                    foreach(($q__pre_exec)($this->i->PDO, $stmt) as $k => $bind){
                        switch($type = gettype($bind)) {
                            case "boolean":{
                                $stmt->bindValue($k, $bind, \PDO::PARAM_BOOL);
                            } break;
                            case "integer":{
                                $stmt->bindValue($k, $bind, \PDO::PARAM_INT);
                            } break;
                            case "double":{
                                $stmt->bindValue($k, $bind, \PDO::PARAM_STR);
                            } break;
                            case "string":{
                                $stmt->bindValue($k, $bind, \PDO::PARAM_STR);
                            } break;
                            case "array":{
                                $stmt->bindValue($k, ...$bind);
                            } break;
                            case "NULL":{
                                $stmt->bindValue($k, $bind);
                            } break;
                            default:{
                                throw new \Exception("Unsupported data-type '{$type}' used in PDO Statement");
                            } break;
                        }
                    }
                    return $stmt;
                };
                $this->execute__fn = function($sql, $step = 1){
                    if(($this->debug__fn ?? null) instanceof \closure){
                        ($this->debug__fn)([
                            'step' => $step,
                            'sql' => $sql,
                        ]);
                    }
                    $stmt = ($this->prepare__fn)($sql);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    return $rows;
                };
                $this->fetch__fn = function(int $start = 0, int $limit = 1){
                    $limit = ($limit < 0)
                        ? $this->i->PDO->query("SELECT COUNT(*) FROM {$this->tblp}")->fetchColumn()
                        : $limit
                    ;
                    if($start < 0){
                        $rstart = -$start;
                        $sql_limit = "ORDER BY `{$this->origin}`.`id` DESC LIMIT {$rstart}";
                    } else {
                        $sql_limit = "LIMIT {$limit} OFFSET {$start}";    
                    }
                    //$pack = $this->i->PDO->query("{$this->sql_limless} {$sql_limit}")->fetchAll(\PDO::FETCH_ASSOC) ?? [];
                    $rows = ($this->execute__fn)("{$this->sql_limless} {$sql_limit}", 1);
                    $count = \count($rows);
                    $seq = $start;
                    foreach($rows as &$d){
                        $d = ['#' => $seq++] + $d;
                    }
                    if(\is_callable($fn = $this->adjust_each__fn ?? null)){
                        foreach($rows as &$row){
                            ($fn)($row, $rows);
                        }
                    }
                    return $rows;
                };
                $this->sql_selects = \implode('', \iterator_to_array((function(){
                    yield "SELECT\n\t";
                    $s = [];
                    if($qselect = $this->select ?? null){
                        if(\is_array($qselect)){
                            foreach($qselect ?? [] as $k => $v){
                                if(\is_numeric($k)){
                                    $s[] =  "{$v}";    
                                } else {
                                    $s[] =  "{$v} as `{$k}`";    
                                }
                            }
                            yield \implode(",\n\t", $s);
                        } else if(\is_string($qselect)){
                            yield $qselect;
                        } else {
                            yield '*';    
                        }
                    } else {
                        yield '*';
                    }
                })()));
                $this->sql_assemblers = \implode('', \iterator_to_array((function(){
                    yield "\nFROM\n\t`{$this->tblp}` as `{$this->origin}`";
                    foreach($this->join ?? [] as $v){
                        yield "\n\t".$v;
                    }
                    if($where = $this->where ?? null){
                        if(\is_string($this->where)){
                            yield "WHERE ".$this->where;
                        } else if(\is_array($this->where)) {
                            $filters = [];
                            $count = \count($args);
                            if($count == 1){
                                $filters[] = $args[0];
                            } else if($count == 2){
                                list($col, $with) = $args;
                                $compate = '=';
                                if($x = $this->select[$col] ?? false){
                                    $x = implode('`.`', \explode('.', \str_replace('`','', $x)));
                                    $filters[] = "`{$x}` {$compare} {$with}";
                                } else {
                                    $col = implode('`.`', \explode('.', \str_replace('`','', $col)));
                                    $filters[] = "`{$col}` {$compare} {$with}";            
                                }
                            } else if($count == 3){
                                list($col, $compare, $with) = $args;
                                if($x = $this->select[$col] ?? false){
                                    $x = implode('`.`', \explode('.', \str_replace('`','', $x)));
                                    $filters[] = "`{$x}` {$compare} '{$with}'";
                                } else {
                                    $col = implode('`.`', \explode('.', \str_replace('`','', $col)));
                                    $filters[] = "`{$col}` {$compare} '{$with}'";
                                }
                            }
                            if($filters){
                                yield "\nWHERE 1";
                                if($filters){
                                    yield "\n\tAND ".\implode("\n\tAND ", $filters);
                                }
                            }
                        }
                    }
                    if($this->group ?? null){
                        yield "\nGROUP BY\n\t";
                        yield implode(',', $this->group);
                    }
                    if($this->order ?? null){
                        yield "\nORDER BY\n\t";
                        yield implode(',', $this->order);
                    }
                })()));
                $this->sql_limless = "{$this->sql_selects} {$this->sql_assemblers}";
                switch($this->params['get'] ?? null){
                    case 'count_all':{
                        return $this->i->PDO->query("SELECT COUNT(*) FROM {$this->TBLP}")->fetchColumn();
                    } break;
                    case 'count_filtered':{
                        return $this->i->PDO->query("SELECT COUNT(*) ".$this->sql__assemblers)->fetchColumn();
                    } break;
                    case 'first':{
                        return ($this->fetch__fn)(0, 1);
                    } break; 
                    case 'last':{
                        return ($this->fetch__fn)(-1, 1);
                    } break; 
                    case 'all':{
                        return ($this->fetch__fn)(0, -1);
                    } break; 
                    case 'one':{
                        return ($this->fetch__fn)($this->params['start'] ?? 0, 1);
                    } break; 
                    case 'bunch':{
                        return ($this->fetch__fn)(0, $this->params['limit'] ?? -1);
                    } break; 
                    case 'range':{
                        return ($this->fetch__fn)($this->params['start'] ?? 0, $this->params['limit'] ?? -1);
                    } break; 
                    case 'slice': {
                        $start = $this->params['start'] ?? 0;
                        $limit = $this->params['limit'] ?? -1;
                        if($count_filtered = $this->i->PDO->query("SELECT COUNT(*) {$this->sql__assemblers}")->fetchColumn()){
                            $limit = ($limit < 0) ? 0 : $limit;
                            $pack = ($this->fetch__fn)($start, $limit);
                            $end = $start + $count -1;
                            $pack = [
                                'meta' => [
                                    'count_filtered' => $count_filtered,
                                    'count' => $count,
                                    'start' => $start,
                                    'end' => $end,
                                    'first' => ($count > 0) ? $start + 1 : $start,
                                    'last' => ($count > 0) ? $end + 1 : $end,
                                ],
                                'rows' => [],
                            ];
                        }  else {
                            $pack = [
                                'meta' => [
                                    'count_filtered' => 0,
                                    'count' => 0,
                                    'start' => 0,
                                    'end' => 0,
                                    'first' => 0,
                                    'last' => 0,
                                ],
                                'rows' => [],
                            ];
                        }  
                        if(\is_callable($fn = $this->adjust_pack__fn ?? null)){
                            ($fn)($pack);
                        }
                        return $pack;
                    } break;
                    case 'page': {
                        if($count_filtered = $this->i->PDO->query("SELECT COUNT(*) {$this->sql__assemblers}")->fetchColumn()){
                            $page_no = ($this->params['page_no'] > 0) ? $this->params['page_no'] : 1;
                            $page_sz = ($this->params['page_sz'] > 0) ? $this->params['page_sz'] : 10;
                            $limit = $page_sz = ($page_sz ?: 10);
                            $pages = ($count_filtered
                                ? (($limit > 0) 
                                    ? (int) \ceil($count_filtered / $limit)
                                    : 1
                                )
                                : 0
                            );
                            $page_no = ($page_no <= 0) ? 1 : ($page_no > $pages
                                ? $pages
                                : $page_no
                            );
                            $start = (($page_no-1) * $limit);
                            $rows = ($this->fetch__fn)($start, $limit);
                            $count = count($rows);
                            $end = $start + $count -1;
                            $pack = [
                                'meta' => [
                                    'pages' => $pages,
                                    'page_sz' => $page_sz,
                                    'page_no' => $page_no,
                                    'count_filtered' => $count_filtered,
                                    'count' => $count,
                                    'start' => $start,
                                    'end' => $end,
                                    'first' => ($count > 0) ? $start + 1 : $start,
                                    'last' => ($count > 0) ? $end + 1 : $end,
                                ],
                                'rows' => $rows,
                            ];
                        }  else {
                            $pack = [
                                'meta' => [
                                    'pages' => 0,
                                    'page_sz' => $page_sz,
                                    'page_no' => $page_no,
                                    'count_filtered' => 0,
                                    'count' => 0,
                                    'start' => 0,
                                    'end' => 0,
                                    'first' => 0,
                                    'last' => 0,
                                ],
                                'rows' => $rows,
                            ];
                        }
                        if(\is_callable($fn = $this->adjust_pack__fn ?? null)){
                            ($fn)($pack);
                        }
                        return $package;                        
                    } break;
                }
                
            })->bindTo((object)[])($tblp, $id, $params, $this->i);
        }
        
    })->bindTo((object)[]))($path, $id, $params);
}}}
#endregion
# ######################################################################################################################
