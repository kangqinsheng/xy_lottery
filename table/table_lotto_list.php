<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 11:49
 */

class table_lotto_list extends discuz_table
{
    public function __construct(){
        $this->_table = 'lotto_list';
        $this->_pk = 'id';
        parent::__construct();
    }
    //查看用户某日抽奖次数
    public function get_count($id,$day){
        $res = DB::result_first("select count(*) from %t where `uid`=%i and `lotto_day`=%i",array($this->_table,$id,$day));
        return $res;
    }
    //添加抽奖记录
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //抽奖次数
    public function lotto_all(){
        $res = DB::result_first("select count(*) from %t",array($this->_table));
        return $res;
    }
    //今日参与人数
    public function lotto_person(){
        $res = DB::result_first("select count(distinct `uid`) from %t",array($this->_table));
        return $res;
    }
    //参与人数
    public function lotto_person_now($now){
        $res = DB::result_first("select count(distinct `uid`) from %t where `lotto_day`=%i",array($this->_table,$now));
        return $res;
    }
}