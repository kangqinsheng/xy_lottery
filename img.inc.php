<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 9:35
 */
$action = $_REQUEST['action'];
if($action == "add"){
    $id = $_POST['upload_id'];
    $base64 = $_POST['upload_pic'];
    $filename = time();
    $rand = rand(10000, 99999);
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
        $type = $result[2];
        if($type = "jpeg")$type = "jpg";
        $new_file = "source/plugin/xy_lottery/uploads/".$filename."_".$rand.".{$type}";
        file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)));
    }
    $data['errCode'] = 0;
    $data['data']['id'] = $id;
    $data['data']['picId'] = $filename."_".$rand;
    echo (json_encode($data));
}elseif($action == "delete"){
    $title = $_GET['title'];
    $new_file = "source/plugin/xy_lottery/uploads/{$title}.jpg";
    unlink($new_file);
}