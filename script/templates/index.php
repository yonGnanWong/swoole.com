<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Swoole®: PHP的异步、并行、高性能网络通信引擎</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Swoole: PHP的异步、并行、高性能网络通信引擎，支持TCP长连接，Websocket，Mqtt等协议。广泛用于手机app、手游服务端、网络游戏服务器、聊天室、硬件通讯、智能家居、车联网、物联网等领域的开发。">
    <meta name="keywords" content="PHP,Swoole,PHP异步,高并发,网络通信,并行,TCP,UDP,PHP应用服务器,Server,WebSocket,TCP长连接,Mqtt,WebIM,聊天,推送系统,PUSH系统">

    <!-- Le styles -->
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
        #feature_list li{
			width:  160px;
		}
    </style>
    <link href="/static/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/static/css/code.css" rel="stylesheet">
    <script src="/static/js/rainbow-custom.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="/static/bootstrap/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/static/bootstrap/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="/static/bootstrap/ico/favicon.png">
    <base target="_blank" />
</head>

<body>

<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="http://www.swoole.com/">
            Swoole官方网站</a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li class="active"><a target="_self" href="/">首页</a></li>
                    <li><a target="_self" href="http://wiki.swoole.com/">文档</a></li>
					<li><a target="_self" href="http://wiki.swoole.com/wiki/page/prid-1-p-project/road_map.html">路线图</a></li>
					<li><a target="_self" href="http://wiki.swoole.com/wiki/page/prid-1-p-project/change_log.html">更新记录</a></li>
					<li><a href="http://group.swoole.com">社区</a></li>
                    <li><a href="http://wiki.swoole.com/wiki/index/prid-13.html">视频教程</a></li>
                    <li><a target="_self" href="http://wiki.swoole.com/wiki/page/prid-1-p-author.html">开发者</a></li>
                    <li><a target="_self" href="http://wiki.swoole.com/wiki/page/p-case.html">用户案例</a></li>
                    <li><a href="http://wiki.swoole.com/wiki/page/p-donate.html">捐赠</a></li>
					<li><a href="https://github.com/swoole/swoole-src/releases">下载</a></li>
                    <li><a href="https://rawgit.com/tchiotludo/swoole-ide-helper/english/docs/index.html">English Document</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div id="banner-ad"></div>
    <div class="hero-unit">
		<h2 style="margin-bottom: 25px;">Swoole®：重新定义PHP</h2>
        <p>
		<span><img src="/static/images/swoole.png" width="160" style="float:left; margin-right: 25px; margin-top: 15px;" />
        </span>
         <p>PHP的异步、并行、高性能网络通信引擎，使用纯C语言编写，提供了<a href="/wiki/page/p-server.html">PHP语言的异步多线程服务器</a>，<a href="/wiki/page/p-client.html">异步TCP/UDP网络客户端</a>，<a href="/wiki/page/517.html">异步MySQL</a>，<a href="/wiki/page/p-redis.html">异步Redis</a>，<a href="https://github.com/swoole/framework/blob/master/tests/async_mysql.php" target="_blank">数据库连接池</a>，<a href="/wiki/page/134.html">AsyncTask</a>，<a href="http://wiki.swoole.com/wiki/page/289.html">消息队列</a>，<a href="/wiki/page/244.html">毫秒定时器</a>，<a href="/wiki/page/183.html">异步文件读写</a>，<a href="/wiki/page/186.html">异步DNS查询</a>。 Swoole内置了<a href="/wiki/page/326.html">Http/WebSocket服务器端</a>/<a href="/wiki/page/p-http_client.html">客户端</a>、<a href="/wiki/page/326.html">Http2.0服务器端</a>。</p>
        <p>除了异步IO的支持之外，Swoole为PHP多进程的模式设计了多个并发数据结构和IPC通信机制，可以大大简化多进程并发编程的工作。其中包括了<a href="/wiki/page/p-atomic.html">并发原子计数器</a>，<a href="/wiki/page/p-table.html">并发HashTable</a>，<a href="/wiki/page/p-channel.html">Channel</a>，<a href="/wiki/page/p-lock.html">Lock</a>，<a href="/wiki/page/363.html">进程间通信IPC</a>等丰富的功能特性。</p>
        
