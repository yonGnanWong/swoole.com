<?php
require __DIR__.'/../config.php';
$data = file_get_contents("./data/upload.log");
$client_id = 1;
/*
$p = new Swoole\Http\Parser;

$parts = explode("\r\n\r\n", $data, 2);

$head = $p->parseHeader($parts[0]);
$request = new Swoole\Request();
$request->head =  $head;
$request->meta = $head[0];
$request->body = $parts[1];
unset($head[0]);

$body = $p->parseBody($request);
var_dump($request->post, $request->file, strlen($request->body));

*/
$parts = explode("\r\n\r\n", $data ,2);

class TestHttp extends Swoole\Network\Protocol\HttpServer
{
    function onRequest($request)
    {
//        var_dump($request);
//        var_dump($this->requests);
        $this->log(__METHOD__);
    }
}
$p = new TestHttp;
$p->loadSetting("../swoole.ini"); //加载配置文件
$p->setLogger(new \Swoole\Log\EchoLog('/tmp/swoole.log')); //Logger
$p->onReceive('', $client_id, 1, $parts[0]."\r\n\r\n");
$p->onReceive('', $client_id, 1, $parts[1]);

/*
$ret = $p->checkData($client_id, $data);var_dump($ret);
$request = $p->request_tmp[$client_id];
$p->parseRequest($request);
var_dump($request);*/
