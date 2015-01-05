<?php
$php->tpl->template_dir = WEBPATH.'/admin/templates';
$php->session->start();
Swoole\Auth::$session_prefix = 'admin_';
Swoole\Auth::$login_url = '/admin/login.php?';
Swoole\Auth::login_require();

$access = array();
require_once "../dict/acl.php";