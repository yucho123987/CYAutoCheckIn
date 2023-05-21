#!/usr/bin/php
<?php
function getStub() {
  $stub = <<<'EOF'
#!/usr/bin/env php
<?php
if (!class_exists('Phar')) {
  echo 'PHP\'s phar extension is missing. Install requires it to run. Enable the extension or recompile php without --disable-phar then try again.' . PHP_EOL;
  exit(1);
}
Phar::mapPhar('cyaci.phar');
EOF;
  return $stub . <<<'EOF'
require 'phar://cyaci.phar/main.php';
__HALT_COMPILER();
EOF;
}
echo "生成phar中...";
if (file_exists('cyaci.phar')) {
  @unlink('cyaci.phar');
}
try {
  $phar=new Phar('cyaci.phar',0,'cyaci.phar');
  $phar->startBuffering();
  $phar->buildFromDirectory('src');
  $content=@file_get_contents(getcwd().'/cyaci.phar');
  $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
  $phar->addFromString('cyaci.phar', $content);
  $phar->setStub(getStub());
  $phar->stopBuffering();
  if (count(explode('Linux',PHP_OS))>1) {
    shell_exec('chmod +x cyaci.phar');
  }
  if (file_exists('cyaci.phar')) {
  echo "\033[32m成功！\033[0m\n";
  } else {
    echo "\033[31m失败！\033[0m\n";
    die("\033[31m无法生成phar\n");
  }
} catch (Exception $error) {
  echo "\033[31m失败！\033[0m\n";
  die("\033[31mphar生成失败：".$error."\033[0m\n");
}
?>
