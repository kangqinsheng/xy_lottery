<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * 抽奖活动表
 *
 */
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `xiaojizhe_xy_active` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ac_name` VARCHAR(100) NOT NULL,
  `ac_sday` int(20),
  `ac_eday` int(20),
  `ac_rule` VARCHAR (3000),
  `ac_status` int(1) DEFAULT 0,
  `ac_frees` int(10),
  `ac_dtimes` int(10),
  `ac_integral` int(10)
) ENGINE=MyISAM;
EOF;
DB::query($sql);
/**
 * 厢遇抽奖奖品列表
 */
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `xiaojizhe_prize_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `prize_num` int(10),
  `prize_name` VARCHAR(100) NOT NULL,
  `prize_img` VARCHAR(100),
  `prize_jifen` int(10) DEFAULT 0,
  `prize_count` int(10),
  `prize_prob` int(10)
) ENGINE=MyISAM;
EOF;
DB::query($sql);
/**
 * 抽奖参与表
 */
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `xiaojizhe_lotto_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int(10) NOT NULL,
  `lotto_day` int(20)
) ENGINE=MyISAM;
EOF;
DB::query($sql);
/**
 * 中奖列表
 */
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `xiaojizhe_lucky_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `luck_id` int(10) NOT NULL,
  `luck_name` varchar(100),
  `luck_phone` varchar(20),
  `luck_time` int(20),
  `luck_day` int(20),
  `prize_id` int(10),
  `price_name` varchar(100),
  `prize_jifen` int(10) DEFAULT 0,
  `dh_status` int(1) DEFAULT 0
) ENGINE=MyISAM;
EOF;
DB::query($sql);
/**
 * 额外次数表
 */
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `xiaojizhe_extra_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `u_id` int(10),
  `extra_times_day` int(20),
  `extra_times` int(10) DEFAULT 0
) ENGINE=MyISAM;
EOF;
DB::query($sql);
echo "数据表添加成功";
