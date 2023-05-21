<?php
function println($text) {
  echo $text.PHP_EOL;
}
function error($text) {
  die("\033[31m".$text."\033[0m\n");
}
function info($text) {
  println("\033[34m".$text."\033[0m");
}
function warning($text) {
  println("\033[33m".$text."\033[0m");
}
function success($text) {
  println("\033[32m".$text."\033[0m");
}
function input() {
  $text=fgets(STDIN);
  return str_replace(PHP_EOL,'',$text);
}
function getStrBetween($origin,$start,$end) {
  return explode($end,explode($start,$origin,2)[1])[0];
}
?>