<p><a href="https://git.oschina.net/swoole/swoole/tree/2.0.1">Swoole2.0</a>支持了类似Go语言的<strong><a href="http://wiki.swoole.com/wiki/page/p-coroutine.html">协程</a></strong>，可以使用完全同步的代码实现异步程序。PHP代码无需额外增加任何关键词，底层自动进行协程调度，实现异步。</p>

        Swoole可以广泛应用于互联网、移动通信、企业软件、云计算、网络游戏、物联网（IOT）、车联网、智能家居等领域。
        使用PHP+Swoole作为网络通信框架，可以使企业IT研发团队的效率大大提升，更加专注于开发创新产品。
        <p style="margin-top: 25px;">
            <a href="https://github.com/swoole/swoole-src" class="btn btn-primary">源代码(GitHub)</a>&nbsp;&nbsp;
            <a href="https://git.oschina.net/swoole/swoole" class="btn btn-primary">源代码(开源中国-码云)</a>&nbsp;&nbsp;
           <a href="http://git.oschina.net/swoole/swoole/issues/new" class="btn btn-danger">提交Bug</a>&nbsp;&nbsp;
           <a href="http://git.oschina.net/swoole/swoole/issues/new" class="btn">提建议</a>&nbsp;&nbsp;
           <a class="btn btn-sm btn-success" href="https://git.oschina.net/swoole/swoole/tree/v<?=$version?>">
             <i class="glyphicon glyphicon-download"></i> &nbsp; 下载 <span style="font-size: 60%;">(<?=$version?>)</span> &nbsp;
		   </a>
          <a class="btn btn-sm btn-success" href="https://git.oschina.net/swoole/swoole/tree/v2.0.7">
             <i class="glyphicon glyphicon-download"></i> &nbsp; 下载 <span style="font-size: 60%;">(2.0.7)</span> &nbsp;
		   </a>
        <a href="http://compiler.swoole.com/" class="btn btn-warning">Swoole Compiler (PHP代码加密器)</a>&nbsp;&nbsp;
        </p>
    </div>

    <div class="row">
        <div class="span3" style="width: 290px;">
            <h3>新闻</h3>
            <ul class="nav nav-list">
                <?php foreach($news as $v): ?>
                <li style="height: 30px;">
                    <a href="<?=$v['link']?>" title="<?=$v['title']?>">
                    <?php
                    if (mb_strlen($v['title']) > 24) echo mb_substr($v['title'], 0, 24).'...'; else echo $v['title'];
                    ?>
                    </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="span4" style="width: 350px;">
            <style >
                .checklist li {
                    list-style-image: url("/static/image/li-checks.png");
                    height: 36px;
                    float: left;
                    width: 170px;
                }
            </style>
            <h3>优势</h3>
            <ul class="checklist nav nav-list" id="feature_list">
                <li>纯C编写性能极强</li>
                <li>简单易用开发效率高</li>
                <li>事件驱动异步非阻塞</li>
                <li>并发百万TCP连接</li>
                <li>TCP/UDP/UnixSock</li>
                <li>服务器端/客户端</li>
                <li>支持异步/同步/协程</li>
                <li>支持多进程/多线程</li>
                <li>CPU亲和性/守护进程</li>
                <li>支持IPv4/IPv6网络</li>
            </ul>
        </div>
        <div class="span5">
            <h3>案例</h3>
            <p style="line-height: 180%">swoole目前已被多家移动互联网、物联网、网络游戏、手机游戏企业使用，替代了C++、Java等复杂编程语言来实现网络服务器程序。
               使用PHP+Swoole，开发效率可以大大提升。<br/>
                官方提供了基于swoole扩展开发的<a href="http://git.oschina.net/swoole/swoole_framework">PHP网络框架</a>，
                支持Http，FastCGI，WebSocket，FTP，SMTP，<a href="http://git.oschina.net/swoole/swoole_framework/blob/master/libs/Swoole/Client/RPC.php">RPC</a>等网络协议
                <br/>swoole在美国，英国，法国，印度等国家都有用户分布，在国内的
                    <a href="http://wiki.swoole.com/wiki/page/p-tencent.html">腾讯</a>、
                    <a href="http://wiki.swoole.com/wiki/page/p-baidu.html">百度</a>、阿里巴巴、YY语音等多家知名互联网公司均有使用。
            </p>
        </div>
    </div>
    <hr/>
    <style>
        .line180 pre {
            line-height: 180%;
        }
    </style>
    <div class="row line180">
    <div class="span6">
    <h4>HttpServer</h4>
    <pre><code class="php" data-language="php">$serv = new Swoole\Http\Server("127.0.0.1", 9502);

$serv->on('Request', function($request, $response) {
    var_dump($request->get);
    var_dump($request->post);
    var_dump($request->cookie);
    var_dump($request->files);
    var_dump($request->header);
    var_dump($request->server);

    $response->cookie("User", "Swoole");
    $response->header("X-Server", "Swoole");
    $response->end("<h1>Hello Swoole!</h1>");
});

$serv->start();</code></pre>
</div>
        <div class="span6">
        <h4>WebSocket Server</h4>
            <pre><code class="php" data-language="php">$serv = new Swoole\Websocket\Server("127.0.0.1", 9502);

$serv->on('Open', function($server, $req) {
    echo "connection open: ".$req->fd;
});

