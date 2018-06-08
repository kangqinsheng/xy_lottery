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
    //�鿴�û�ĳ�ճ齱����
    public function get_count($id,$day){
        $res = DB::result_first("select count(*) from %t where `uid`=%i and `lotto_day`=%i",array($this->_table,$id,$day));
        return $res;
    }
    //��ӳ齱��¼
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //�齱����
    public function lotto_all(){
        $res = DB::result_first("select count(*) from %t",array($this->_table));
        return $res;
    }
    //���ղ�������
    public function lotto_person(){
        $res = DB::result_first("select count(distinct `uid`) from %t",array($this->_table));
        return $res;
    }
    //��������
    public function lotto_person_now($now){
        $res = DB::result_first("select count(distinct `uid`) from %t where `lotto_day`=%i",array($this->_table,$now));
        return $res;
    }
}