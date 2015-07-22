<?php
if(!isset($argv[1])){
  fwrite(STDERR, 'Usage: php redis.php /path/to/lang/dir lang'.PHP_EOL);
  exit(1);
}

$langDir = $argv[1];

if(!isset($argv[2])){
  fwrite(STDERR, 'Usage: php redis.php /path/to/lang/dir lang'.PHP_EOL);
  exit(1);
}

$lang = $argv[2];

require_once __DIR__ . '/bootstrap.php';

$gen = new Gomo\I18n\Generator();
$gen->setDir($langDir);
$gen->setLang($lang)->updateRedis();

fwrite(STDOUT, 'Done '.$lang.' redis '.PHP_EOL);