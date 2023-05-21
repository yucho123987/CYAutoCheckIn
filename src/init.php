<?php
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
if (!file_exists(getcwd().'/config.yml')) {
  warning('未在当前目录发现配置文件');
  info('是否生成配置文件？[y/n]');
  $choice=input();
  if (!strcasecmp($choice,"y")||$choice=="") {
    function ask_login_type() {
      $login_type=input();
      switch ($login_type) {
      case '1':
        return 'email';
        break;
      case '2':
        return 'phone';
        break;
      default:
        warning('您输入的登录类型错误，请重新输入：');
        return ask_login_type();
        break;
      }
    }
    function ask_username() {
      $username=input();
      if ($username=='') {
        warning('用户名（手机号）不能为空，请重新输入：');
        return ask_username();
      }
      return $username;
    }
    function ask_password() {
      $password=input();
      if ($password=='') {
        warning('密码不能为空，请重新输入：');
        return ask_password();
      }
      return $password;
    }
    echo '登录类型（邮箱+密码：1，手机号+密码：2）：';
    $username_text='邮箱';
    $config['login_type']=ask_login_type();
    if ($config['login_type']=='phone') {
      $username_text='手机号';
    }
    echo $username_text.'：';
    $config['username']=ask_username();
    echo '密码：';
    $config['password']=ask_password();
    $configuration_file_contents=Yaml::dump($config);
    file_put_contents(getcwd().'/config.yml',$configuration_file_contents);
    if (file_exists(getcwd().'/config.yml')==false) {
      error('配置文件生成失败');
    }
    success('配置文件生成成功');
  } else  {
    error('无配置文件，退出程序');
  }
} else {
  try {
    if (str_replace(' ','',str_replace(PHP_EOL,'',file_get_contents(getcwd().'/config.yml')))=='') {
      error('配置文件内容不能为空');
    }
    $config=Yaml::parseFile(getcwd().'/config.yml');
    if (!array_key_exists('login_type',$config)||empty($config['login_type'])) {
      error('必须在配置文件中设置login_type（登录类型，email为邮箱，phone为手机号）');
    }
    if (!array_key_exists('username',$config)||empty($config['username'])) {
      error('必须在配置文件中设置username（邮箱/手机号）');
    }
    if (!array_key_exists('password',$config)||empty($config['password'])) {
      error('必须在配置文件中设置password（密码）');
    }
  } catch (ParseException $exception) {
    error('无法解析配置文件：'.$exception);
  }
}
?>