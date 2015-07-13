<?php
namespace Gomo;

use redisent\Redis;

class I18n
{
  private $lang;
  private $redisDb = 0;
  private $redis;

  public function __construct()
  {
    $this->redis = new Redis();
    $this->redis->select($this->redisDb);
  }

  public function setLang($lang)
  {
    $this->lang = $lang;
  }

  public function __i18n($key){
    $value = $this->redis->get($key.'@'.$this->lang);
    return $value === null ? $key : $value;
  }
}