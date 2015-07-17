<?php
namespace Gomo\I18n;

require_once __DIR__ . '/../../../vendor/redisent/redis/src/Redisent/Redis.php';
use Gomo\I18n\Generator\Entry;
use Symfony\Component\Yaml\Yaml;
use redisent\Redis;

class Generator
{
  private $lang;
  private $dir;
  private $entries = array();
  private $redisDb = 0;

  const YAML_INLINE = 3;

  public function setDir($dir)
  {
    $this->dir = $dir;
    return $this;
  }

  public function getLangFilePath()
  {
    return $this->dir.'/'.$this->lang.'.yml';
  }

  public function setLang($lang)
  {
    $this->lang = $lang;
    return $this;
  }

  public function load()
  {
    $this->entries = array();
    $source = @file_get_contents($this->getLangFilePath());
    if($source){
      $values = Yaml::parse($source);
    } else {
      $values = array();
    }

    foreach($values as $key => $data){
      $entry = new Entry($key);
      $entry->setValue($data['value']);
      foreach ($data['files'] as $file) {
        $entry->addFile($file);
      }

      $this->entries[$entry->getKey()] = $entry;
    }
  }

  public function getEntries()
  {
    return $this->entries;
  }

  public function addEntries($filePath)
  {
    $body = file_get_contents($filePath);
    if(preg_match_all('/Gomo\\\I18n::get\([\r\n ]*(?:\'|")([^\r\n]+)(?:\'|")[\r\n ]*\)/u', $body, $matches))
    {

      foreach($matches[1] as $key){
        $entry = @$this->entries[$key];
        if(!$entry){
          $entry = new Entry($key);
        }

        $entry->addFile($filePath);
        $this->entries[$entry->getKey()] = $entry;
      }
    }
  }

  public function saveFile()
  {
    ksort($this->entries);
    $values = array();
    foreach($this->entries as $entry)
    {
      $values[$entry->getKey()] = array(
        'value' => $entry->getValue(),
        'files' => $entry->getFiles()
      );
    }

    file_put_contents($this->getLangFilePath(), Yaml::dump($values, Generator::YAML_INLINE));
  }

  public function clearRedis()
  {
    $redis = new Redis();
    $redis->select($this->redisDb);
    $redis->flushdb();
  }

  public function updateRedis()
  {
    $redis = new Redis();
    $redis->select($this->redisDb);

    $path = $this->getLangFilePath();
    if(!is_readable($path)){
      throw new Exception("Not readable ".$path);
    }
    $values = Yaml::parse(file_get_contents($path));
    foreach($values as $key => $data){
      $redis->set($key.'@'.$this->lang, $data['value']);
    }
  }
}