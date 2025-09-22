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
    global $BOOT;
    $BOOT ??= "epx__pax_boot/v250922_03";
    if(!\is_file($localfile = \_\START_DIR."/".($file = \str_replace('\\','/',$BOOT).'/.boot.php'))){
        global $REMOTE_BR;
        $REMOTE_BR = "250922-01-dev";
        $url = "https://raw.githubusercontent.com/klude-org/epx-pax-one/{$REMOTE_BR}/plugins/".\urlencode($file);
        if(!($contents = \file_get_contents($url))){
            empty($_SERVER['HTTP_HOST']) OR \http_response_code(500);
            echo "500: Failed to download: {$BOOT}".PHP_EOL;
            exit();
        }
        \is_dir($d = \dirname($localfile)) OR \mkdir($d, 0777, true);
        \file_put_contents($localfile, $contents);
        if(!\is_file($localfile)){
            empty($_SERVER['HTTP_HOST']) OR \http_response_code(500);
            echo "500: Failed to locate: {$BOOT}".PHP_EOL;
            exit();
        } 
    };
    return include $localfile;
    
})(); }
