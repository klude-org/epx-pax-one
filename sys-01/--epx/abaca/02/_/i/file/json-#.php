<?php namespace _\i\file;

final class json {

    use \_\i\instance__t;
    
    public readonly \_\i\file $file;
    public int $encode_options = 0;
    public int $depth = 512;
    public $write_result = null;
    public bool $decode_assoc = false;
    public int $decode_options = 0;
    public ?array $decode_error = null;
    
    private function __construct($file){
        $this->file = $file;
    }
    
    public function write($object){ 
        $this->file->ensure_dir();
        $this->write_result = \file_put_contents($this->file, \json_encode($object, $this->encode_options, $this->depth));
        return $this;
    }
    
    public function encode_pretty(){
        $this->encode_options |= JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        return $this;
    }
    
    public function decode_assoc($en = true){
        $this->decode_assoc = $en;
        return $this;
    }
    
    public function read(){
        if($this->file->exists()){
            if($contents = \file_get_contents($this->file)){
                try {
                    return json_decode($contents, $this->decode_assoc, $this->depth, $this->decode_options);
                } finally {
                    if($e = \json_last_error()) {
                        $this->decode_error = [
                            'json_code' => $e,
                            'message' => \json_last_error_msg() ?: 'Unknown Error',
                        ];
                    }
                }
            }
        } else {
            $this->decode_error = [
                'missing_file' => true,
                'message' => 'file not found',
            ];
        }
    }
    
    
}