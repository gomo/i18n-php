<?php
namespace Gomo;

use redisent\Redis;

class I18n
{
  private $lang;
  private $redisDb = 0;
  private $redis;
  private static $current;

  public static function current(I18n $i18n = null)
  {
    if($i18n){
      self::$current = $i18n;
    } else {
      return self::$current;
    }
  }

  public function __construct($lang)
  {
    $this->redis = new Redis();
    $this->redis->select($this->redisDb);
    $this->lang = $lang;
  }

  public function __i18n($key)
  {
    $value = $this->redis->get($key.'@'.$this->lang);
    return $value === null ? $key : $value;
  }
}