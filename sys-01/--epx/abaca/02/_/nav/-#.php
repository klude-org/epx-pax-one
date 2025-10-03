<?php namespace _;

class nav {
    
    public static function portal_nav(){
        return \_\dob("{$_REQUEST->_['panel']}/nav") ?? \_\nav\url_tree::portal_tree();
    }

    public static function control_list__attributed(){
        $list = [];
        if(\class_exists(\Attribute::class)){ //only for php 8.+
            foreach(($r = new \ReflectionObject(\_\nav\controller::_()))->getMethods() as $m) {
                if($a = $m->getAttributes(\_\nav\controller\control__a::class)[0] ?? null){
                    $list[$m->getName()] = $a->newInstance();
                }
            }
        }
        return $list;
    }
    
    public static function control_list(){
        $list = [];
        if(\class_exists(\Attribute::class)){ //only for php 8.+
            foreach(($r = new \ReflectionObject(\_\nav\controller::_()))->getMethods() as $m) {
                $m_name = $m->getName();
                if($a = $m->getAttributes(\_\nav\controller\control__a::class)[0] ?? null){
                    $list[$m_name] = $a->newInstance()->props();
                } else if(\str_starts_with($m_name,'c__')) {
                    $list[$n = \substr($m_name, 3)] = [
                        'label' => $n,
                    ];
                }
            }
        }
        return $list;
    }
    
    public static function control_tree(){
        $nav = [];
        $x = \_\linq(static::control_list())
            ->where(function($o){ return $o->en ?? true; })
            ->map(function($o,$k){ 
                return 
                    [ 'key' => $k, 'rpth' => ":.{$k}" ] + $o
                ; 
            })
            ->order_by('category', SORT_ASC)
            ->select('key', 'label','category','rpth','description')
            ->group_by('category')
            ->to_array()
        ;
        $uncat = $x[''] ?? [];
        unset($x['']);
        $x[''] = $uncat;
        $catindex = 0;
        foreach($x as $category => $controls){
            $nav[++$catindex]['desc'] = $category ?: '.';
            foreach($controls as $k => $v){
                $nav[$catindex]['urls'][] = [ 
                    'label' => $v['label'] ?: $k, 
                    'key' => $k,
                    'url' => \_\u($v['rpth']), 
                    'desc' => $v['description'] ?? ''
                ];
            }
        }
        return $nav;
    }

}