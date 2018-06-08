<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if($_POST['action']=="add_active"){
    $id = $_POST['ac_id'];//判断id标示
    $ac_name = $_POST['ac_name'];
    //开始结束时间比较大小
    if(intval($_POST['ac_sday'])<intval($_POST['ac_eday'])){
        $ac_sday = intval($_POST['ac_sday']);
        $ac_eday = intval($_POST['ac_eday']);
    }else{
        $ac_sday = intval($_POST['ac_eday']);
        $ac_eday = intval($_POST['ac_sday']);
    }
    $ac_rule = $_POST['ac_rule'];
    $ac_status = 0;
    $ac_frees = intval($_POST['ac_frees']);
    $ac_dtimes = intval($_POST['ac_dtimes']);
    $ac_integral = intval($_POST['ac_integral']);
    $data = array('ac_name'=>$ac_name,'ac_sday'=>$ac_sday,'ac_eday'=>$ac_eday,'ac_rule'=>$ac_rule,'ac_status'=>$ac_status,'ac_frees'=>$ac_frees,'ac_dtimes'=>$ac_dtimes,'ac_integral'=>$ac_integral);
    if(!isset($id)||$id==''){
        //没有数据，新镇
        $res = C::t("#xy_lottery#xy_active")->add_data($data);
        if($res){
            //添加成功
            header("Location:plugin.php?id=xy_lottery:admin&action=active");
        }else{
            die("数据错误，请检查后重试");
        }
    }else{
        //已有数据，修改
        $res = C::t("#xy_lottery#xy_active")->update_data($id,$data);
        if($res){
            //添加成功
            header("Location:plugin.php?id=xy_lottery:admin&action=active");
        }else{
            die("数据错误，请检查后重试");
        }
    }
}
if($_POST['action']=="add_prize"){
    $prize_num = intval($_POST['prize_num']);
    $prize_name = $_POST['prize_name'];
    $prize_img = $_POST['prize_img'][0];
    $prize_count = intval($_POST['prize_count']);
    $prize_jifen = intval($_POST['prize_jifen']);
    $prize_prob = intval($_POST['prize_prob']);
    $data = array('prize_num'=>$prize_num,'prize_name'=>$prize_name,'prize_jifen'=>$prize_jifen,'prize_img'=>$prize_img,'prize_count'=>$prize_count,'prize_prob'=>$prize_prob);
    //查看已有数据条数，不能超过8条
    $count = C::t("#xy_lottery#prize_list")->count_num();
    if($count['count']<8){
        //直接插入数据
        $res = C::t("#xy_lottery#prize_list")->add_data($data);
        if($res){
            //添加成功
            header("Location:plugin.php?id=xy_lottery:admin&action=active");
        }else{
            die("数据错误，请查证后重试");
        }
    }else{
        die("活动奖品已满，请删除部分后添加");
    }
}
//直接进入显示
$action = $_GET['action']?$_GET['action']:'active';
if($action == 'active'){
    //获取活动相关数据
    $active_data = C::t("#xy_lottery#xy_active")->get_first();
    //获取讲评列表数据ss
    $prize_data = C::t("#xy_lottery#prize_list")->get_list();
    $prize_list = array();
    foreach($prize_data as $key=>$val){
        $prize['id'] = $val['id'];
        $prize['prize_num'] = $val['prize_num'];
        $prize['prize_name'] = $val['prize_name'];
        $prize['prize_img'] = $val['prize_img'];
        $prize['prize_jifen'] = $val['prize_jifen'];
        $prize['prize_count'] = $val['prize_count'];
        //计算余量和已中
        $yizhong = C::t("#xy_lottery#lucky_list")->get_yizhong($val['id']);
        $shengyu = $val['prize_count']-$yizhong['count'];
        $prize['prize_yizhong'] = $yizhong['count'];
        $prize['prize_shengyu'] = $shengyu;
        $prize['prize_prob'] = $val['prize_prob'];
        $prize_list[] = $prize;
    }
    include template("admin_active","","source/plugin/xy_lottery/template");
}
if($action == 'luck_list'){
    //参与次数
    $times_all = C::t('#xy_lottery#lotto_list')->lotto_all();
    //参与人数
    $persons_all = C::t('#xy_lottery#lotto_list')->lotto_person();
    //今日参与人数
    $now = intval(date("Ymd", time()));
    $persons_all_now = C::t('#xy_lottery#lotto_list')->lotto_person_now($now);
    //查看中奖列表
    $page = isset($_GET['page'])? intval($_GET['page']):1;
    $pagesize = 15;
    $start = ($page-1)*$pagesize;
    $luck_list = C::t('#xy_lottery#lucky_list')->get_limit($start,$pagesize);
    //获取其他信息
    $data = array();
    foreach ($luck_list as $key=>$val){
        $arr['id'] = $val['id'];
        $arr['nick_name'] = $val['luck_name'];
        $arr['phone'] = $val['luck_phone'];
        $arr['prize_name'] = $val['price_name'];
        $arr['prize_jifen'] = $val['prize_jifen'];
        $arr['luck_time'] = date("Y-m-d H:i:s",$val['luck_time']);
        $arr['dh_status'] = $val['dh_status'];
        $data[] = $arr;
    }
    $count = C::t('#xy_lottery#lucky_list')->get_count();
    $showNextPage = 1;
    if(($start + $pagesize) >= $count){
        $showNextPage = 0;
    }
    $allPageNum = ceil($count/$pagesize);
    $prePage = $page - 1;
    $nextPage = $page + 1;
    $prePageUrl = "plugin.php?id=xy_lottery:admin&action=luck_list&page={$prePage}";
    $nextPageUrl = "plugin.php?id=xy_lottery:admin&action=luck_list&page={$nextPage}";
    $firstPageUrl = "plugin.php?id=xy_lottery:admin&action=luck_list&page=1";
    $lastPageUrl = "plugin.php?id=xy_lottery:admin&action=luck_list&page={$allPageNum}";
    include template("luck_list","","source/plugin/xy_lottery/template");
}