<?php
namespace app\index\model;

use mysql_xdevapi\Collection;
use think\Model;

class Participant extends Model{

    protected $name = "vote_active_participant";

    static function getParticipant($id)
    {
        $where['active_id'] = $id;
        $where['is_show'] = 1;
        $where['is_del'] = 1;
        $res = self::all(function($query)use($id, $where){
            $query->where($where)->field('id, nick_name, real_name, age, introduction, avatar_url, votes')->order('id', 'asc');
        });
        return collection($res)->toArray();
    }

    static function updateParticipant($where){
        return self::where($where)->setInc('votes');
    }

    static function checkParticipant($where){
        return self::where($where)->value('id');
    }

    static function getLeaderBoard($field, $where, $limit){
        $res = self::all(function($query)use($field, $where, $limit){
            $query->where($where)->field($field)->order('votes','desc')->limit($limit);
        });
        return collection($res)->toArray();
    }

}