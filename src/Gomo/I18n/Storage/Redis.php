<?php
namespace Gomo\I18n\Storage;

require_once __DIR__ . '/../../../../vendor/redisent/redis/src/Redisent/Redis.php';

class Redis implements Storage
{
  private $redis;

  public function __construct()
  {
    $this->redis = new \redisent\Redis();
    $this->select(0);
  }

  public function select($db)
  {
    $this->redis->select($db);
  }

  public function clear()
  {
    $this->redis->flushdb();
  }

  public function set($lang, $key, $value)
  {
    $this->redis->set($key.'@'.$lang, $value);
  }

  public function get($lang, $key)
  {
    return $this->redis->get($key.'@'.$lang);
  }
}