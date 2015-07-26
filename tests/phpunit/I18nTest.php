<?php

use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/../../src/bootstrap.php';

class I18nTest extends PHPUnit_Framework_TestCase
{
  private $basePath;

  public static function setUpBeforeClass()
  {

  }

  public function setUp()
  {
    $this->basePath = __DIR__ . '/..';

    //ymlファイルをクリア
    foreach(scandir($this->basePath.'/sample/lang/') as $entry)
    {
      if(strpos($entry, '.yml') !== false)
      {
        unlink($this->basePath.'/sample/lang/'.$entry);
      }
    }

    //storageをクリア
    $storage = new Gomo\I18n\Storage\Redis();
    $storage->clear();
  }

  private function loadYaml($lang)
  {
    return Yaml::parse(file_get_contents($this->basePath.'/sample/lang/'.$lang.'.yml'));
  }

  private function resetLangFile($lang, array $values)
  {
    $gen = new Gomo\I18n\Generator\Generator();
    $gen
      ->setLang($lang)
      ->setDir($this->basePath.'/sample/lang');

    $storage = new Gomo\I18n\Storage\Redis();
    $gen->setStorage($storage);

    file_put_contents($gen->getLangFilePath(), Yaml::dump($values), Gomo\I18n\Generator\Generator::YAML_INLINE);
  }

  public function testSimpleGenerator()
  {
    $this->resetLangFile('ja', array(
      "保存" => array(
        "value" => "",
        "files" => array(
          $this->basePath.'/sample/old.php',
        ),
      ),
    ));
    $gen = new Gomo\I18n\Generator\Generator();
    $gen
      ->setLang('ja')
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

    $this->assertCount(2, $entries['保存']->getFiles());

    $gen->saveFile();

    //保存ファイルの確認
    $result = $this->loadYaml('ja');
    $this->assertCount(2, $result);

    $keys = array_keys($result);
    $this->assertEquals('保存', $keys[0]);
    $this->assertEquals('戻る', $keys[1]);

    $this->assertCount(2, $result['保存']['files']);
    $this->assertEquals($this->basePath.'/sample/index.php', $result['保存']['files'][0]);
    $this->assertEquals($this->basePath.'/sample/old.php', $result['保存']['files'][1]);

    $this->assertCount(1, $result['戻る']['files']);
    $this->assertEquals($this->basePath.'/sample/index.php', $result['戻る']['files'][0]);
  }

  public function testRegexGenerator()
  {
    $this->resetLangFile('ja', array(
      "保存" => array(
        "value" => "",
        "files" => array(
          $this->basePath.'/sample/old.php',
        ),
      ),
    ));
    $gen = new Gomo\I18n\Generator\Generator();
    $gen
      ->setLang('ja')
      ->setDir($this->basePath.'/sample/lang');

    $gen->load();

    $entries = $gen->getEntries();
    $this->assertEquals(1, count($entries));

    //sample/index.phpを読み込む
    $gen->addEntries($this->basePath.'/sample/regex.php');

    $entries = $gen->getEntries();
    $this->assertCount(2, $entries);

    $gen->saveFile();

    //保存ファイルの確認
    $result = $this->loadYaml('ja');
    $this->assertCount(2, $result);

    $keys = array_keys($result);
    $this->assertEquals("Gomo\I18n::get('foo')", $keys[0]);
    $this->assertEquals('保存', $keys[1]);

    $this->assertCount(1, $result["Gomo\I18n::get('foo')"]['files']);
    $this->assertEquals($this->basePath.'/sample/regex.php', $result["Gomo\I18n::get('foo')"]['files'][0]);

    $this->assertCount(1, $result['保存']['files']);
    $this->assertEquals($this->basePath.'/sample/old.php', $result['保存']['files'][0]);
  }

  public function testRedis()
  {
    $this->resetLangFile('ja', array(
      "保存" => array(
        "value" => "",
        "files" => array(
          $this->basePath.'/sample/old.php',
        ),
      ),
      "desc for somthing" => array(
        "value" => <<<EOF
改行を含む
改行を含む
EOF
        ,
        "files" => array(
          $this->basePath.'/sample/old.php',
        ),
      ),
      "%s分コース" => array(
        "value" => "",
        "files" => array(
          $this->basePath.'/sample/old.php',
        )
      ),
      "%s/%s/%d" => array(
        "value" => "",
        "files" => array(
          $this->basePath.'/sample/old.php',
        )
      )
    ));

    $this->resetLangFile('en', array(
      "保存" => array(
        "value" => "Save",
        "files" => array(
          $this->basePath.'/sample/old.php',
        ),
      ),
      "%s分コース" => array(
        "value" => "%s minutes course",
        "files" => array(
          $this->basePath.'/sample/old.php',
        )
      ),
    ));
    $gen = new Gomo\I18n\Generator\Generator();
    $gen->setDir($this->basePath.'/sample/lang');
    $storage = new Gomo\I18n\Storage\Redis();
    $gen->setStorage($storage);

    $gen->clearStorage();

    $gen->setLang('ja')->updateStorage();
    $gen->setLang('en')->updateStorage();

    $storage = new Gomo\I18n\Storage\Redis();

    Gomo\I18n::setCurrent(new Gomo\I18n($storage, 'ja'));
    $this->assertEquals('保存', Gomo\I18n::get('保存'));
    $this->assertEquals("改行を含む".PHP_EOL."改行を含む", Gomo\I18n::get('desc for somthing'));
    $this->assertEquals('120分コース', Gomo\I18n::get('%s分コース', '120'));
    $this->assertEquals('foo/bar/30', Gomo\I18n::get('%s/%s/%d', 'foo', 'bar', 30));

    Gomo\I18n::setCurrent(new Gomo\I18n($storage, 'en'));
    $this->assertEquals('Save', Gomo\I18n::get('保存'));
    $this->assertEquals('120 minutes course', Gomo\I18n::get('%s分コース', '120'));
  }
}