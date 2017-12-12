<?php
require dirname(__DIR__) . '/server/config.php';
Swoole\Error::$echo_html = true;
if ($_SERVER['HTTP_HOST'] == 'wiki.swoole.com')
{
    Swoole::$default_controller['controller'] = 'wiki';
}
Swoole::$php->runMVC();

