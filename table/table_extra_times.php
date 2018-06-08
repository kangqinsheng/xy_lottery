<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 11:50
 */

class table_extra_times extends discuz_table
{
    public function __construct(){
        $this->_table = 'extra_times';
        $this->_pk = 'id';
        parent::__construct();
    }
    //某人某天额外次数
    public function extra_time($id,$day){
        $res = DB::result_first("select `extra_times` from %t where `u_id`=%i and `extra_times_day`=%i",array($this->_table,$id,$day));
        return $res;
    }
    //添加额外中奖
    public function add_extra($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //更新中奖次数
    public function update_extra($uid,$times,$day){
        $res = DB::query("update ".$this->_table." set `extra_times`=`extra_times`+{$times} where `u_id`={$uid} and `extra_times_day`={$day}");
        return $res;
    }
}