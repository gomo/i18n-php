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

  public function testSimpleGenerator()
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
    $this->assertCount(2, $enties);

    $this->assertCount(1, $enties['保存']->getFiles());

    $gen->save();

    //保存ファイルの確認
    $result = $this->loadYaml('ja');
    $this->assertCount(2, $result);

    $keys = array_keys($result);
    $this->assertEquals('保存', $keys[0]);
    $this->assertEquals('戻る', $keys[1]);

    $this->assertCount(1, $result['保存']['files']);
    $this->assertEquals($this->basePath.'/sample/app/controllers/TestController.php', $result['保存']['files'][0]);

    $this->assertCount(1, $result['戻る']['files']);
    $this->assertEquals($this->basePath.'/sample/app/controllers/TestController.php', $result['戻る']['files'][0]);
  }
}