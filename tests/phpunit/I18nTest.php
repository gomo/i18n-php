<?php

use Symfony\Component\Yaml\Yaml;

class I18nTest extends PHPUnit_Framework_TestCase
{
  private $basePath;

  public static function setUpBeforeClass()
  {

  }

  public function setUp()
  {
    $this->basePath = __DIR__ . '/..';

    $gen = new Gomo\I18n\Generator();
    $gen
      ->setSourceLang('ja')
      ->setDir($this->basePath.'/sample/lang');

    file_put_contents($gen->getLangFilePath(), Yaml::dump(array(
      "保存" => array(
        "value" => "",
        "files" => array(
          $this->basePath.'/sample/app/controllers/TestController.php',
        ),
      ),
    )), Gomo\I18n\Generator::YAML_INLINE);
  }

  private function loadYaml($lang)
  {
    return Yaml::parse(file_get_contents($this->basePath.'/sample/lang/'.$lang.'.yml'));
  }

  public function testGenerator()
  {
    $gen = new Gomo\I18n\Generator();
    $gen
      ->setSourceLang('ja')
      ->setDir($this->basePath.'/sample/lang');

    $gen->load();

    $enties = $gen->getEntries();
    $this->assertEquals(1, count($enties));

    $gen->addEntries($this->basePath.'/sample/app/controllers/TestController.php');

    $enties = $gen->getEntries();
    $this->assertEquals(2, count($enties));

    $this->assertEquals(1, count($enties['保存']->getFiles()));

    $gen->save();
  }
}