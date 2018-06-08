<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 11:49
 */

class table_prize_list extends discuz_table
{
    public function __construct(){
        $this->_table = 'prize_list';
        $this->_pk = 'id';
        parent::__construct();
    }
    //插入新数据
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //获取总条数
    public function count_num(){
       $res = DB::fetch_first("select count(*) as 'count' from %t",array($this->_table));
       return $res;
    }

    //获取已有数据，按序号升序排列
    public function get_list(){
        $data = DB::fetch_all("select * from %t ORDER BY `prize_num` asc",array($this->_table));
        return $data;
    }
    //删除一条数据
    public function delete_one($id){
        $res = DB::delete($this->_table,array('id'=>$id));
        return $res;
    }
    //更新数据
    public function update_one($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
}