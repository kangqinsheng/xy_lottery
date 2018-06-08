<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20
 * Time: 11:53
 */
//�û�����齱��ҳ��ȡ�û�������Ϣ
require("class/OpenSSLEncryptDecrypt.php");
require("class/Poster.php");
session_start();
$uid = $_SESSION['uid'];
$token = $_GET['voucher'];
const KEY = '7bef64952807359ccc94ac5d1b864a4e';
$poster = new Poster();
if($token){
    $object = new OpenSSLEncryptDecrypt(KEY);
    $object->setCipherText($token);
    $deText = $object->decrypt();
    //��ȡ�û�id
    $url = "http://api.dsrb.cq.cn/member/my/index";
    $data = array("request"=>json_encode(array("user_token"=>$deText,"device_type"=>"Android","post_body"=>"")));
    $all = json_decode($poster->getHttpContent($url, "POST", $data),true);
    $users = $all["result"];
    $uid = $users['member_id'];
    $_SESSION['uid']=$uid;
}
if($_SESSION['uid']){
    $uid=$_SESSION['uid'];
    /**
     * �鿴��ҳ����״̬���ʱ�䡢�״̬����ǰ�û�Ȩ��
     */
    $active_time_status = 0;//0Ϊ��ʼ��1Ϊδ�ڻʱ����
    $active_status = 0;//0Ϊ������1Ϊ�ѽ���
    $user_free_times = 0;//�û���Ѵ���������+�û����������
    $user_times = 0;//�û����ֿɳ����
    //���ѯ
    $active = C::t("#xy_lottery#xy_active")->get_first();
    //�鿴��Ƿ����ֶ���ͣ
    $active_status = $active['ac_status'];
    //��ȡ��ǰʱ��,��20180305,�жϻ�Ƿ��ڽ���ʱ�䷶Χ��
    $now = intval(date("Ymd",time()));
    $ac_start = $active['ac_sday'];
    $ac_end = $active['ac_eday'];
    if($ac_start<=$now&&$now<=$ac_end){
        //�ڻʱ����
        $active_time_status = 0;
    }else{
        //���ڻʱ����
        $active_time_status = 1;
    }
    //�鿴�û��ѳ����
    $count = C::t("#xy_lottery#lotto_list")->get_count($uid,$now);
    //���Ѵ���
    $free_times = $active['ac_frees'];
    //���˵�ǰ�������
    $extra_times = C::t("#xy_lottery#extra_times")->extra_time($uid,$now);
    if($count>=($free_times+$extra_times)){
        //��ѻ���������
        $user_free_times = 0;
        //��շѴ���
        $ac_dtimes = $active['ac_dtimes'];
        if($count>=($free_times+$extra_times+$ac_dtimes)){
            //���л��ᶼ������
            $user_times = 0;
        }else{
            //����ʣ���շѴ���
            $user_times = ($free_times+$extra_times+$ac_dtimes)-$count;
        }
    }else{
        $user_free_times = ($free_times+$extra_times)-$count;
        $user_times = $active['ac_dtimes'];
    }
    //��ѯ��Ʒ
    $prizes = C::t("#xy_lottery#prize_list")->get_list();
    $prizes_list = array();//ʹ������Ϊ0-7������
    foreach ($prizes as $key=>$val){
        $prizes_list[] = $val;
    }
    //�鿴�����н���Ա
    $luck_list = C::t("#xy_lottery#lucky_list")->get_newest(50);//ǰ��ʮ��
    $luck_data = array();
    foreach ($luck_list as $key=>$val){
        $arr['nick_name'] = $val['luck_name'];
        $arr['time'] = date("md H:i",$val['luck_time']);
        $arr['prize'] = $val['price_name'];
        $luck_data[] = $arr;
    }
    //�鿴�ҵ��н���¼
    $my_list = C::t("#xy_lottery#lucky_list")->get_my($uid);
    $my_data = array();
    foreach ($my_list as $key=>$val){
        $arr1['id'] = $val['id'];
        $arr1['time'] = date("md H:i",$val['luck_time']);
        $arr1['prize'] = $val['price_name'];
        $arr1['dh_status'] = $val['dh_status'];
        $my_data[] = $arr1;
    }
    include template("lotto_home","","source/plugin/xy_lottery/template");
}else{
    header("Location:http://www.cqdsrb.com.cn/app");
    exit;
}