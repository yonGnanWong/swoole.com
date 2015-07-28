<?php
require __DIR__ . '/config.php';
define('MARKDOWN_LIB_DIR', __DIR__ . '/apps/classes');
Swoole\Error::$echo_html = true;
$php->runMVC();

