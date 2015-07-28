<?php
namespace Gomo;

use Gomo\I18n\Storage;

class I18n
{
  private $lang;
  private $storage;
  private static $current;

  public static function setCurrent(I18n $i18n)
  {
    self::$current = $i18n;
  }

  /**
   * self::$current::getVar()へのショートカット。
   * @see I18n::getVar()
   * @param string key[, ...]
   * @return string
   */
  public static function get()
  {
    return call_user_func_array(array(self::$current, 'getVal'), func_get_args());
  }

  /**
   * self::$current::getLang()へのショートカット。
   * @return string e.g. en|ja|fr
   */
  public static function lang()
  {
    return self::$current->getLang();
  }

  public function __construct(Storage\Storage $storage, $lang)
  {
    $this->storage = $storage;
    $this->lang = $lang;
  }

  public function getLang()
  {
    return $this->lang;
  }

  /**
   * 可変引数。sprintfの様な記法が可能。
   * @param string key[, ...]
   * @return string
   */
  public function getVal()
  {
    $args = func_get_args();
    $key = $args[0];
    unset($args[0]);

    $value = $this->storage->get($this->lang, $key);
    if($value === null){
      $value = $key;
    }

    if($args){
      return vsprintf($value, $args);
    } else {
      return $value;
    }
  }
}