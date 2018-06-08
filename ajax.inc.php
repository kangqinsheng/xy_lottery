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
    //下线活动
    if($action == "underline_active"){
        $data = array('ac_status'=>1);
        $res = C::t("#xy_lottery#xy_active")->update_status($id,$data);
        if($res){
            echo json_encode(array("status"=>200));
        }
    }
    //上线活动
    if($action == "online_active"){
        $data = array('ac_status'=>0);
        $res = C::t("#xy_lottery#xy_active")->update_status($id,$data);
        if($res){
            echo json_encode(array("status"=>200));
        }
    }
    //删除奖品
    if($action == "delete_prize"){
        $res = C::t("#xy_lottery#prize_list")->delete_one($id);
        if($res){
            echo json_encode(array("status"=>200));
        }
    }
    //更新奖品
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
    //查看中奖记录
    if($action == "my_lucks"){
        //查看我的中奖记录
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

    //添加额外抽奖次数
    if($action == "add_extra"){
        $now = intval(date("Ymd",time()));
        $times = $_POST['times']?$_POST['times']:1;
        //查看是否已有数据
        $res = C::t("#xy_lottery#extra_times")->extra_time($id,$now);
        if($res){
            //更新
            $res = C::t("#xy_lottery#extra_times")->update_extra($id,$times,$now);
        }else{
            $data =array("u_id"=>$id,"extra_times_day"=>$now,"extra_times"=>$times);
            //插入数据
            $res = C::t("#xy_lottery#extra_times")->add_extra($id,$data);
        }
        if($res){
            echo json_encode(array("status"=>200));
        }else{
            echo json_encode(array("status"=>500,'msg'=>"添加失败"));
        }
    }

    //兑换已中奖
    if($action == 'dh_lucky'){
        $one = C::t("#xy_lottery#lucky_list")->get_one($id);
        $uid = $one['luck_id'];
        $ac_integral = $one['prize_jifen'];
        //中奖积分如果大于0,添加积分
        if($ac_integral>0&&$one['dh_status']==0){
            //添加积分
            $data = array("type"=>1,"integral"=>$ac_integral,"id"=>$uid);
            $url = "http://api.dsrb.cq.cn/member/my/addfen";
            $res = json_decode($poster->getHttpContent($url, "POST", $data),true);
            if($res['status']!=200){
                echo json_encode(array("status"=>500,"message"=>"兑换积分失败"));
            }else{//兑换成功，修改状态
                $data = array('dh_status'=>1);
                $res = C::t("#xy_lottery#lucky_list")->update_data($id,$data);
            }
        }
        if($res){
            echo json_encode(array("status"=>200));
        }else{
            echo json_encode(array("status"=>500,'msg'=>"兑换失败"));
        }
    }
    //搜索
    if($action == 'search_lucky'){
        $luck_name = iconv("UTF-8",'GBK',$_POST['luck_name']);
        $luck_phone = iconv("UTF-8",'GBK',$_POST['luck_phone']);
        $prize_name = iconv("UTF-8",'GBK',$_POST['prize_name']);
        $data = array();
        if(isset($luck_name)&&$luck_name!=""){
            //通过名字查
            $by_name = C::t("#xy_lottery#lucky_list")->get_by_uname($luck_name);
            foreach ($by_name as $key=>$val){
                $data[] = $val;
            }
        }
        if(isset($luck_phone)&&$luck_phone!=""){
            //通过电话查
            $by_phone = C::t("#xy_lottery#lucky_list")->get_by_uphone($luck_phone);
            foreach ($by_phone as $key=>$val){
                $data[] = $val;
            }
        }
        if(isset($prize_name)&&$prize_name!=""){
            //通过奖品名字查
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
    //抽奖随机方法
    function get_rand($proArr){
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
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

    //抽奖
    if($action == "lotto"){
        $now = intval(date("Ymd", time()));
        //查看用户已抽次数
        $count = C::t("#xy_lottery#lotto_list")->get_count($id,$now);
        //活动详情
        $active = C::t("#xy_lottery#xy_active")->get_first();
        //活动免费次数
        $free_times = $active['ac_frees'];
        //个人当前额外次数
        $extra_times = C::t("#xy_lottery#extra_times")->extra_time($id,$now);
        if($count>=($free_times+$extra_times)){//免费次数已用完
            $money = $active['ac_integral'];
            if($money>0){
                //扣除积分
                $data = array("type"=>2,"integral"=>$money,"id"=>$id);
                $url = "http://api.dsrb.cq.cn/member/my/addfen";
                $res = json_decode($poster->getHttpContent($url, "POST", $data),true);
                if($res['status']==200){

                }else{
                    echo json_encode(array("status"=>500,"message"=>"扣除积分失败"));
                }
            }
        }
        //查询奖品
        $prizes = C::t("#xy_lottery#prize_list")->get_list();
        //谢谢参与下标
        $xx_index = 0;
        //设置奖品
        $prize_arr = array();
        foreach ($prizes as $key => $val) {
            $arr['id'] = $key+1;
            $arr['p_id'] = $val['id'];
            $arr['prize'] = $val['prize_name'];
            //计算余量
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
        //获取奖品
        $rand_arr = array();
        foreach ($prize_arr as $key => $val) {
            $rand_arr[$val['id']] = $val['v'];
        }
        $rid = get_rand($rand_arr); //根据概率获取奖项id
        $jp = $prize_arr[$rid-1]; //中奖项
        //添加抽奖记录
        $lotto_data = array("uid" => $id, "lotto_day"=>$now);
        $res = C::t("#xy_lottery#lotto_list")->add_data($lotto_data);
        //控制个人当天中奖上限
        $dayall = C::t("#xy_lottery#lucky_list")->get_dayall($id,$now);
        if ($res) {//抽奖记录添加成功
            if($jp['jifen']!=9999&&$jp['count']!=0&&$dayall<3) {//已中奖,已抽完,当日已中3次不能再中
                //获取用户信息
                $url = "http://api.dsrb.cq.cn/member/my/info";
                $data = array("member_id" => $id);
                $res = json_decode($poster->getHttpContent($url, "POST", $data), true);
                if ($res) {
                    //获取用户资料成功
                    $info = $res['result'];
                    $luck_name = iconv("UTF-8","GBK",$info['member_nick_name']);
                    $luck_phone = $info['member_phone'];
                    //添加中奖记录
                    $jp_data = array('luck_id' => $id, 'luck_name' => $luck_name, 'luck_phone' => $luck_phone, 'luck_time' => time(), 'luck_day' => $now, 'price_name' => $jp['prize'], 'prize_jifen' => $jp['jifen'], 'prize_id' => $jp['p_id']);
                    $res = C::t("#xy_lottery#lucky_list")->add_data($jp_data);
                    //返回处理数据
                    if ($res) {
                        //插入中奖记录成功
                        echo json_encode(array("status" => 200, 'jp_index' => $jp['id'], "jp_id" => $jp['p_id'], 'jp_name' => iconv("GBK","UTF-8",$jp['prize'])));
                    } else {
                        //数据添加失败
                        echo json_encode(array("status" => 500, "msg" => "中奖记录添加失败"));
                    }
                } else {
                    //获取中奖用户详细资料失败
                    echo json_encode(array("status" => 500, "msg" => "获取用户详细资料失败"));
                }
            }else{//谢谢参与
                echo json_encode(array("status" => 100,'jp_index' => $xx_index));
            }
        } else {
            //抽奖记录添加失败
            echo json_encode(array("status" => 500, "msg" => "抽奖记录添加失败"));
        }
    }

}