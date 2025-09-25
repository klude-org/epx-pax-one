::<?php echo "\r   \r"; if(0): ?>
:: Installed: #__FW_INSTALLED__#
:: #####################################################################################################################
:: #region LICENSE
::     /* 
::                                                EPX-WIN-SHELL
::     PROVIDER : KLUDE PTY LTD
::     PACKAGE  : EPX-PAX
::     AUTHOR   : BRIAN PINTO
::     RELEASED : 2025-03-10
::     
::     The MIT License
::     
::     Copyright (c) 2017-2025 Klude Pty Ltd. https://klude.com.au
::     
::     of this software and associated documentation files (the "Software"), to deal
::     in the Software without restriction, including without limitation the rights
::     to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
::     copies of the Software, and to permit persons to whom the Software is
::     furnished to do so, subject to the following conditions:
::     
::     The above copyright notice and this permission notice shall be included in
::     all copies or substantial portions of the Software.
::     
::     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
::     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
::     FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
::     AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
::     LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
::     OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
::     THE SOFTWARE.
::         
::     */
:: #endregion
:: # ###################################################################################################################
:: # i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
@echo off
:: Set variables
C:/xampp/current/php__xdbg/php.exe "%~f0" %*
:: php "%~f0" %*
exit /b 0
<?php endif; 
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
        $root_dir = $site_dir;
        $root_url = "";
    }
}
$browser_exe = $argv[1] ?? 'msedge';
if($root_url){
    $url = \str_replace('\\','/', $root_url.\substr(getcwd(),\strlen($root_dir)));
    \system("start {$browser_exe} {$url}");
}