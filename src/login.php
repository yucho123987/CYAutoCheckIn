<?php
use Curl\Curl;
println('获取登录token...');
$cookies=[];
$login_data=[];
$token_curl=new Curl();
$token_curl->setCookies($cookies);
$token_curl->get('https://www.zovps.com/login');
if ($token_curl->error) {
  error('获取登录token失败：'.$token_curl->errorMessage);
} else {
  $login_data['token']=getStrBetween($token_curl->response,'<input type="hidden" name="token" value="','">');
  success('登录token获取成功：'.$login_data['token']);
  $cookies=$token_curl->getResponseCookies();
}
switch ($config['login_type']) {
  case 'email':
    println('尝试使用配置文件中的邮箱与密码登录...');
    break;
  case 'phone':
    println('尝试使用配置文件中的手机号与密码登录...');
    break;
  default:
    error('暂不支持的登录类型：'.$config['login_type'].'，无法登录');
    break;
}
$login_curl=new Curl();
$login_curl->setCookies($cookies);
$login_data[$config['login_type']]=$config['username'];
$login_data['password']=$config['password'];
  println('登录中...');
$login_curl->post('https://www.zovps.com/login?action='.$config['login_type'],$login_data);
$cookies=$login_curl->getResponseCookies();
if ($login_curl->error) {
  error('登录失败：'.$login_curl->errorMessage);
}
if (count(explode('<div class="alert-body">',$login_curl->response))==2) {
  error('登录失败：'.getStrBetween($login_curl->response,'<div class="alert-body">','</div>'));
} else {
  success('登录成功');
}
?>