<?php 
########################################################################################################################
#region
    /* 
                                               EPX-PAX-START
    PROVIDER : KLUDE PTY LTD
    PACKAGE  : EPX-PAX
    AUTHOR   : BRIAN PINTO
    RELEASED : 2025-07-04
    
    Copyright (c) 2017-2023 Klude Pty Ltd. https://klude.com.au

    The MIT License

    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    
    */
#endregion
# ######################################################################################################################
# i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
# ######################################################################################################################
#region START
namespace _ { return (function(){

    \defined('_\PSTART') OR \define('_\PSTART', \microtime(true));
    \define('_\START_FILE', \str_replace('\\','/', __FILE__));
    \define('_\START_DIR', \dirname(\_\START_FILE));
    
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    0 AND \error_reporting(E_ALL);

    $fault__fn = function($ex = null){
        $intfc = (\defined('_\INTFC') ? \_\INTFC : null)
            ?? $GLOBALS['INTFC']
            ?? (empty($_SERVER['HTTP_HOST']) 
                ? 'cli'
                : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
            )
        ;
        switch($intfc){
            case 'cli':{
                echo "\033[91m\n"
                    .$ex::class.": {$ex->getMessage()}\n"
                    ."File: {$ex->getFile()}\n"
                    ."Line: {$ex->getLine()}\n"
                    ."\033[31m{$ex}\033[0m\n"
                ;
                exit(1);
            } break;
            case 'web':{
                \http_response_code(500);
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                echo <<<HTML
                    <style>
                        body{ background-color: #121212; color: #e0e0e0; font-family: sans-serif; margin: 0; padding: 20px;}
                        pre{ overflow:auto; color:red;border:1px solid red;padding:5px; background-color: #1e1e1e; max-height: calc(100vh-25px); }
                        /* Scrollbar styles for WebKit (Chrome, Edge, Safari) */
                        ::-webkit-scrollbar { width: 12px; height: 12px;}
                        ::-webkit-scrollbar-track { background: #1e1e1e; }
                        ::-webkit-scrollbar-thumb { background-color: #555; border-radius: 6px; border: 2px solid #1e1e1e; }
                        ::-webkit-scrollbar-thumb:hover { background-color: #777; }
                        /* Firefox scrollbar (limited support) */
                        * { scrollbar-width: thin; scrollbar-color: #555 #1e1e1e;}
                    </style>
                    <pre>{$ex}</pre>
                HTML;
                exit(1);
            } break;
            default:{
                \http_response_code(500);
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                \header('Content-Type: application/json');
                echo \json_encode([
                    'status' => "error",
                    'message' => $ex->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
                exit(1);
            } break;
        }
    };
    \set_exception_handler(function($ex) use($fault__fn){
        $fault__fn($ex);
    });
    \set_error_handler(function($severity, $message, $file, $line) use($fault__fn){
        try{
            throw new \ErrorException(
                $message, 
                0,
                $severity, 
                $file, 
                $line
            );
        } catch(\Throwable $ex) {
            $fault__fn($ex);
        }
    });
    
    $BOOT = $_['BOOT'] ?? null ?: "sys-01/--epx/boot/01";
    if($start = \realpath($d = \dirname(__DIR__)."/{$BOOT}/.boot.php")){
        return include $start;    
    } else {
        empty($_SERVER['HTTP_HOST']) OR \http_response_code(500);
        echo "500: Failed to locate boot: {$d}".PHP_EOL;
        exit();
    }
    
    
})(); }    