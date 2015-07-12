<?php
namespace Gomo\I18n;

require_once __DIR__ . '/../../../vendor/symfony/yaml/Symfony/Component/Yaml/Yaml.php';
use Gomo\I18n\Generator\Entry;
use Symfony\Component\Yaml\Yaml;

class Generator
{
  private $sourceLang;
  private $dir;
  private $entries = array();

  const YAML_INLINE = 3;

  public function setDir($dir)
  {
    $this->dir = $dir;
  }

  public function getLangFilePath()
  {
    return $this->dir.'/'.$this->sourceLang.'.yml';
  }

  public function setSourceLang($lang)
  {
    $this->sourceLang = $lang;
    return $this;
  }

  public function load()
  {
    $this->entries = array();
    $values = Yaml::parse(file_get_contents($this->getLangFilePath()));
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

    if(preg_match_all('/\->__i18n\([\r\n ]*(?:\'|")([^\r\n]+)(?:\'|")[\r\n ]*\)/u', $body, $matches))
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

  public function save()
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
}