<?php
date_default_timezone_set('Asia/Shanghai');
require('functions.php');
require __DIR__ . '/vendor/autoload.php';
use Curl\Curl;
global $cookies;
println('————————————————————————————————————————————');
println('慈云自动签到程序 v1.0.0');
println('Github: https://github.com/yucho123987/CYAutoCheckIn');
println('————————————————————————————————————————————');
$last_check_in_date='1919-08-10';
if (file_exists(getcwd().'/.lastCheckInDate.txt')) {
  $last_check_in_date=file_get_contents(getcwd().'/.lastCheckInDate.txt');
} else {
  @file_put_contents(getcwd().'/.lastCheckInDate.txt',$last_check_in_date);
}
info('当前目录：'.getcwd());
info('当前时间：'.date('Y-m-d H:i:s'));
require('init.php');
require('login.php');
println('获取用户信息中...');
$user_info_curl=new Curl();
$user_info_curl->setCookies($cookies);
$user_info_curl->get('https://www.zovps.com/clientarea');
if ($user_info_curl->error) {
  warning('获取用户信息失败：'.$user_info_curl->errorMessage);
}
if (count(explode('<span class="d-none d-xl-inline-block ml-1" key="t-henry">',$user_info_curl->response))==2) {
  println(getStrBetween($user_info_curl->response,'<span class="d-none d-xl-inline-block ml-1" key="t-henry">','</span>').'，欢迎您！');
  info('当前账户余额：'.getStrBetween($user_info_curl->response,'<div class="funds-num">','</div>'));
  $uid=getStrBetween($user_info_curl->response,'账户ID:','</span>');
  info('当前用户ID：'.$uid);
} else if(!$user_info_curl->error) {
  error('获取用户信息失败');
}
function check($last_check_in_date,$cookies,$uid) {
  if ($last_check_in_date!=date('Y-m-d')) {
    check_in($cookies,$uid);
    $last_check_in_date=date('Y-m-d');
    @file_put_contents(getcwd().'/.lastCheckInDate.txt',$last_check_in_date);
  }
  sleep(2);
  check($last_check_in_date,$cookies,$uid);
}
function check_in($cookies,$uid) {
  println('['.date('Y-m-d').']签到中...');
  $check_in_curl=new Curl();
  $check_in_curl->setCookies($cookies);
  $check_in_curl->post('https://www.zovps.com/addons?_plugin=57&_controller=index&_action=index',['uid'=>$uid]);
  if ($check_in_curl->error) {
    warning('请求失败，无法签到：'.$check_in_curl->errorMessage);
    info('5s 后重试...');
    sleep(5000);
    check_in($cookies,$uid);
  } else {
    $check_in_result=json_decode($check_in_curl->response,true);
    if ($check_in_result['code']==200) {
      success($check_in_result['msg']);
    } else {
      warning('签到失败：'.$check_in_result['msg']);
    }
  }
}
info('已开启自动签到功能，每日0:00将自动签到...');
check($last_check_in_date,$cookies,$uid);
?>