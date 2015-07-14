<?php
namespace Gomo;

use redisent\Redis;

class I18n
{
  private $lang;
  private $redisDb = 0;
  private $redis;
  private static $current;

  public static function setCurrent(I18n $i18n)
  {
    self::$current = $i18n;
  }

  public static function get($key)
  {
    return self::$current->getVal($key);
  }

  public function __construct($lang)
  {
    $this->redis = new Redis();
    $this->redis->select($this->redisDb);
    $this->lang = $lang;
  }

  public function getVal($key)
  {
    $value = $this->redis->get($key.'@'.$this->lang);
    return $value === null ? $key : $value;
  }
}