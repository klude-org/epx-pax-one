<?php namespace _\theme\studio;

class plat {
    
    use \_\i\singleton__t;
    
    public function prt(){
        // Detect if this request is loading into an <iframe>
        $dest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
        $is_iframe = ($dest === 'iframe') || (isset($_GET['embed']) && $_GET['embed'] === '1');
        // Ensure proxies/CDNs cache separately by destination
        header('Vary: Sec-Fetch-Dest', false);

        if($is_iframe){
            \_\view::_("#")();
        } else {
            include '-v.php';
        }
    }
    
}