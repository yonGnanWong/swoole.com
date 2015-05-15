<?php
define('WEBPATH', __DIR__);
define('MARKDOWN_LIB_DIR', __DIR__ . '/../apps/classes');
require __DIR__ . '/../libs/lib_config.php';

$config = [
    'document_root' => realpath(__DIR__ . '/../'),
    'worker_num'    => 8,
    'dispatch_mode' => 3,
    'user'          => 'nginx',
    'group'         => 'nginx',
    'log_file'      => __DIR__ . '/swoole.log',
    'daemonize'     => true
];

if (!empty($argv[1]) and $argv[1] == 'dev')
{
    $config['daemonize'] = false;
    $config['max_request'] = 1;
}

$php = Swoole::getInstance();
$php->runHttpServer('0.0.0.0', 9508, $config);