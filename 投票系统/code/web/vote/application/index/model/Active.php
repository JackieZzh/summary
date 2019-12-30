<?php
namespace app\index\model;

use think\Model;

class Active extends Model{

    protected $name = "vote_active_list";

    static function getActiveInfo($field, $id){
        $where['id'] = $id;
        $where['is_show'] = 1;
        $where['is_del'] = 1;
        $res = self::field($field)->where($where)->find($id);
        if (!empty($res)){
            $res = $res->toArray();
        }
        return $res;
    }
}