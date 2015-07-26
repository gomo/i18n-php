<?php
namespace Gomo\I18n\Generator;

use Gomo\I18n\Storage;
use Symfony\Component\Yaml\Yaml;

class Generator
{
  private $lang;
  private $dir;
  private $entries = array();
  private $storage;

  const YAML_INLINE = 3;

  public function setStorage(Storage\Storage $storage)
  {
    $this->storage = $storage;
    return $this;
  }

  public function setDir($dir)
  {
    $this->dir = $dir;
    return $this;
  }

  public function getLangFilePath()
  {
    if(!$this->dir){
      throw new \Exception('Missing dir.');
    }

    if(!$this->lang){
      throw new \Exception('Missing lang.');
    }
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

  private function storage()
  {
    if(!$this->storage){
      throw new \Exception("Storage is empty.");
    }
    return $this->storage;
  }

  public function clearStorage()
  {
    $this->storage()->clear();
  }

  public function updateStorage()
  {
    $path = $this->getLangFilePath();
    if(!is_readable($path)){
      throw new \Exception("Not readable ".$path);
    }
    $values = Yaml::parse(file_get_contents($path));
    foreach($values as $key => $data){
      $this->storage()->set($this->lang, $key, $data['value']);
    }
  }
}