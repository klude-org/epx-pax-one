<?php return function($path,$d = null){
    $flist = [];
    if(\str_ends_with($path,'.php')){
        $flist[] = $path;
    } else {
        $flist[] = "{$path}/--epx/.start.php";
        $flist[] = "{$path}/.start.php";
        $flist[] = "{$path}/.start-php";
    }
    $d ??= \dirname($_SERVER['SCRIPT_FILENAME']);
    $found = false;
    while ($d && $d !== dirname($d)) {
        foreach($flist as $f){
            if(\file_exists($p = "{$d}/{$f}")){
                $found = $p;
                break 2;
            }
        }
        $d = dirname($d);
    }
    if(!\is_file($found)){
        try{
            throw new \Exception("Unable to locate start - {$f}");
        } catch (\Throwable $ex) {
            if(empty($_SERVER['HTTP_HOST'])){
                echo "\033[91m\n"
                    .$ex::class.": {$ex->getMessage()}\n"
                    ."File: {$ex->getFile()}\n"
                    ."Line: {$ex->getLine()}\n"
                    ."\033[31m{$ex}\033[0m\n"
                ;
            } else {
                \http_response_code(500);
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                \header('Content-Type: application/json');
                echo \json_encode([
                    'status' => "error",
                    'message' => $ex->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                exit(1);
            }
        }
    }
    return include $found;    
};