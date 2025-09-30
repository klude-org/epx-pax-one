<?php 

(new class() extends \stdClass{
    
    public function __invoke(){
        if($_REQUEST->_['is_action']){
            switch($_POST['--action'] ?? false){
                case "Save":{
                    $this->env_data__submit(function(&$ff){
                        $ff = array_replace_recursive($ff, $_POST['ff']);
                        ($_ENV['DB_HOSTNAME'] ?? null) OR $_ENV['DB_HOSTNAME'] = null;
                        ($_ENV['DB_USERNAME'] ?? null) OR $_ENV['DB_USERNAME'] = null;
                        ($_ENV['DB_PASSWORD'] ?? null) OR $_ENV['DB_PASSWORD'] = null;
                        ($_ENV['DB_DATABASE'] ?? null) OR $_ENV['DB_DATABASE'] = null;
                        ($_ENV['DB_CHAR_SET'] ?? null) OR $_ENV['DB_CHAR_SET'] = null;
                    });
                } break;
                case 'Mount':{
                    $this->db()->execute(include $this->model()->file('schema','-sql.php'));
                } break; 
                case 'Unmount':{
                    $this->db__backup();
                    //$this->db()->execute("DROP TABLE `{$this->tblp()}`");
                } break;
                case 'Backup':{
                    $this->db__backup();
                } break;
                case 'Restore':{
                    $this->db__restore();
                } break;
                case 'Remove':{
                    $this->db__backup_remove();
                } break;
                case 'Load':{
                    $this->db__load();
                } break;
                case 'Clear':{
                    $this->db__clear();
                } break;
                case 'Download':{
                    $this->db__backup_download();
                }
            }
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
            
        } else { 
            
            if($this->db()->connect()){
                $this->db_is_connected = true;
                $this->alert_type = 'success';
                if($dbname = $this->db()->database_name()){
                    $this->alert_message = "Connected To Database <strong>{$dbname}</strong>";
                } else {
                    $this->alert_message = "Connected To Server (Database not specified!)";
                }
            } else {
                $this->db_is_connected = false;
                $this->alert_type = 'danger';
                $this->alert_message = $this->db()->last_error_message();
            }
            
            \_\view('.')->o($this)();
        }
    }
    

    private function db(){
        return $this->DB ?? ($this->DB = new class {
            private $pdo;
            private $dir__i;
            private $database__i = '';
            private $hostname__i = '';
            private $char_set__i = '';
            private $last_error_message;
            public function __construct(){
                $this->dir__i = \_\p(\_\DATA_DIR."/db");
            }
            public function pdo(){
                $hostname = \_\e('DB_HOSTNAME') ??  'localhost';
                $database = \_\e('DB_DATABASE') ??  '';
                $char_set = \_\e('DB_CHAR_SET') ??  'utf8mb4';
                $username = \_\e('DB_USERNAME') ??  'root';
                $password = \_\e('DB_PASSWORD') ??  '';
                if(!$this->pdo){
                    try {
                        $this->pdo = new \PDO(
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
                }
                return $this->pdo;
            }
            public function last_error_message(){
                return $this->last_error_message;
            }
            public function database_name(){
                return \_\e('DB_DATABASE', '');
            }
            public function connect(){
                try{
                    $this->last_error_message = null;
                    if($this->pdo()){
                        return true;
                    }
                } catch (\PDOException $ex){
                    $this->last_error_message = $ex->getMessage();
                }
                return false;
            }
            public function __call($name, $args){
                if(\method_exists($this->pdo ?? $this->pdo(),$name)){
                    return $this->pdo->$name(...$args);
                } else {
                    throw new \Exception("Method Not Found '{$name}'");
                }
            }
        });        
    }

    private function db__list_tables(){
        return $this->db()->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);        
    }
    
    private function db__clear(){
        try{
            $this->db()->query('SET foreign_key_checks = 0');
            foreach($this->db__list_tables() as $tblp){
                $this->db()->exec("DROP TABLE IF EXISTS `{$tblp}`");
            }
        } finally {
            $this->db()->query('SET foreign_key_checks = 1');
        }
    }
    
    private function db__mount(){
        //creates the database and loads the backed up copy
    }
    
    private function db__unmount(){
        //takes the backup copy and drops the database
    }
    
    private function db__install(){
        //takes the backup copy and drops the database
    }
    
    private function db__uninstall(){
        //takes the backup copy and drops the database
    }
    
    private function db__get_schema(){
        $table = [];
        foreach($this->db__list_tables() as $tblp){
            $table_create =   $this->db()->query("SHOW CREATE TABLE `$tblp`")->fetch(\PDO::FETCH_ASSOC);
            $table[$tblp]['fields'] = [];
            $table[$tblp]['constraint'] = [];
            $table[$tblp]['keys'] = [];
            $table[$tblp]['ainc'] = '';
            $table[$tblp]['last'] = '';
            $table[$tblp]['first'] = '';
            foreach(\explode("\n", $table_create['Create Table']) as $v){
                //echo "--".$v.'<br>';
                $x = \trim($v);
                if(\str_starts_with($x, 'CREATE TABLE')){
                    $table[$tblp]['first'] = $x;
                } else if(\str_starts_with($x, '`')){
                    if(\str_contains($x,'AUTO_INCREMENT')){
                        $table[$tblp]['fields'][] = str_replace(' AUTO_INCREMENT','',\rtrim($x," \t,"));
                        $table[$tblp]['ainc'] = "MODIFY ".\rtrim($x," \t,").', AUTO_INCREMENT=0'; 
                    } else {
                        $table[$tblp]['fields'][] = \rtrim($x," \t,");
                    }
                } else if(\str_starts_with($x, ')')){
                    $table[$tblp]['last'] = \preg_replace("#AUTO_INCREMENT=\d+ #",'',$x);
                } else if(\str_starts_with($x, 'CONSTRAINT')){
                    $table[$tblp]['constraint'][] = "ADD ".\rtrim($x," \t,");
                } else {
                    $table[$tblp]['keys'][] = "ADD ".\rtrim($x," \t,");
                }
            }
        }
        $schema_1 = '';
        $schema_2 = '';
        $schema_3 = '';
        $schema_4 = '';
        foreach($table as $tblp => $v){
            $schema_1.= "\n\n{$v['first']}\n  ";
            $schema_1.= \implode(",\n  ", $v['fields']);
            $schema_1.= "\n{$v['last']};\n";
            if($v['keys']){
                $schema_2.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_2.= "  ".\implode(",\n  ", $v['keys']);
                $schema_2.="\n;";
            }
            if($v['ainc']){
                $schema_3.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_3.= "  {$v['ainc']}";
                $schema_3.="\n;";
            }
            if($v['constraint']){
                $schema_4.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_4.= "  ".\implode(",\n  ", $v['constraint']);
                $schema_4.="\n;";
            }
        }
        return \trim("{$schema_1}\n\n{$schema_2}\n\n{$schema_3}\n\n{$schema_4}");
    }
    
    private function db__backup_remove(){
        if($f = $_REQUEST['file'] ?? null){
            if(\is_file($f = \_\DATA_DIR."/{$f}")){
                \unlink($f);
            }
        }
    }
    
    private function db__backup_download(){
        if($f = $_REQUEST['file'] ?? null){
            if(\is_file($f = \_\DATA_DIR."/{$f}")){
                \_\download($f);
            }
        }
    }
    
    private function db__backup(array $options = []){
        if($this->db__list_tables()){
            $clean = true;
            $dated_backup = true;
            \extract($options);
            $data = [];
            $schema = $this->db__get_schema();
            foreach($this->db__list_tables() as $tblp){
                $data[$tblp] = $this->db()->query("SELECT * FROM `{$tblp}`")->fetchAll();    
            }
            $date = \date("Y-md-Hi-s");
            $file = \_\DATA_DIR."/db-backup-{$date}.json";
            $file_latest = \_\DATA_DIR."/db-backup.json";
            $db['schema'] = $schema;
            $db['data'] = $data;
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            $x = json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            \file_put_contents($file_latest, $x);
            if($dated_backup){
                \file_put_contents($file, $x);
            }
            $schema_file = \_\DATA_DIR."/db-schema.sql";
            \file_put_contents($schema_file, $schema);
        }
    }
    
    private function db__restore(){
        if($f = $_REQUEST['file'] ?? null){
            if(\is_file($f = \_\DATA_DIR."/{$f}")){
                if($this->db__list_tables() && $f != \_\DATA_DIR."/db-backup.json"){
                    $this->db__backup(['dated_backup' => false]);
                }
                $this->db__load($f);
            }
        }
    }
    
    private function db__load($file = null){
        if(\is_file($file = $file ?? \_\DATA_DIR."/db-backup.json")){
            $this->db__clear();
            try{
                $this->db()->query('SET foreign_key_checks = 0');
                $db = json_decode(\file_get_contents($file), true);
                $schema = $db['schema'];
                echo $schema;
                $this->db()->exec($schema);
                foreach($db['data'] as $tblp => $data){
                    foreach($data as $record){
                        if($record){
                            $keys = array_keys($record);
                            $l1 = "`".implode('`, `',$keys)."`";
                            $l2 = ":".implode(', :',$keys);
                            $sql = "INSERT INTO `{$tblp}` ({$l1}) VALUES ({$l2})";
                        } else {
                            $sql = "INSERT INTO `{$tblp}` () VALUES();";
                        }
                        $this->db()->prepare($sql)->execute($record);
                    }
                }
            } finally {
                $this->db()->query('SET foreign_key_checks = 1');
            }
        }
    }
        
    
})();