<?php
require __DIR__.'/config.php';
Swoole\Error::$echo_html = true;
$php->runMVC();

