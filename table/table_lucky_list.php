<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 11:49
 */

class table_lucky_list extends discuz_table
{
    public function __construct(){
        $this->_table = 'lucky_list';
        $this->_pk = 'id';
        parent::__construct();
    }
    //抽取对应奖品已中数量数量
    public function get_yizhong($id){
        $res = DB::fetch_first("select count(*) as 'count' from %t where `prize_id`=%i",array($this->_table,$id));
        return $res;
    }
    //添加中奖记录
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //中奖总条数
    public function get_count(){
        $res = DB::result_first("select count(*) from %t",array($this->_table));
        return $res;
    }
    //中奖分页
    public function get_limit($start,$size){
        $data = DB::fetch_all("select * from %t order by `luck_time` desc limit $start,$size",array($this->_table));
        return $data;
    }
    //中奖条数
    public function get_newest($limit){
        $data = DB::fetch_all("select * from %t order by `luck_time` desc limit 0,$limit",array($this->_table));
        return $data;
    }
    //更新状态
    public function update_data($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
    //通过用户名搜索
    public function get_by_uname($name){
        $data = DB::fetch_all("select * from %t where `luck_name` LIKE %s order by `dh_status` asc ",array($this->_table,'%'.$name.'%'));
        return $data;
    }
    //通过电话搜索
    public function get_by_uphone($phone){
        $data = DB::fetch_all("select * from %t where `luck_phone` LIKE %s order by `dh_status` asc ",array($this->_table,'%'.$phone.'%'));
        return $data;
    }
    //通过奖品名搜索
    public function get_by_pname($prize){
        $data = DB::fetch_all("select * from %t where `price_name` LIKE %s order by `dh_status` asc ",array($this->_table,'%'.$prize.'%'));
        return $data;
    }
    //获取对应单条数据
    public function get_one($id){
        $data = DB::fetch_first("select * from %t where `id`=%i",array($this->_table,$id));
        return $data;
    }
    //获取某用户中奖数据
    public function get_my($uid){
        $data = DB::fetch_all("select * from %t where `luck_id`=%i ORDER BY `luck_time` DESC",array($this->_table,$uid));
        return $data;
    }
    //获取用户当天中奖次数
    public function get_dayall($uid,$now){
        $res = DB::result_first("select count(*) from %t where `luck_id`=%i and `luck_day`=%i",array($this->_table,$uid,$now));
        return $res;
    }
}