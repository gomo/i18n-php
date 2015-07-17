<?php
namespace Gomo;

require_once __DIR__ . '/../../vendor/redisent/redis/src/Redisent/Redis.php';
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

  public function __construct($lang)
  {
    $this->redis = new Redis();
    $this->redis->select($this->redisDb);
    $this->lang = $lang;
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

    $value = $this->redis->get($key.'@'.$this->lang);
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