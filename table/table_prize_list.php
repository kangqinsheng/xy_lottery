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
    //����������
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //��ȡ������
    public function count_num(){
       $res = DB::fetch_first("select count(*) as 'count' from %t",array($this->_table));
       return $res;
    }

    //��ȡ�������ݣ��������������
    public function get_list(){
        $data = DB::fetch_all("select * from %t ORDER BY `prize_num` asc",array($this->_table));
        return $data;
    }
    //ɾ��һ������
    public function delete_one($id){
        $res = DB::delete($this->_table,array('id'=>$id));
        return $res;
    }
    //��������
    public function update_one($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
}