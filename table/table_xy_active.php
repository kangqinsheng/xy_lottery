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
    //����������
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //ͨ��id�б��޸�����
    public function update_data($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
    //��ѯ��������
    public function get_first(){
        $data = DB::fetch_first("select * from %t",array($this->_table));
        return $data;
    }
    //����״̬
    public function update_status($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
}