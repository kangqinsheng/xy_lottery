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
    //��ȡ��Ӧ��Ʒ������������
    public function get_yizhong($id){
        $res = DB::fetch_first("select count(*) as 'count' from %t where `prize_id`=%i",array($this->_table,$id));
        return $res;
    }
    //����н���¼
    public function add_data($data){
        $res = DB::insert($this->_table,$data,true);
        return $res;
    }
    //�н�������
    public function get_count(){
        $res = DB::result_first("select count(*) from %t",array($this->_table));
        return $res;
    }
    //�н���ҳ
    public function get_limit($start,$size){
        $data = DB::fetch_all("select * from %t order by `luck_time` desc limit $start,$size",array($this->_table));
        return $data;
    }
    //�н�����
    public function get_newest($limit){
        $data = DB::fetch_all("select * from %t order by `luck_time` desc limit 0,$limit",array($this->_table));
        return $data;
    }
    //����״̬
    public function update_data($id,$data){
        $res = DB::update($this->_table,$data,array('id'=>$id));
        return $res;
    }
    //ͨ���û�������
    public function get_by_uname($name){
        $data = DB::fetch_all("select * from %t where `luck_name` LIKE %s order by `dh_status` asc ",array($this->_table,'%'.$name.'%'));
        return $data;
    }
    //ͨ���绰����
    public function get_by_uphone($phone){
        $data = DB::fetch_all("select * from %t where `luck_phone` LIKE %s order by `dh_status` asc ",array($this->_table,'%'.$phone.'%'));
        return $data;
    }
    //ͨ����Ʒ������
    public function get_by_pname($prize){
        $data = DB::fetch_all("select * from %t where `price_name` LIKE %s order by `dh_status` asc ",array($this->_table,'%'.$prize.'%'));
        return $data;
    }
    //��ȡ��Ӧ��������
    public function get_one($id){
        $data = DB::fetch_first("select * from %t where `id`=%i",array($this->_table,$id));
        return $data;
    }
    //��ȡĳ�û��н�����
    public function get_my($uid){
        $data = DB::fetch_all("select * from %t where `luck_id`=%i ORDER BY `luck_time` DESC",array($this->_table,$uid));
        return $data;
    }
    //��ȡ�û������н�����
    public function get_dayall($uid,$now){
        $res = DB::result_first("select count(*) from %t where `luck_id`=%i and `luck_day`=%i",array($this->_table,$uid,$now));
        return $res;
    }
}