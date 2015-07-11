<?php
namespace Gomo\I18n\Generator;

class Entry
{
  private $key;
  private $value = '';

  private $files = array();
  public function __construct($key)
  {
    $this->key = $key;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getFiles()
  {
    return $this->files;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function addFile($file)
  {
    if(!in_array($file, $this->files)){
      $this->files[] = $file;
    }

    sort($this->files);
  }
}