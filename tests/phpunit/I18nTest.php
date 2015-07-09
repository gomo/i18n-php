<?php

class I18nTest extends PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
      $i18n = new Gomo\I18n();
      $this->assertEquals('テスト', $i18n->__i18n('テスト'));
    }
}