<?php namespace _\i\fault;

class exception extends \Exception implements \JsonSerializable {
    
    protected $details = [];
    
    public static function _(string $message = "", int $code = 0, ?\Throwable $previous = null, array $details = []) { 
        return new static($message, $code, $previous,$details); 
    }
   
    public function __construct($message, $code = 0, ?\Throwable $previous = null, array $details = []) {
        $this->details = $details;
        parent::__construct($message, $code, $previous);
    }
    
    public function getDetails(){
        return $this->details;
    }
    
    public function jsonSerialize():mixed{
        return \iterator_to_array((function(){
            foreach($this->fault_stack() as $ex){
                yield [
                    'class' => $ex::class,
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'details' => \method_exists($ex,'getDetails') ? $ex->getDetails() : null,
                    'trace'    => array_map(
                        function($v){ 
                            return \str_replace('\\','/', $v); 
                        }, 
                        explode("\n", $ex->getTraceAsString())
                    ),
                ];
            }
        })());
    }
    
    public function fault_stack(){
        return \array_reverse(\iterator_to_array((function(){
            $ex = $this;
            for($i = 0, $ex = $this; $ex && $i < 5; $i++, $ex = $ex->getPrevious()){
                yield $ex;
            }
        })()));
    }
    
    public function compile_formatted($ex){
        $ret = new \stdClass();
        $trace = $ex->getTrace();
        $count = count($trace) - 1;
        $s_trace = [];
        $k = 0;
        $point_back = $this->details['point_back'] ?? 0;
        $point_back = ($point_back > $count) 
            ? $count 
            : $point_back
        ;
        foreach($trace as $k => $v){
            $f_file = ($n_file = $v['file'] ?? null) ? '': '<i style="color:green">'.$n_file.'</i>';
            $f_line = ($n_line = $v['line'] ?? null) ? '': '<i style="color:green">'.$n_line.'</i>';
            //$f_href = \urlencode('vscode://file/'.str_replace('\\','/',$v['file']).':'.$v['line']);
            if($n_file && $n_line){
                $f_href = '?'.\http_build_query(['--IDE' => 'vscode', "file" => str_replace('\\','/',$n_file), "line" => $n_line]);
            } else {
                $f_href = "javascript:;";
            }
            $f_class = empty($v['class']) ? '': '<i style="color:blue">'.$v['class'].'</i>';
            $f_type = $v['type'] ?? '';
            $f_func = empty($v['function']) ? '': '<i style="color:orange">'.$v['function'].'</i>';;
            $args = [];
            foreach($v['args'] as $x){
                if(is_scalar($x)){
                    $x = json_encode($x);
                    if(is_string($x)){
                        if(strlen($x) > 10){
                            $args [] = '<i style="color:#cc0000">"'.substr($x,0,9).'..."</i>';
                        } else {
                            $args [] = '<i style="color:#cc0000">"'.$x.'"</i>';
                        }
                    } else {
                        $args [] = '<i style="color:#cc0000">'.$x.'</i>';
                    }
                } else if(is_object($x)){
                    $args [] = '<i style="color:blue">{'.get_class($x).'}</i>';
                } else if(is_array($x)){
                    $args [] = '<i style="color:black">[...]</i>';
                } else {
                    $args [] = '<i style="color:green">'.gettype($x).'</i>';
                }
            }
            $f_args = implode(', ',$args);
            if($k == $point_back){
                $prefix = '<span style="cursor:pointer; border:1px solid red; text-decoration: none;" onclick="fetch(\''.$f_href.'\')">';
                $postfix = '</span>';
                $ret->file = $v['file'];
                $ret->line = $v['line'];
            } else {
                $prefix = '<span style="cursor:pointer; text-decoration: none;" onclick="fetch(\''.$f_href.'\')">';
                $postfix = '</span>';
            }
            
            $s_trace [] = "{$prefix}#{$k} {$f_file}({$f_line}): {$f_class}{$f_type}{$f_func}({$f_args}){$postfix}";
        }
        $k++;
        $s_trace [] = '#'.$k.'<i style="color:green"> {main}</i>';
        $ret->trace = implode("\n", $s_trace);
        return $ret;
    }
    
    public function as_text($type = false){
        switch($type){
            default: {
                return parent::__toString();
            } break;
            case 'rich':{
                $text = '';
                foreach($this->fault_stack() as $ex){
                    $formatted = $this->compile_formatted($ex);
                    $text .= '<pre>'.
                        "\n".'<b style="color:red">'.$ex->getMessage()."</b>".
                        "\nFile: ".'<b style="color:red">'.$formatted->file."</b>".
                        "\nLine: ".'<b style="color:red">'.$formatted->line."</b>".
                        "\nException: ".'<b style="color:red">'.static::class."</b>".
                        "\nStackTrace:\n".$formatted->trace.
                        '</pre>'
                    ;
                }
                return $text;
            } break;
            case 'simple':{
                return '<pre>'.
                    "\nException ".static::class.":".$this->getMessage().
                    "\nFile:".$this->getFile().
                    "\nLine:".$this->getLine().
                    "\nStackTrace:\n".$this->getTraceAsString().
                    '</pre>'
                ;
            } break;
            case 'std':{
                return '<pre>'.$this.'</pre>';
            } break;
        }
    }    
    
    public function __toString() {
        return $this->as_text($_ENV['DBG']['print'] ?? 'rich');
    }
}