$serv->on('Message', function($server, $frame) {
    echo "message: ".$frame->data;
    $server->push($frame->fd, json_encode(["hello", "world"]));
});

$serv->on('Close', function($server, $fd) {
    echo "connection close: ".$fd;
});

$serv->start();</code></pre>
        </div>
    <div class="span6">
            <h4>TCP Server</h4>
            <pre><code class="php" data-language="php">$serv = new Swoole\Server("127.0.0.1", 9501);
$serv->set(array(
    'worker_num' => 8,   //工作进程数量
    'daemonize' => true, //是否作为守护进程
));
$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();</code></pre>
            </div>
    <div class="span6">
        <h4>TCP Client</h4>
            <pre><code class="php" data-language="php">$client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
//设置事件回调函数
$client->on("connect", function($cli) {
    $cli->send("hello world\n");
});
$client->on("receive", function($cli, $data){
    echo "Received: ".$data."\n";
});
$client->on("error", function($cli){
    echo "Connect failed\n";
});
$client->on("close", function($cli){
    echo "Connection close\n";
});
//发起网络连接
$client->connect('127.0.0.1', 9501, 0.5);</code></pre>
    </div>

<div class="span6">
    <h4>异步MySQL</h4>
    <pre><code class="php" data-language="php">$db = new Swoole\MySQL;
$server = array(
    'host' => '127.0.0.1',
    'user' => 'test',
    'password' => 'test',
    'database' => 'test',
);

$db->connect($server, function ($db, $result) {
    $db->query("show tables", function (Swoole\MySQL $db, $result) {
        if ($result === false) {
            var_dump($db->error, $db->errno);
        } elseif ($result === true) {
            var_dump($db->affected_rows, $db->insert_id);
        } else {
            var_dump($result);
            $db->close();
        }
    });
});
</code></pre>
</div>

<div class="span6">
            <h4>异步Redis/异步Http客户端</h4>
    <pre><code class="php" data-language="php">$redis = new Swoole\Redis;
$redis->connect('127.0.0.1', 6379, function ($redis, $result) {
    $redis->set('test_key', 'value', function ($redis, $result) {
        $redis->get('test_key', function ($redis, $result) {
            var_dump($result);
        });
    });
});

$cli = new Swoole\Http\Client('127.0.0.1', 80);
$cli->setHeaders(array('User-Agent' => 'swoole-http-client'));
$cli->setCookies(array('test' => 'value'));

$cli->post('/dump.php', array("test" => 'abc'), function ($cli) {
    var_dump($cli->body);
    $cli->get('/index.php', function ($cli) {
        var_dump($cli->cookies);
        var_dump($cli->headers);
    });
});
</code></pre>
</div>


    <div class="span6">
        <h4>Async-IO</h4>
            <pre><code class="php" data-language="php">$fp = stream_socket_client("tcp://127.0.0.1:80", $code, $msg, 3);
$http_request = "GET /index.html HTTP/1.1\r\n\r\n";
fwrite($fp, $http_request);
Swoole\Event::add($fp, function($fp){
    echo fread($fp, 8192);
    swoole_event_del($fp);
    fclose($fp);
});
Swoole\Timer::after(2000, function() {
    echo "2000ms timeout\n";
});
Swoole\Timer::tick(1000, function() {
    echo "1000ms interval\n";
});
</code></pre>
    </div>
    <div class="span6">
            <h4>异步任务</h4>
            <pre><code class="php" data-language="php">$serv = new Swoole\Server("127.0.0.1", 9502);
$serv->set(array('task_worker_num' => 4));
$serv->on('Receive', function($serv, $fd, $from_id, $data) {
    $task_id = $serv->task("Async");
    echo "Dispath AsyncTask: id=$task_id\n";
});
$serv->on('Task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id=$task_id]".PHP_EOL;
    $serv->finish("$data -> OK");
});
$serv->on('Finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});
$serv->start();</code></pre>
        </div>
    </div>
    <hr />
    <footer>
        <p>Swoole® 是 Swoole Inc. 公司所有的注册商标。 &copy; Swoole开源项目 2008 - 2017 备案号：京ICP备14049466号-7 官方QQ群：193772828 开发组邮件列表：
        <a href="mailto:team@swoole.com">team@swoole.com</a>
        </p>
    </footer>

</div>
<script src="/static/bootstrap/js/bootstrap.min.js"></script>
<div style="display: none">
<script type="text/javascript">
$.get("http://group.swoole.com/ad.php", function(data) {
    if (data) {        
        $('#banner-ad').html(data);
    } else {
        $('#banner-ad').hide();
    }
});
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F4967f2faa888a2e52742bebe7fcb5f7d' type='text/javascript'%3E%3C/script%3E"));
</script>
</div>
</body>
</html>
