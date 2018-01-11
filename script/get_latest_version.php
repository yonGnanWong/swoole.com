<?php
define('ROOT', dirname(__DIR__));
require ROOT . '/server/config.php';

$curl = new \Swoole\Client\CURL();
$html = $curl->get('https://gitee.com/swoole/swoole/tags', null, 30);
if ($html and preg_match('#/swoole/swoole/tree/v(1\.\d+\.\d+)#i', $html, $match))
{
    $version1 = $match[1];
    Swoole::$php->redis->set('swoole:latest:version1', $version1);
}
if ($html and preg_match('#/swoole/swoole/tree/v(2\.\d+\.\d+)#i', $html, $match))
{
    $version2 = $match[1];
    Swoole::$php->redis->set('swoole:latest:version2', $version2);
}
