<?php 

$list = ['' => '']; // remove the directly link
if(\_\db()->is_connected()){
    foreach(\_\db()->table__list() as $table_name){
        $list['/'.$table_name] = $this->get_url($panel, $path, "?table={$table_name}");
    }
}

return $list;