<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20
 * Time: 11:53
 */
//用户进入抽奖主页获取用户基本信息
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
    //获取用户id
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
     * 查看首页基本状态，活动时间、活动状态、当前用户权限
     */
    $active_time_status = 0;//0为开始，1为未在活动时间内
    $active_status = 0;//0为开启、1为已结束
    $user_free_times = 0;//用户免费次数（活动免费+用户额外次数）
    $user_times = 0;//用户积分可抽次数
    //活动查询
    $active = C::t("#xy_lottery#xy_active")->get_first();
    //查看活动是否已手动暂停
    $active_status = $active['ac_status'];
    //获取当前时间,如20180305,判断活动是否在进行时间范围内
    $now = intval(date("Ymd",time()));
    $ac_start = $active['ac_sday'];
    $ac_end = $active['ac_eday'];
    if($ac_start<=$now&&$now<=$ac_end){
        //在活动时间内
        $active_time_status = 0;
    }else{
        //不在活动时间内
        $active_time_status = 1;
    }
    //查看用户已抽次数
    $count = C::t("#xy_lottery#lotto_list")->get_count($uid,$now);
    //活动免费次数
    $free_times = $active['ac_frees'];
    //个人当前额外次数
    $extra_times = C::t("#xy_lottery#extra_times")->extra_time($uid,$now);
    if($count>=($free_times+$extra_times)){
        //免费机会已用完
        $user_free_times = 0;
        //活动收费次数
        $ac_dtimes = $active['ac_dtimes'];
        if($count>=($free_times+$extra_times+$ac_dtimes)){
            //所有机会都已用完
            $user_times = 0;
        }else{
            //计算剩余收费次数
            $user_times = ($free_times+$extra_times+$ac_dtimes)-$count;
        }
    }else{
        $user_free_times = ($free_times+$extra_times)-$count;
        $user_times = $active['ac_dtimes'];
    }
    //查询奖品
    $prizes = C::t("#xy_lottery#prize_list")->get_list();
    $prizes_list = array();//使用索引为0-7的数组
    foreach ($prizes as $key=>$val){
        $prizes_list[] = $val;
    }
    //查看最新中奖人员
    $luck_list = C::t("#xy_lottery#lucky_list")->get_newest(50);//前五十人
    $luck_data = array();
    foreach ($luck_list as $key=>$val){
        $arr['nick_name'] = $val['luck_name'];
        $arr['time'] = date("md H:i",$val['luck_time']);
        $arr['prize'] = $val['price_name'];
        $luck_data[] = $arr;
    }
    //查看我的中奖记录
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