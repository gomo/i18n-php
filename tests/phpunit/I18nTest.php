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
          $this->basePath.'/sample/index.php',
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

    $entries = $gen->getEntries();
    //setUpで一つ入れてあるので`1`。入れたキーは`保存`
    $this->assertEquals(1, count($entries));

    //sample/index.phpを読み込む
    $gen->addEntries($this->basePath.'/sample/index.php');

    //sample/index.phpには2つあるが、`保存`はかぶっているので`2`
    $entries = $gen->getEntries();
    $this->assertCount(2, $entries);

    $this->assertCount(1, $entries['保存']->getFiles());

    $gen->save();

    //保存ファイルの確認
    $result = $this->loadYaml('ja');
    $this->assertCount(2, $result);

    $keys = array_keys($result);
    $this->assertEquals('保存', $keys[0]);
    $this->assertEquals('戻る', $keys[1]);

    $this->assertCount(1, $result['保存']['files']);
    $this->assertEquals($this->basePath.'/sample/index.php', $result['保存']['files'][0]);

    $this->assertCount(1, $result['戻る']['files']);
    $this->assertEquals($this->basePath.'/sample/index.php', $result['戻る']['files'][0]);
  }
}