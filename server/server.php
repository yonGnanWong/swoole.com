<?php
require __DIR__.'/config.php';

//require __DIR__'/phar://swoole.phar';
//Swoole\Config::$debug = true;
Swoole\Error::$echo_html = false;

//设置PID文件的存储路径
Swoole\Network\Server::setPidFile(__DIR__ . '/server.pid');

/**
 * 显示Usage界面
 * php app_server.php start|stop|reload
 */
Swoole\Network\Server::start(function ($options)
{
    $config = array(
        'document_root' => WEBPATH,
        'worker_num' => 1,
//        'max_request' => 1000,
        //'log_file' => __DIR__.'/logs/swoole.log',
        'enable_static_filter' => true,
        'document_root' => dirname(__DIR__) .'/web',
    );
    Swoole::$enableCoroutine = true;
    Swoole::$php->runHttpServer('0.0.0.0', 9503, $config);
//    $AppSvr = new Swoole\Protocol\AppServer();
//    $AppSvr->loadSetting("./swoole.ini"); //加载配置文件
//    $AppSvr->setAppPath(__DIR__.'/apps/'); //设置应用所在的目录
//    $AppSvr->setDocumentRoot(__DIR__);
//    $AppSvr->setLogger(new \Swoole\Log\FileLog('/tmp/swoole.log')); //Logger
//
//    $server = new \Swoole\Network\Server('0.0.0.0', 9503);
//    $server->setProtocol($AppSvr);
//    $server->setProcessName("webserver_swoole.com");
//    $server->run(array('worker_num' => 16, 'max_request' => 1000));
});
