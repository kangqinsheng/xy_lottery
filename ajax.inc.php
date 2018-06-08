<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 16:31
 */
require ("class/Poster.php");
if($_POST['ajax']){
    $action = $_POST['ajax'];
    $id = $_POST['act_id'];
    $poster = new Poster();
    //���߻
    if($action == "underline_active"){
        $data = array('ac_status'=>1);
        $res = C::t("#xy_lottery#xy_active")->update_status($id,$data);
        if($res){
            echo json_encode(array("status"=>200));
        }
    }
    //���߻
    if($action == "online_active"){
        $data = array('ac_status'=>0);
        $res = C::t("#xy_lottery#xy_active")->update_status($id,$data);
        if($res){
            echo json_encode(array("status"=>200));
        }
    }
    //ɾ����Ʒ
    if($action == "delete_prize"){
        $res = C::t("#xy_lottery#prize_list")->delete_one($id);
        if($res){
            echo json_encode(array("status"=>200));
        }
    }
    //���½�Ʒ
    if($action == "update_prize"){
        $prize_num = intval($_POST['prize_num']);
        $prize_name = iconv('UTF-8','GBK',$_POST['prize_name']);
        $prize_jifen = intval($_POST['prize_jifen']);
        $prize_count = intval($_POST['prize_count']);
        $prize_prob = intval($_POST['prize_prob']);
        $data = array('prize_num'=>$prize_num,'prize_name'=>$prize_name,'prize_jifen'=>$prize_jifen,'prize_count'=>$prize_count,'prize_prob'=>$prize_prob);
        $res = C::t("#xy_lottery#prize_list")->update_one($id,$data);
        if($res){
            echo json_encode(array("status"=>200));
        }else{
            echo json_encode(array("status"=>500));
        }
    }
    //�鿴�н���¼
    if($action == "my_lucks"){
        //�鿴�ҵ��н���¼
        $my_list = C::t("#xy_lottery#lucky_list")->get_my($id);
        $my_data = array();
        foreach ($my_list as $key=>$val){
            $arr1['id'] = $val['id'];
            $arr1['time'] = date("md H:i",$val['luck_time']);
            $arr1['prize'] = iconv("GBK","UTF-8",$val['price_name']);
            $arr1['dh_status'] = $val['dh_status'];
            $my_data[] = $arr1;
        }
        echo json_encode($my_data);
    }

    //��Ӷ���齱����
    if($action == "add_extra"){
        $now = intval(date("Ymd",time()));
        $times = $_POST['times']?$_POST['times']:1;
        //�鿴�Ƿ���������
        $res = C::t("#xy_lottery#extra_times")->extra_time($id,$now);
        if($res){
            //����
            $res = C::t("#xy_lottery#extra_times")->update_extra($id,$times,$now);
        }else{
            $data =array("u_id"=>$id,"extra_times_day"=>$now,"extra_times"=>$times);
            //��������
            $res = C::t("#xy_lottery#extra_times")->add_extra($id,$data);
        }
        if($res){
            echo json_encode(array("status"=>200));
        }else{
            echo json_encode(array("status"=>500,'msg'=>"���ʧ��"));
        }
    }

    //�һ����н�
    if($action == 'dh_lucky'){
        $one = C::t("#xy_lottery#lucky_list")->get_one($id);
        $uid = $one['luck_id'];
        $ac_integral = $one['prize_jifen'];
        //�н������������0,��ӻ���
        if($ac_integral>0&&$one['dh_status']==0){
            //��ӻ���
            $data = array("type"=>1,"integral"=>$ac_integral,"id"=>$uid);
            $url = "http://api.dsrb.cq.cn/member/my/addfen";
            $res = json_decode($poster->getHttpContent($url, "POST", $data),true);
            if($res['status']!=200){
                echo json_encode(array("status"=>500,"message"=>"�һ�����ʧ��"));
            }else{//�һ��ɹ����޸�״̬
                $data = array('dh_status'=>1);
                $res = C::t("#xy_lottery#lucky_list")->update_data($id,$data);
            }
        }
        if($res){
            echo json_encode(array("status"=>200));
        }else{
            echo json_encode(array("status"=>500,'msg'=>"�һ�ʧ��"));
        }
    }
    //����
    if($action == 'search_lucky'){
        $luck_name = iconv("UTF-8",'GBK',$_POST['luck_name']);
        $luck_phone = iconv("UTF-8",'GBK',$_POST['luck_phone']);
        $prize_name = iconv("UTF-8",'GBK',$_POST['prize_name']);
        $data = array();
        if(isset($luck_name)&&$luck_name!=""){
            //ͨ�����ֲ�
            $by_name = C::t("#xy_lottery#lucky_list")->get_by_uname($luck_name);
            foreach ($by_name as $key=>$val){
                $data[] = $val;
            }
        }
        if(isset($luck_phone)&&$luck_phone!=""){
            //ͨ���绰��
            $by_phone = C::t("#xy_lottery#lucky_list")->get_by_uphone($luck_phone);
            foreach ($by_phone as $key=>$val){
                $data[] = $val;
            }
        }
        if(isset($prize_name)&&$prize_name!=""){
            //ͨ����Ʒ���ֲ�
            $by_prize = C::t("#xy_lottery#lucky_list")->get_by_pname($prize_name);
            foreach ($by_prize as $key=>$val){
                $data[] = $val;
            }
        }
        foreach ($data as $key=>$val){
            $data[$key]['luck_name'] = iconv("GBK","UTF-8",$val['luck_name']);
            $data[$key]['luck_phone'] = iconv("GBK","UTF-8",$val['luck_phone']);
            $data[$key]['price_name'] = iconv("GBK","UTF-8",$val['price_name']);
            $data[$key]['luck_time'] = date("Y-m-d H:i:s",$val['luck_time']);
            $data[$key]['dh_status'] = $val['dh_status'];
        }
        echo json_encode(array("status"=>200,"result"=>$data));
    }
    //�齱�������
    function get_rand($proArr){
        $result = '';
        //����������ܸ��ʾ���
        $proSum = array_sum($proArr);
        //��������ѭ��
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }

    //�齱
    if($action == "lotto"){
        $now = intval(date("Ymd", time()));
        //�鿴�û��ѳ����
        $count = C::t("#xy_lottery#lotto_list")->get_count($id,$now);
        //�����
        $active = C::t("#xy_lottery#xy_active")->get_first();
        //���Ѵ���
        $free_times = $active['ac_frees'];
        //���˵�ǰ�������
        $extra_times = C::t("#xy_lottery#extra_times")->extra_time($id,$now);
        if($count>=($free_times+$extra_times)){//��Ѵ���������
            $money = $active['ac_integral'];
            if($money>0){
                //�۳�����
                $data = array("type"=>2,"integral"=>$money,"id"=>$id);
                $url = "http://api.dsrb.cq.cn/member/my/addfen";
                $res = json_decode($poster->getHttpContent($url, "POST", $data),true);
                if($res['status']==200){

                }else{
                    echo json_encode(array("status"=>500,"message"=>"�۳�����ʧ��"));
                }
            }
        }
        //��ѯ��Ʒ
        $prizes = C::t("#xy_lottery#prize_list")->get_list();
        //лл�����±�
        $xx_index = 0;
        //���ý�Ʒ
        $prize_arr = array();
        foreach ($prizes as $key => $val) {
            $arr['id'] = $key+1;
            $arr['p_id'] = $val['id'];
            $arr['prize'] = $val['prize_name'];
            //��������
            $yizhong = C::t("#xy_lottery#lucky_list")->get_yizhong($val['id']);
            $shengyu = $val['prize_count']-$yizhong['count'];
            $arr['count'] = $shengyu;
            $arr['jifen'] = $val['prize_jifen'];
            if($arr['jifen'] == 9999){
                $xx_index = $arr['id'];
            }
            $arr['v'] = intval($val['prize_prob']);
            $prize_arr[] = $arr;
        }
        //��ȡ��Ʒ
        $rand_arr = array();
        foreach ($prize_arr as $key => $val) {
            $rand_arr[$val['id']] = $val['v'];
        }
        $rid = get_rand($rand_arr); //���ݸ��ʻ�ȡ����id
        $jp = $prize_arr[$rid-1]; //�н���
        //��ӳ齱��¼
        $lotto_data = array("uid" => $id, "lotto_day"=>$now);
        $res = C::t("#xy_lottery#lotto_list")->add_data($lotto_data);
        //���Ƹ��˵����н�����
        $dayall = C::t("#xy_lottery#lucky_list")->get_dayall($id,$now);
        if ($res) {//�齱��¼��ӳɹ�
            if($jp['jifen']!=9999&&$jp['count']!=0&&$dayall<3) {//���н�,�ѳ���,��������3�β�������
                //��ȡ�û���Ϣ
                $url = "http://api.dsrb.cq.cn/member/my/info";
                $data = array("member_id" => $id);
                $res = json_decode($poster->getHttpContent($url, "POST", $data), true);
                if ($res) {
                    //��ȡ�û����ϳɹ�
                    $info = $res['result'];
                    $luck_name = iconv("UTF-8","GBK",$info['member_nick_name']);
                    $luck_phone = $info['member_phone'];
                    //����н���¼
                    $jp_data = array('luck_id' => $id, 'luck_name' => $luck_name, 'luck_phone' => $luck_phone, 'luck_time' => time(), 'luck_day' => $now, 'price_name' => $jp['prize'], 'prize_jifen' => $jp['jifen'], 'prize_id' => $jp['p_id']);
                    $res = C::t("#xy_lottery#lucky_list")->add_data($jp_data);
                    //���ش�������
                    if ($res) {
                        //�����н���¼�ɹ�
                        echo json_encode(array("status" => 200, 'jp_index' => $jp['id'], "jp_id" => $jp['p_id'], 'jp_name' => iconv("GBK","UTF-8",$jp['prize'])));
                    } else {
                        //�������ʧ��
                        echo json_encode(array("status" => 500, "msg" => "�н���¼���ʧ��"));
                    }
                } else {
                    //��ȡ�н��û���ϸ����ʧ��
                    echo json_encode(array("status" => 500, "msg" => "��ȡ�û���ϸ����ʧ��"));
                }
            }else{//лл����
                echo json_encode(array("status" => 100,'jp_index' => $xx_index));
            }
        } else {
            //�齱��¼���ʧ��
            echo json_encode(array("status" => 500, "msg" => "�齱��¼���ʧ��"));
        }
    }

}