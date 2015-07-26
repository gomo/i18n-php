<?php
namespace Gomo\I18n\Storage;

interface Storage
{
  public function clear();
  public function set($lang, $key, $value);
  public function get($lang, $key);
}