<?php
require __DIR__.'/config.php';

//require __DIR__'/phar://swoole.phar';
//Swoole\Config::$debug = true;
Swoole\Error::$echo_html = false;

$AppSvr = new Swoole\Protocol\AppServer();
$AppSvr->loadSetting("./swoole.ini"); //加载配置文件
$AppSvr->setAppPath(__DIR__.'/apps/'); //设置应用所在的目录
$AppSvr->setDocumentRoot(__DIR__);
$AppSvr->setLogger(new \Swoole\Log\FileLog('/tmp/swoole.log')); //Logger

$server = new \Swoole\Network\Server('0.0.0.0', 9501);
$server->setProtocol($AppSvr);
//$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 2, 'max_request' => 1000));
