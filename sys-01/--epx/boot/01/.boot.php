<?php 
# ######################################################################################################################
#region FRONT
namespace { (function(){
    
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    \define('_\OB_OUT', \ob_get_level());
    !empty($_SERVER['HTTP_HOST']) AND \ob_start();
    \define('_\OB_TOP', \ob_get_level());
    \define('_\INTFC', $GLOBALS['INTFC'] =
        $GLOBALS['INTFC']
        ?? (empty($_SERVER['HTTP_HOST']) 
            ? 'cli'
            : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
        )
    );
    
})(); }
#endregion
# ######################################################################################################################
#region DX
namespace {(function(){
    
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    0 AND \error_reporting(E_ALL);
    $trap__fn = function() use(&$fault__fn){
        $type_filter__fn = function(array $types) {
            return \array_values(array_filter($types,function($className){
                return
                    !\str_contains($className,'@anonymous')
                    && !(new \ReflectionClass($className))->isInternal()
                ;
            }));
        };
        $slashes__fn = function($x) use(&$slashes__fn){
            if(\is_string($x)){
                if(($x[0] ?? null) === '\\'){
                    return \str_replace('\\','/', $x);
                } else if(($x[1] ?? null) === ":" && ($x[2] ?? null) === '\\'){
                    return \str_replace('\\','/', $x);
                } else {
                    return $x;
                }
            } else if(\is_scalar($x)){
                return $x;
            } else if(\is_array($x)){
                $list = [];
                foreach($x as $k => $v){
                    $list[$k] = ($slashes__fn)($v);
                } 
                return $list;
            } 
        };
        return [
            'elapsed' => \number_format((((\defined('_\SIG_END') ? \_\SIG_END : \microtime(true)) - \_\MSTART)),6).'s',
            'trace' => $GLOBALS['_TRACE'] ?? [],
            //'loader' => $this ?? null,
            'env' => $_ENV ?? null,
            '_request' => $_REQUEST,
            'faults' => \json_decode(\json_encode((function(){
                if(\class_exists(\_\response::class) && \method_exists(\_\response::class, 'report')){
                    return \_\response::report();
                } else {
                    return [];
                }
            })())),
            'tsp' => \explode(PATH_SEPARATOR, \get_include_path()),
            'const' => \get_defined_constants(true)['user'] ?? [],
            'profile' => [
                'time' => [
                    'elapsed' => number_format(((microtime(true) - \_\MSTART)),6).'s',
                ],
                'mem' => [
                    'limit' => \ini_get('memory_limit'),
                    'start' => (\defined('_\DX_STATS')) ? \number_format(\_\DX_STATS['MUSAGE'] / (1024 * 1024), 2).'MB' : null,
                    'usage' => \number_format(\memory_get_usage(true) / (1024 * 1024), 2).'MB',
                    'mpeak' => \number_format(\memory_get_peak_usage(true) / (1024 * 1024), 2).'MB',
                ],
                'rusage' => [
                    'start' => (\defined('_\DX_STATS'))? \_\DX_STATS['RUSAGE'] : null,
                    'usage' => \getrusage(),
                ],
                'trace' => \array_filter(
                    \preg_split(
                        "/<br>|\n/",
                        \implode('<br>',$GLOBALS['_']['FW_OUTPUT']['TRACE'] ?? [])
                    )
                ),
            ],
            'included' => \array_map($slashes__fn, \get_included_files()),
            'classes' => ($type_filter__fn)(\get_declared_classes()),
            'interfaces' => ($type_filter__fn)(\get_declared_interfaces()),
            'traits' => ($type_filter__fn)(\get_declared_traits()),
            'ini_files' => [
                'loaded' => ($slashes__fn)(\php_ini_loaded_file()),
                'scanned' => ($slashes__fn)(\php_ini_scanned_files()),
            ],
            '_server' => ($slashes__fn)((function(){
                $s = $_SERVER;
                $s['PATH'] = \explode(\PATH_SEPARATOR, $s['PATH'] ?? '');
                return $s;
            })()),
            //'_request' => $_REQUEST,
            '_get' => $_GET,
            '_post' => $_POST,
            '_files' => $_FILES,
            '_cookie' => $_COOKIE,
            'ini' => \ini_get_all(),
        ];
    };    
    $fault__fn = function($ex = null) use($trap__fn){
        static $FAULTS = [];
        if($ex){
            if(\_\IS_CLI){
                $type = $ex::class;
                echo "\033[91m\n"
                    ."{$type}: {$ex->getMessage()}\n"
                    ."File: {$ex->getFile()}\n"
                    ."Line: {$ex->getLine()}\n"
                    ."\033[31m{$ex}\033[0m\n"
                ;
            } else {
                $FAULTS[\microtime(true).':'.\uniqid()] = $ex;
            }
        } else if($FAULTS){
            if(
                (($ex = end($FAULTS)) instanceof \Throwable)
                && \str_starts_with($message = $ex->getMessage(), '--trap')
            ){
                $exit = (object)[];
                $exit->content = $trap__fn();
                return $exit;
            } else if(\_\IS_CLI){
                $exit = (object)[];
                $content = '';
                foreach(\array_reverse($FAULTS) as $k => $v){
                    if($v instanceof \Throwable){
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Exception [E0.3]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = $v;
                    } else if(\is_string($v)){
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Fault [E0.4]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = $v;
                    } else {
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Fault [E0.4]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = \json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    }
                    $content .= "\033[31m".PHP_EOL.$message.PHP_EOL.$fault_text.PHP_EOL."\033[0m";
                }
                $content .= "";
                $exit->code = 500;
                $exit->content = $content;
                return $exit;
            } else if(\_\IS_WEB){
                $exit = (object)[];
                $content = <<<HTML
                <style>
                    body{ background-color: #121212; color: #e0e0e0; font-family: sans-serif; margin: 0; padding: 20px; }
                    pre{ overflow:auto; color:red;border:1px solid red;padding:5px; background-color: #1e1e1e; max-height: calc(100vh-25px); }
                    /* Scrollbar styles for WebKit (Chrome, Edge, Safari) */
                    ::-webkit-scrollbar { width: 12px; height: 12px; }
                    ::-webkit-scrollbar-track { background: #1e1e1e; }
                    ::-webkit-scrollbar-thumb { background-color: #555; border-radius: 6px; border: 2px solid #1e1e1e; }
                    ::-webkit-scrollbar-thumb:hover { background-color: #777; }
                    /* Firefox scrollbar (limited support) */
                    * { scrollbar-width: thin; scrollbar-color: #555 #1e1e1e; }
                </style>                            
                HTML;
                foreach(\array_reverse($FAULTS) as $k => $v){
                    if($v instanceof \Throwable){
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Exception [E0.3]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = $v;
                    } else if(\is_string($v)){
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Fault [E0.4]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = $v;
                    } else {
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Fault [E0.4]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = \json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    }
                    $content .= <<<HTML
                    <pre>{$message}:
                    <b>{$fault_text}</b>
                    </pre>
                    HTML;
                }
                $content .= <<<HTML
                    <pre><i>Request: {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}</i></pre>
                HTML;
                $exit->code = 500;
                $exit->content = $content;
                return $exit;
            } else {
                $exit = (object)[];
                $content = [];
                foreach(\array_reverse($FAULTS) as $k => $v){
                    if($v instanceof \Throwable){
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Exception [E0.3]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = $v;
                    } else if(\is_string($v)){
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Fault [E0.4]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = $v;
                    } else {
                        if(\is_numeric($k)){ 
                            $message = 'Unhandled Fault [E0.4]['.\uniqid().']';
                        } else {
                            $message = $k;
                        }
                        $fault_text = \json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    }
                    $content[] = [$fault_text];
                }
                $exit->code = 500;
                $exit->content = $content;
                return $exit;
            }
        }
    };
    \set_exception_handler(function($ex) use($fault__fn){
        if(
            \class_exists(\_\dx::class, false)
            && \method_exists(\_\dx::class,'_')
            && \method_exists(\_\dx::_(),'on__exception')
        ){
            \_\dx::_()->on__exception($ex);
        } else {
            $fault__fn($ex);
        }
        exit();
    });
    \set_error_handler(function($severity, $message, $file, $line) use($fault__fn){
        if(
            \class_exists(\_\dx::class, false)
            && \method_exists(\_\dx::class,'_')
            && \method_exists(\_\dx::_(),'on__error')
        ){
            \_\dx::_()->on__error($ex);
        } else {
            try{
                throw new \ErrorException(
                    $message, 
                    0,
                    $severity, 
                    $file, 
                    $line
                );
            } catch(\Throwable $ex) {
                if(!\defined('_\SIG_END')){
                    $fault__fn($ex);    
                } else {
                    throw $ex;
                }
            }
        }
    });
    \register_shutdown_function(function() use($fault__fn){
        if(
            \class_exists(\_\dx::class, false)
            && \method_exists(\_\dx::class,'_')
            && \method_exists(\_\dx::_(),'on__shutdown')
        ){
            \_\dx::_()->on__shutdown();
        } else {
            
            try{
                if(\defined('_\SIG_ABORT') && \_\SIG_ABORT < 0){
                    exit();
                }
                
                if(\defined('_\SIG_END')){
                    throw new \Exception("Invalid SIG_END setting or Duplicate call to Root Finalizer");
                } else {
                    \define('_\SIG_END', \microtime(true));
                };
    
                if($error = \error_get_last()){ 
                    \error_clear_last();
                    throw new \ErrorException(
                        $error['message'], 
                        0,
                        $error["type"], 
                        $error["file"], 
                        $error["line"]
                    );
                } 
            } catch(\Throwable $ex) {
                $fault__fn($ex);
            }
    
            try {
                if('exit' == ($_REQUEST['--trap'] ?? null)){
                    throw new \Exception('--trap=exit');
                }
            } catch (\Throwable $ex) {
                $fault__fn($ex);
            }
            
            if(\defined('_\SIG_ABORT') && \_\SIG_ABORT == 0){
                exit();
            }

            try {
                if($exit = $fault__fn()){
                    //fall through
                } else if (\is_null($response = $GLOBALS['--RESPONSE'] ?? null)) {
                    $exit = null;
                } else if($response instanceof \SplFileInfo){
                    $exit = (object)[];
                    if(\is_file($file = $response)){
                        $mime_type = match($ext = \strtolower(\pathinfo($file, PATHINFO_EXTENSION))){
                            'html' => null,
                            'css'  => 'text/css',
                            'js'   => 'application/javascript',
                            'json' => 'application/json',
                            'jpg'  => 'image/jpeg',
                            'png'  => 'image/png',
                            'gif'  => 'image/gif',
                            'html' => 'text/html',
                            'txt'  => 'text/plain',
                            default => \mime_content_type((string) $file) ?: 'application/octet-stream',
                        };
                        if(empty($mime_type)) {
                            $exit->code = 404;
                            $exit->content = '404: Not Found: Unknown Mime Type';
                        } else {
                            // Set appropriate headers
                            $exit->headers[] = 'Content-Type: ' . $mime_type;
                            $exit->headers[] = 'Cache-Control: public, max-age=86400'; // Cache for 1 day
                            $exit->headers[] = 'Expires: ' . \gmdate('D, d M Y H:i:s', \time() + 86400) . ' GMT'; // 1 day in the future
                            $exit->headers[] = 'Last-Modified: ' . \gmdate('D, d M Y H:i:s', \filemtime($file)) . ' GMT';
                            // Check for If-Modified-Since header
                            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
                                \strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= \filemtime($file)) {
                                $exit->code = 304; // Not Modified
                                $exit->content = null;
                            } else {
                                // Output the file content
                                $exit->content = new \SplFileInfo((string) $file);
                            }
                        }
                    } else {
                        $exit->code = 404;
                        $exit->content = '404: Not Found';
                    }
                } else if(\is_scalar($response)){
                    $exit = (object)[
                        'content' => $response,
                    ];
                } else if($response instanceof \Throwable) {
                    ($this->report_fault)($response, "Fault [E0.4]");
                } else if(\is_array($response)) {
                    $exit = (object)[
                        'headers' => [
                            'Content-Type: application/json'
                        ],
                        'content' => $response,
                    ];
                } else if(\is_object($response)) {
                    switch($response->type ?? null){
                        case 'download':{
                            $exit = (object)[];
                            if(!\is_null($content = $response->content ?? null)){
                                $exit->type = $response->type;
                                if(!($response->download_name ?? null)){
                                    $download_name = \basename($file);
                                } else if($response->download_name === true){
                                    $fname = pathinfo($file, PATHINFO_FILENAME);
                                    $download_name = \str_replace('/','-','download-'.date('Y-md-Hi-s')."-{$fname}");
                                } else if(\is_string($response->download_name)){
                                    $download_name = $response->download_name;
                                }
                                $exit->headers[] = "Content-Type: application/octet-stream";
                                $exit->headers[] = "Content-Transfer-Encoding: Binary";
                                $exit->headers[] = "Content-disposition: attachment; filename=\"".$download_name."\"";
                                if($content instanceof \SplFileInfo){
                                    $exit->headers[] = "Content-length: ".(string)(filesize($content));
                                } else if(\is_string($content) && \is_file($content)){
                                    $exit->content = new \SplFileInfo($content);
                                    $exit->headers[] = "Content-length: ".(string)(filesize($content));
                                } else if(\is_scalar($content)) {
                                    $exit->type = 'download-string';
                                    $exit->content = (string) $content;
                                    $exit->headers[] = "Content-length: ".\strlen((string) $content);
                                }
                            } else {
                                $exit->content = 'Unable To Download';
                            }
                        } break;
                        default: {
                            $exit = $response;
                        } break;
                    }
                } else {
                    $exit = null;
                }
                
                //* try and override the exit
                if(\is_object($exit)){
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    if(\is_numeric($code = $exit->code ?? null)){
                        \http_response_code($code ?: 200);
                    } 
                    if(\is_array($exit->headers ?? null)){
                        foreach($exit->headers ?? [] as $k => $v){
                            if(\is_string($v)){
                                if(\is_numeric($k)){
                                    \header($v);    
                                } else {
                                    \header("{$k}: {$v}");
                                }
                            }
                        }
                    }
                    if(\is_null($content = $exit->content ?? null)){ 
                        return; 
                    } else if($content instanceof \SplFileInfo){
                        \readfile($content);
                    } else if(\is_scalar($content)) {
                        echo $content;
                    } else {
                        \header('Content-Type: application/json');
                        echo \json_encode($content ?? [],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    }
                    if($code >= 400){
                        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 1);
                    }
                }
            } catch (\Throwable $ex) {
                $http_code = 500;
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                switch(\_\INTFC){
                    case 'cli':{
                        if($_REQUEST['--verbose'] ?? null){
                            echo "\033[91m\n"
                                .$ex::class.": {$ex->getMessage()}\n"
                                ."File: {$ex->getFile()}\n"
                                ."Line: {$ex->getLine()}\n"
                                ."\033[31m{$ex}\033[0m\n"
                            ;
                        } else {
                            echo "\033[91m{$ex->getMessage()}\033[0m\n";
                        }
                    } break;
                    case 'web':{
                        \http_response_code($http_code);
                        $trace = \json_encode($GLOBALS['_TRACE'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                        $style = <<<HTML
                        <style>
                            body{ background-color: #121212; color: #e0e0e0; font-family: sans-serif; margin: 0; padding: 20px; }
                            pre{ overflow:auto; color:red;border:1px solid red;padding:5px; background-color: #1e1e1e; max-height: calc(100vh-25px); }
                            /* Scrollbar styles for WebKit (Chrome, Edge, Safari) */
                            ::-webkit-scrollbar { width: 12px; height: 12px; }
                            ::-webkit-scrollbar-track { background: #1e1e1e; }
                            ::-webkit-scrollbar-thumb { background-color: #555; border-radius: 6px; border: 2px solid #1e1e1e; }
                            ::-webkit-scrollbar-thumb:hover { background-color: #777; }
                            /* Firefox scrollbar (limited support) */
                            * { scrollbar-width: thin; scrollbar-color: #555 #1e1e1e; }
                        </style>                            
                        HTML;
                        if($_ENV['DX']['VERBOSE'] ?? null){
                            exit(<<<HTML
                            {$style}
                            <pre>Unhandled Exception:
                            <b>{$ex}</b>
                            TRACE: {$trace}
                            </pre>
                            <i>Request: {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}</i>
                            HTML);
                        } else {
                            exit(<<<HTML
                            {$style}
                            <pre>Unhandled Exception:
                            <b>{$ex->getMessage()}</b>
                            </pre>
                            <i>Request: {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}</i>
                            HTML);
                        }
                    } break;
                    default:
                    case 'rest':
                    case 'rpc':
                    case 'api':{
                        \http_response_code(500);
                        \header('Content-Type: application/json');
                        exit(\json_encode([
                            'status' => "error",
                            'message' => $ex->getMessage(),
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    } break;
                }
            }
        }
    });

})();}
#endregion
# ######################################################################################################################
#region CONFIG
namespace {(function(){
    
    global $_;
    global $_TRACE;
    (isset($_) && \is_array($_)) OR $_ = [];
    (isset($_ENV[':ALT']) && \is_array($_ENV[':ALT'])) OR $_ENV[':ALT'] = [];
    (isset($_TRACE) && \is_array($_TRACE)) OR $_TRACE = [];
    \define('_\START_EN', \is_array($a = $_['START_EN'] ?? null) ? $a : []);
    \define('_\SYS_DIR', \str_replace('\\','/',\dirname(__DIR__,2)));
    \define('_\INCP_DIR', \str_replace('\\','/',\realpath(\dirname($_SERVER['SCRIPT_FILENAME']))));
    \define('_\SITE_DIR', (empty($_SERVER['HTTP_HOST'])
        ? \str_replace('\\','/',\realpath($_SERVER['FW__SITE_DIR'] ?? \getcwd()))
        : \_\INCP_DIR
    ));
    \define('_\PHP_TSP_DEFAULTS', [
        'handler' => 'spl_autoload',
        'extensions' => \spl_autoload_extensions(),
        'path' =>  \get_include_path(),
    ]);
    ($_ENV['DBG']['EN'] ?? false) AND \is_file($f = \_\SITE_DIR."/.dbg.php") AND (function($f){
        global $_;
        include $f;
        ($_['DBG']['PRIME_LOGGER']['EN'] ?? false) AND (function(){ 
            $type =\in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE'])
                ? 'action'
                : 'get'
            ;
            $file = \_\SITE_DIR."/.local/dbg/primary_logger-on__{$type}.json";
            \is_dir($d = \dirname($file)) OR \mkdir($d, 0777, true);
            \file_put_contents($file, \json_encode(
                [
                    'getallheaders()' => getallheaders(),
                    'php://input' => \file_get_contents('php://input'),
                    '$_REQUEST' => $_REQUEST,
                    '$_SERVER' => $_SERVER,
                    '$_FILES' => $_FILES,
                    '$_GET' => $_GET,
                    '$_POST' => $_POST,
                    '$_COOKIE' => $_COOKIE,
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ));
        })();
    })($f);    
    
    $intfc = \_\INTFC;
    $lcl__ft = \is_file($f = $lcl__f = \_\SITE_DIR."/.local/.cache-config-{$intfc}.php") ? \filemtime($f) : 0;
    $cfg__ft = \is_file($f = $cfg__f = \_\SITE_DIR."/.env/.config.php") ? \filemtime($f) : 0;
    $ifc__ft = \is_file($f = $ifc__f = \_\SITE_DIR."/.env/.config-{$intfc}.php") ? \filemtime($f) : 0;
    if(
        1
        || $lcl__ft <= $cfg__ft
        || $lcl__ft <= $ifc__ft
        || $lcl__ft <= \filemtime(__FILE__)
        || $lcl__ft <= \filemtime($_SERVER['SCRIPT_FILENAME'])
    ){ 
        global $_;
        $ifc__ft AND include $ifc__f;
        $cfg__ft AND include $cfg__f;
        \is_file($f = \_\SITE_DIR."/.local/.config.php") AND include $f;
        \is_file($f = \_\SITE_DIR."/.local/.config-{$intfc}.php") AND include $f;
        $env = $_;
        $env['LSP']['LIST'] = \iterator_to_array((function(){
            global $_;
            foreach($_['LIBRARIES'] ?? [] as $dx => $en){
                if($en){
                    if(\is_dir($dx)){
                        yield \str_replace('\\','/', \realpath($dx)) => true;
                    } else {
                        $GLOBALS['_TRACE'][] = "Warning: Library directory not found: '".\str_replace('\\','/', $dx)."'";
                    }
                } else {
                    $GLOBALS['_TRACE'][] = "Notice: Library directory disabled: '".\str_replace('\\','/', $dx)."'";
                }
            }
            for (
                $i=0, $dx=\_\SITE_DIR; 
                $dx && $i < 20 ; 
                $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
            ){ 
                if(\is_dir($dy = $dx."/--epx")){
                    yield \str_replace('\\','/', $dy) => true;
                }
            }
            for (
                $i=0, $dx=\_\SYS_DIR; 
                $dx && $i < 20 ; 
                $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
            ){ 
                if(\is_dir($dy = $dx."/--epx")){
                    yield \str_replace('\\','/', $dy) => true;
                }
            }
        })());
        foreach($env['LSP']['LIST'] as $d => $en){
            $GLOBALS['_TRACE'][] = "Library directory included: '".\str_replace('\\','/', $d)."'";
        }
        $MODULE_RESOLVE__FN = function($expr, $auto = null) use($env){
            if(($expr[0]??'')=='/' || ($expr[1]??'')==':'){
                return \str_replace('\\','/', \realpath($expr));
            } else {
                foreach($env['LSP']['LIST'] ?? [] as $d => $en){
                    if($en){
                        if(\is_dir($dy = "{$d}/{$expr}")){
                            return \str_replace('\\','/', \realpath($dy));
                        }
                    }
                }
            }
            if($auto){
                \is_dir($auto) OR \mkdir($d, 0777, true);
                return \realpath($d);
            }
        };
        $modules = [];
        if(!\is_dir(\_\SITE_DIR."/--epx") && \glob(\_\SITE_DIR."/__*", GLOB_ONLYDIR)){
            $modules[\_\SITE_DIR] = true;
        }
        foreach($_['MODULES'] ?? ['app' => true] as $k => $v){
            if($k == 'START'){
                $modules[\_\SYS_DIR] = $v ? true : false;
            } else if($k == 'SITE'){
                $modules[\_\SITE_DIR] = $v ? true : false;
            } else if($v){
                if($dy = ($MODULE_RESOLVE__FN)($k)){
                    $modules[\str_replace('\\','/',$dy)] = true;
                } else {
                    $GLOBALS['_TRACE'][] = "Warning: Module not found: '{$k}'";
                }
            } else {
                $modules[\str_replace('\\','/',$k)] = false;
            }
        }
        foreach(\explode(PATH_SEPARATOR,\get_include_path()) as $v){
            $modules[\str_replace('\\','/', $v)] ??= true;
        }
        foreach($modules as $d => $en){
            if($en){
                $GLOBALS['_TRACE'][] = "Module included: '".\str_replace('\\','/', $d)."'";
            } else {
                $GLOBALS['_TRACE'][] = "Notice: Module disabled: '".\str_replace('\\','/', $d)."'";
            }
        }
        $themes = [];
        foreach($_['THEME'] ?? [] as $k1 => $t){
            if($dy = ($MODULE_RESOLVE__FN)($t)){
                $themes[$k1] = \str_replace('\\','/',$dy);
            } else {
                $GLOBALS['_TRACE'][] = "Warning: Theme not found: '{$t}'";
            }
        }
        $env['THEME'] = $themes;
        $env['TSP']['PATH'] = \implode(PATH_SEPARATOR, \array_keys(\array_filter($modules)));
        $def['LOCAL_DIR'] = \_\SITE_DIR."/.local";
        $def['DATA_DIR'] = ($_['DATA'] ?? false) ? (($MODULE_RESOLVE__FN)($_['DATA']) ?: ((function($p){
            throw new \Exception("Unable to locate data path: '{$p}'");
        }))($_['DATA'])) : (function(){
            \is_dir($d = \_\SITE_DIR."/.local/data") OR \mkdir($d,0777,true);
            return $d;
        })();
        $def['ROOT_DIR'] = (function() use(&$root_dir, &$root_url){
            if(!empty($_SERVER['HTTP_HOST'])){
                $root_url = (function(){
                    return (($_SERVER["REQUEST_SCHEME"] 
                        ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'))
                    ).'://'.$_SERVER["HTTP_HOST"];
                })();
                $root_dir = \str_replace('\\','/', \realpath($_SERVER['DOCUMENT_ROOT']));
                if(\is_file($f = "{$root_dir}/.http-root.php")){
                    include $f;
                }
            } else {
                for (
                    $i=0, $dx=\getcwd(); 
                    $dx && $i < 20 ; 
                    $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
                ){ 
                    if(\is_file($f = "{$dx}/.http-root.php")){
                        include $f;
                        break;
                    }
                }
                if(!$root_dir){
                    $root_dir = \_\SITE_DIR;
                    $root_url = "";
                }
            }
            return $root_dir;
        })();
        $def['SITE_URP'] = $site_urp = (function()use($root_dir, $root_url){
            if(empty($_SERVER['HTTP_HOST'])){
                if($root_url){
                    if(\str_starts_with(\_\SITE_DIR, $root_dir)){
                        return \substr(\_\SITE_DIR, \strlen($root_dir));
                    } else {
                        return false;
                    }
                }
            } else if((\php_sapi_name() == 'cli-server')){
                return '';
            } else {
                $p = \strtok($_SERVER['REQUEST_URI'],'?');
                if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
                    return \substr($p, 0, \strlen($_SERVER['SCRIPT_NAME']));
                } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
                    return '';
                } else {
                    return \substr($p, 0, \strlen($d));
                }
            }
        })();
        $def['ROOT_URL'] = $root_url;
        $def['SITE_URL'] = $site_url = ($root_url  ? \rtrim($root_url.$site_urp,'/') : "");
        $env_export = \str_replace("\n","\n  ", \var_export($env,true));
        $def_export = '';
        foreach($def as $k => $v){
            $def_export .= "\define('_\\{$k}', '{$v}');\n";
        }
        $stamp = \date('Y-md-Hi-s').PHP_EOL;
        $trace = \implode(PHP_EOL."# ", $GLOBALS['_TRACE'] ?? []);
        $contents = <<<PHP
        <?php 
        namespace {
        \$_ENV = \array_replace_recursive(\$_ENV, {$env_export});
        {$def_export}
        }
        # {$trace}
        # {$stamp}
        PHP;
        \is_dir($d = \dirname($lcl__f)) OR \mkdir($d,0777,true);
        \file_put_contents(
            $lcl__f, 
            $contents,
            LOCK_EX // prevents race when testing and you have ton of simultaneous requests
        );
    }

    try{
        // prevents race when testing and you have ton of simultaneous requests
        $handle = fopen($lcl__f, 'r');
        if (flock($handle, LOCK_SH)) {
            try {
                include $lcl__f;
            } finally {
                \flock($handle, LOCK_UN);
            }
        } else {
            throw new \Exception("Cache error");
        }
    } finally {
        fclose($handle);
    }      
    
    
    \set_time_limit($_ENV['TIMELIMIT'] ?? 5);
    //* Default is 'Australia/Adelaide' because thats where epx-php was invented.
    \date_default_timezone_set($_['TIMEZONE'] ?? 'Australia/Adelaide');
    \defined('_\DBG') OR \define('_\DBG', (int) ($_REQUEST['--debug'] ?? $_ENV['DBG']['EN'] ?? 0));
    \defined('_\DBG_') OR \define('_\DBG_',[
        0 => \_\DBG >= 0,
        1 => \_\DBG >= 1,
        2 => \_\DBG >= 2,
        3 => \_\DBG >= 3,
        4 => \_\DBG >= 4,
        5 => \_\DBG >= 5,
        6 => \_\DBG >= 6,
        7 => \_\DBG >= 7,
        8 => \_\DBG >= 8,
        9 => \_\DBG >= 9,
    ]);
    \set_include_path($_ENV['TSP']['PATH']);
    \spl_autoload_extensions("-#{$GLOBALS['INTFC']}.php,/-#{$GLOBALS['INTFC']}.php,-#.php,/-#.php");
    \spl_autoload_register();
    function o(){ static $I; return $I ?? $I = (\class_exists(\_::class) ? \_::_() : new \stdClass); }
})();}
#endregion
# ######################################################################################################################
#region BOOTSTRAP 
namespace {(function(){
    \define('_\SIG_START', \_\MSTART); //always master_start!
    \define('_\REGEX_CLASS_FQN', '/^(([a-zA-Z_\\x80-\\xff][\\\\a-zA-Z0-9_\\x80-\\xff]*)\\\\)?([a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*)$/');
    \define('_\REGEX_CLASS_QN', '/^[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*$/');
    \define('_\IS_CLI', (\_\INTFC === 'cli'));
    \define('_\IS_WEB', (\_\INTFC === 'web'));
    \define('_\IS_HTTP', (\_\INTFC !== 'cli'));
    \define('_\IS_API', (!\_\IS_CLI && !\_\IS_WEB));
    \define('_\IS_HTML', (strpos(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html') !== false));
    \define('_\KEY', \md5($_SERVER['SCRIPT_FILENAME']));
})();}
#endregion
namespace _ { final class request extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    public readonly array $params;
    public readonly array $_;
    
    public static function _() { static $i;  return $i ?: ($i = new static()); }
    
    public function __construct(){
        # ##############################################################################################################
        #region PARSE
        if(!\preg_match(
            "#^/"
                ."(?:"
                    ."(?:"
                        ."(?<facet>"
                            ."(?<portal>(?:__|--)[^/\.]*)"
                            ."(?:\.(?<role>[^/]*))?"
                        .")/?"
                    .")?"
                    ."(?<rpath>"
                        ."(?<comp>[^@]*)(?:(?<cseg>@(?<index>[^/]*))?(?<spath>.*))?"
                    .")"
                .")?"
            . "$#",
            $rurp = ((function(){
                if(empty($_SERVER['HTTP_HOST'])){
                    if(!\str_starts_with(($s = $_SERVER['argv'][1] ?? ''),'-')){
                        return '/'.\ltrim($s,'/');
                    }
                } else {
                    $p = \rtrim(\strtok($_SERVER['REQUEST_URI'],'?'),'/');
                    if((\php_sapi_name() == 'cli-server')){
                        return $p;
                    } else {
                        if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
                            return \substr($p,\strlen($n));
                        } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
                            return $p;
                        } else {
                            return \substr($p,\strlen($d));
                        }
                    }
                }
            })() ?: '/'),
            $m
        )){
            throw new \Exception("Invalid request path format");
        }
        $req['intfc'] = $intfc = ($GLOBALS['INTFC'] ?? (empty($_SERVER['HTTP_HOST']) 
            ? 'cli'
            : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
        ));
        if(!empty($_SERVER['HTTP_HOST'])){
            $req += \parse_url($url = (($_SERVER["REQUEST_SCHEME"] 
                ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'))
            ).'://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
        }
        $req['rurp'] = $rurp;
        $req += \array_filter($m, fn($k) => !is_numeric($k), \ARRAY_FILTER_USE_KEY);
        $req['rpath'] = \trim($req['rpath'], '/');
        $req['comp'] = \trim($req['comp'], '/');
        $req['spath'] = \trim($req['spath'], '/');
        $req['panel'] = \trim(\str_replace('-','_', $req['portal'] ?? null ?: '__'),'/');    
        if(!empty($_SERVER['HTTP_HOST'])){
            $req['url'] = $url;
            $req['method'] = $method = $_SERVER['REQUEST_METHOD'] ?? '';
            # ----------------------------------------------------------------------
            $req['is_supply'] = \preg_match('#(?:-pub|-asset)[/\.]#', $req['rpath']) ? true : false;
            $req['is_get'] = $is_get = !\in_array($method, ['POST','PUT','PATCH','DELETE']);
            $req['action'] = $action = $_REQUEST['--action'] ?? null;
            $req['is_action'] = $is_action = ($action || !$is_get) ? true : false;
            $req['is_view'] = !$is_action;
            $req['referer'] = ($j = $_SERVER['HTTP_REFERER'] ?? null) ? \parse_url($j) : [];
            $req['is_top'] = (($dest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? ($j ? 'document' : null)) === 'document');
            $req['is_frame'] = ($dest == 'iframe');
            $req['is_mine'] = !$j || \str_starts_with($j, $url);
            $req['is_html'] = (\str_contains(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html'));
            $req['is_xhr'] = ('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' ));
            $req['headers'] = \iterator_to_array((function(){
                foreach(\getallheaders() as $k => $v){
                    yield $k => $v;
                }
            })());
            $req['headers']['Accept'] = \explode(',', $req['headers']['Accept'] ?? '');
            $req['agent'] = $agent = (function() use($req){
                if(!\is_null($agent = $req['headers']['Epx-Agent'] ?? null)){
                    return $agent;
                } else if('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' )) {
                    return 'xhr';
                } else {
                    return 'page';
                }
            })();
        }
        $this->_ = $req;
        #endregion
        # ##############################################################################################################
        #region PARAMS
        $this->params = (empty($_SERVER['HTTP_HOST']) 
            ? function(){
                $parsed = [];
                $key = null;
                $args = \array_slice($argv = $_SERVER['argv'] ?? [], 1);
                foreach ($args as $arg) {
                    if ($key !== null) {
                        $parsed[$key] = $arg;
                        $key = null;
                    } else if(\str_starts_with($arg, '-')){
                        if(\str_ends_with($arg, ':')){
                            $key = \substr($arg,0,-1);
                        } else if(\str_contains($arg,':')) {
                            [$k, $v] = \explode(':', $arg);
                            $parsed[$k] = $v;
                        } else {
                            $parsed[$arg] = true;
                        }
                    } else {
                        $parsed[] = $arg;
                    }
                }
                if ($key !== null) {
                    $parsed[$key] = true;
                }
                $parsed[0] ??= '/';
                return $parsed;
            }
            : function(){
                global $_;
                $json = [];
                $files = [];
                switch($content_type = \strtok($_SERVER["CONTENT_TYPE"] ?? '',';')){
                    case "application/json": {
                        $json = (function(){
                            $input = \file_get_contents('php://input');
                            $ox = [];
                            foreach(\json_decode($input, true) as $k => $v){
                                $oy =& $ox;
                                foreach(explode('[',\str_replace("]","", $k)) as $kk){
                                    ($oy[$kk] = []);
                                    $oy = &$oy[$kk];
                                }
                                $oy = $v;
                            }
                            return $ox;
                        })();
                    } break;
                    case "multipart/form-data": {
                        $files = (function(){
                            $o = [];
                            foreach($_FILES as $field => $array){
                                foreach($array as $attrib => $inner){
                                    if(\is_array($inner)){
                                        foreach(($r__fn = function($array, $pfx = '', $ifx = '[', $sfx = ']') use(&$r__fn){
                                            foreach($array as $k  => $v){
                                                if(\is_array($v)){
                                                    yield from ($r__fn)($v,"{$pfx}{$ifx}{$k}{$sfx}",$ifx,$sfx);
                                                } else {
                                                    yield "{$pfx}{$ifx}{$k}{$sfx}" => $v;
                                                }
                                            }
                                        })($inner,$field) as $k => $v){
                                            $o[$k][$attrib] = $v;
                                        }
                                    } else {
                                        $o[$field][$attrib] = $inner;
                                    }
                                }
                            }
                            $ox = [];
                            foreach($o as $k => $v){
                                if(!($v['name'] ?? null)){ continue; }
                                $oy =& $ox;
                                foreach(explode('[',\str_replace("]","", $k)) as $kk){
                                    isset($oy[$kk]) OR $oy[$kk] = [];
                                    $oy = &$oy[$kk];
                                }
                                $oy =  new class($v) extends \SplFileInfo implements \JsonSerializable {
                                    public readonly array $info;
                                    public function __construct($v){
                                        $this->info = $v; 
                                        parent::__construct($v['tmp_name']);
                                    }
                                    public function info($n){
                                        if($n == 'extension'){
                                            return \pathinfo($this->details['name'] ?? '', PATHINFO_EXTENSION);
                                        } else {
                                            return $this->details[$n] ?? null;
                                        }
                                    }
                                    public function jsonSerialize(): mixed {
                                        return "--file::".$this->getRealPath();
                                    }
                                    public function f(){
                                        return \_\i\file::_((string) $this, $this->INFO);
                                    }
                                    public function move_to($path){
                                        \is_dir($d = \dirname($path)) OR \mkdir($d,0777,true);
                                        if(\move_uploaded_file($this, $path)){
                                            return new \SplFileInfo($path);
                                        } else {
                                            return false;
                                        }
                                    }
                                };
                            }
                            return $ox;
                        })();
                    } break;
                    case "application/x-www-form-urlencoded": 
                    default:{
                        //* do nothing
                    } break;
                }
                
                // $_FILES = $files;
                //! warning: array_merge_recursive messes up if $_FILES and $_POST have same key
                return \array_replace_recursive(
                    $_POST, 
                    $_FILES, //* $_FILES is higher priority over $_POST
                    $json,
                    $_GET,
                );
            }
        )();
        $_REQUEST = $this;
        #endregion
        # ##############################################################################################################
    }
    public function route(){
        # ##############################################################################################################
        #region SESSION
        if(empty($_SERVER['HTTP_HOST'])){
            
        } else {
            if(\session_status() == PHP_SESSION_NONE) {
                //* if the primary starter did the session it would have managed the auth
                //* this part will be scipped
                \session_name(\_\KEY); 
                \session_start();
            }
            \define('_\SESSION_PATH', \_\KEY.'/'.\session_id());
            if($this->_['portal'] && \class_exists(\__auth::class)){
                
                $abort__fn = function(int $code, string $message){
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
                    \http_response_code($code);
                    exit($message);
                };
                $redirect__fn = function($goto){
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
                    \header("Location: ".$goto);
                    exit();
                };            
                
                
                \is_array($_SESSION['--AUTH'] ?? null) OR $_SESSION['--AUTH'] = [];
                
                if(($_SESSION['--AUTH']['login_in_progress'] ?? null) == 1){
                    $this->alt_route_path = ['__auth', \_\INTFC."/login"];
                } else {
                    if(($_SESSION['--AUTH']['en'] ?? false) !== true){
                        if(!($_SESSION['--AUTH']['login_in_progress'] ?? false)){
                            $_SESSION['--AUTH'] = [];
                            $_SESSION['--AUTH']['login_in_progress'] = 1;
                            $this->alt_route_path = ['__auth', \_\INTFC."/login"];
                            //$redirect__fn(\strtok($_SERVER['REQUEST_URI'],'?'));
                        }
                    }
                    
                    if(isset($this->alt_route_path)){
                        //* do nothing
                    } else if(
                        isset($_GET['--logout'])
                        || isset($_GET['--signout'])
                    ){
                        $this->alt_route_path = ['__auth', \_\INTFC."/logout"];
                        //$_SESSION['--AUTH'] = [];
                        //$redirect__fn(\strtok($_SERVER['REQUEST_URI'],'?'));
                    } else if(isset($_GET['--switch'])){
                        $this->alt_route_path = ["__auth", \_\INTFC."/facet"];
                    } else if(($auth_option = $_GET['--auth'] ?? null)){
                        $this->alt_route_path = ["__auth", \_\INTFC."/{$auth_option}"];
                    } else {
                        //* let it be
                    }
                }
                
                if(0 AND !empty($this->_['portal']) && empty($this->alt_route_path)){
                    $portal = $this->_['portal'];
                    $portals = $_SESSION['--AUTH']['portals'] ?? [];
                    $role_data = ['permits' => ['*']];
                    if(
                        (([0] ?? '') === '*')
                        || \in_array($portal, $portals)
                    ){
                        //* all ok;
                    } else {
                        $abort__fn(403, '403: Not Allowed (A)');
                    }
                    
                    if(($role_data['permits'][0] ?? null) == '*'){
                        return;
                    }
                    
                    foreach($role_data['permits'] ?? [] as $k => $v){
                        if(\fnmatch($k, $rurp)){
                            return;
                        }
                    }
                        
                    foreach($role_data['exclusions'] ?? [] as $k => $v){
                        if(\fnmatch($k, $rurp)){
                            $abort__fn(403, '403: Not Allowed (C)');
                        }
                    }

                    if($_['is_supply'] ?? null){
                        $abort__fn(403, '403: Not Allowed (D)');
                    }
                }
            }
            isset($_SESSION['--CSRF']) OR $_SESSION['--CSRF'] = \md5(uniqid('csrf-'));
            \define('_\CSRF', $_SESSION['--CSRF']);
            \define('_\FLASH', $_SESSION['--FLASH'] ?? []);
            $_SESSION['--FLASH'] = [];
            if(\_\START_EN['csrf_protect'] ?? true){
                $token = $_REQUEST['--csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                if(
                    \in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE'])
                    && ($token) != ($_SESSION['--CSRF'] ?? null)
                ){
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \http_response_code(406);
                    exit('406: Not Acceptable');
                }
            }
        }
        #endregion
        # ##############################################################################################################
        #region ROUTE
        try {
        
            $INIT__EN = true;
            
            \define('_\MODE', $_ENV['mode'] ?? 'route');
            
            if(\_\MODE == 'standalone'){
                return function(){ };
            }
            
            $resolve_file__fn = function($path, $sfx){
                $sfx = \is_array($sfx) ? $sfx : [$sfx];
                foreach($sfx as $suffix){
                    if($f = (($suffix)
                        ? \stream_resolve_include_path($r[] = "{$path}/{$suffix}") 
                            ?: (\stream_resolve_include_path($r[] = "{$path}{$suffix}")
                        )
                        : \stream_resolve_include_path($r[] = "{$path}")
                    )){
                        return new \SplFileInfo($f);
                    }
                }
            };
            
            $prt_dx__fn = function($exit = false){
                if($exit){
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    !empty($_SERVER['HTTP_HOST']) AND \header('Content-Type: application/json');
                    echo json_encode(
                        [
                            'elapsed' => \number_format((((\defined('_\SIG_END') ? \_\SIG_END : \microtime(true)) - \_\MSTART)),6).'s',
                            'tsp' => \explode(PATH_SEPARATOR, \get_include_path()),
                            'env' => $_ENV,
                            'const' => \get_defined_constants(true)['user'] ?? [],

                        ],
                        \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES
                    );
                    exit();
                } else {
                    echo json_encode(
                        [
                            'env' => $_ENV,
                            'fw' => \array_filter($_SERVER, fn($k) => str_starts_with($k,'FW__'), \ARRAY_FILTER_USE_KEY)
                        ],
                        \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES
                    );
                }
            };
            
            //$_ENV = \_\REQ + $_ENV;
            \define('_\BASE_URL', $base_url = rtrim(\_\SITE_URL."/"
                .(
                    ($this->_['portal'] ?? null ?: '')
                    .'.'.($this->_['role'] ?? null ?: '')
                )
                , 
                '/.'
            ));
            \define('_\CTLR_URL',\rtrim(\_\BASE_URL."/{$this->_['rpath']}",'/'));
            $route_path = $this->alt_route_path ?? null ?: [$this->_['panel'], $this->_['rpath']];

            //$route_path = [$this->_['panel'], $this->_['rpath']];
            $intfc = $this->_['intfc'];
            $tsp = \explode(PATH_SEPARATOR, \get_include_path());
            0 AND ($prt_dx__fn)(true);
            ($_['TRAP']['pre-route'] ?? null) AND ($prt_dx__fn)(true);
            $suffix = ["-@{$intfc}.php", "-@.php", "-@.html"];
            
            if($route_path = $route_path ?? null){
                $__CONTEXT__ = fn() => o();
                $__CTLR_FILE__ = ($resolve_file__fn)(
                    implode('/',\array_map(fn($k) => \trim($k,'/'), $route_path)),
                    $suffix
                );
            } else {
                $__CONTEXT__ = fn() => o();
                $__CTLR_FILE__ = null;
            }
        
            if($__CTLR_FILE__ instanceof \SplFileInfo){
                return (function() use($__CTLR_FILE__){
                    if(\is_callable($o = (include $__CTLR_FILE__))){
                        ($o)();
                    }
                })->bindTo($__CONTEXT__());
            } else {
                $INIT__EN = false;
                return (function() {
                    $route_path = ($_REQUEST->alt_route_path ?? null) 
                        ? ('('.implode('/',\array_map(fn($k) => \trim($k,'/'), $_REQUEST->alt_route_path)).')')
                        : ''
                    ;
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
                    if(empty($_SERVER['HTTP_HOST'])){
                        echo "\e[31m404 Not Found: \e[91m{$_REQUEST->_['rurp']}\e[0m\n";
                    } else {
                        \http_response_code(404);
                        while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                        echo "404 Not Found: {$_REQUEST->_['rurp']} {$route_path}";
                    }
                    exit(404);
                })->bindTo($__CONTEXT__());
            }
            
        } finally {
            
            if($INIT__EN){

                if($panel = $route_path[0] ?? null){
                    
                    if($f = \stream_resolve_include_path("{$panel}/.panel.php")){
                        include $f;
                    }
                    
                    \set_include_path(\trim(
                        ($_ENV['THEME'][$panel] ?? $_ENV['THEME']['*'] ?? '').PATH_SEPARATOR
                        .\get_include_path(),PATH_SEPARATOR
                    ));
                }

                $tsp = \explode(PATH_SEPARATOR,get_include_path());
                
                foreach($tsp as $d){
                    \is_file($f = "{$d}/.functions.php") AND include_once $f;
                }
                
                foreach(\array_reverse($tsp) as $d){
                    \is_file($f = "{$d}/.module.php") AND include_once $f;
                }
                
                foreach($_ENV['AUTO_INITS'][\_\INTFC][\_\MODE] ?? [] as $k => $v){
                    if(\is_numeric($k)){
                        \o()->$v;
                    } else {
                        $v && o()->$k;
                    }
                }
            }
        }
        #endregion
        # ##############################################################################################################
    }
    
    public function trap($trap = 0){
        
        $trap AND (function(){
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            !empty($_SERVER['HTTP_HOST']) AND \header('Content-Type: application/json');
            echo json_encode(
                [
                    'elapsed' => \number_format((((\defined('_\SIG_END') ? \_\SIG_END : \microtime(true)) - \_\MSTART)),6).'s',
                    'tsp' => \explode(PATH_SEPARATOR, \get_include_path()),
                    'env' => $_ENV,
                    'const' => \get_defined_constants(true)['user'] ?? [],
                    'request' => $_REQUEST,

                ],
                \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES
            );
            exit();        
        })();
        return $this;
    }
    
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return isset($this->params[$n]);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        return $this->params[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return (array) $this;
    }
}}
namespace { 
    
    return \_\request::_()->trap(0)->route();  
    
}