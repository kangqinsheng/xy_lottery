<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 11:47
 */

class table_xy_active extends discuz_table
{
    public function __construct(){
        $this->_table = 'xy_active';
        $this->_pk = 'id';
        parent::__construct();
    }
    //插入新数据
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //通过id判别，修改数据
    public function update_data($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
    //查询现有数据
    public function get_first(){
        $data = DB::fetch_first("select * from %t",array($this->_table));
        return $data;
    }
    //更新状态
    public function update_status($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
}