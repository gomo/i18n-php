<?php
if(!isset($argv[1])){
  fwrite(STDERR, 'Usage: php generate.php /path/to/script.php /path/to/lang/dir lang'.PHP_EOL);
  exit(1);
}

$scriptPath = $argv[1];

if(!isset($argv[2])){
  fwrite(STDERR, 'Usage: php generate.php /path/to/script.php /path/to/lang/dir lang'.PHP_EOL);
  exit(1);
}

$langDir = $argv[2];

if(!isset($argv[3])){
  fwrite(STDERR, 'Usage: php generate.php /path/to/script.php /path/to/lang/dir lang'.PHP_EOL);
  exit(1);
}

$lang = $argv[3];

require_once __DIR__ . '/../vendor/autoload.php';

$gen = new Gomo\I18n\Generator();
$gen
  ->setSourceLang($lang)
  ->setDir($langDir);

$gen->load();

$gen->addEntries($scriptPath);
$gen->saveFile();

fwrite(STDOUT, 'Done '.$scriptPath.' for '.$lang.PHP_EOL);
