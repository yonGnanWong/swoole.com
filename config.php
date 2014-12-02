<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

if (!empty($_SERVER['SERVER_NAME']))
{
    define("WEBROOT", 'http://' . $_SERVER['SERVER_NAME']);
}

define("TABLE_PREFIX", 'st');
define("SITENAME", 'Swoole_PHP开发社区');
//define("TPL_DIR",WEBPATH.'/site/'.SITENAME.'/templates');
//模板目录

//上传文件的位置
define('UPLOAD_DIR','/static/uploads');

//Login登录用户配置
define('LOGIN_TABLE','user_login');

require __DIR__.'/libs/lib_config.php';
require __DIR__.'/admin/func.php';
require LIBPATH.'/code/ns_warrper.php';

if (get_cfg_var('env.name') == 'dev')
{
    require __DIR__.'/apps/dev_config.php';
    Swoole::$php->config->setPath(APPSPATH.'/configs/dev/');
}
//Swoole\Config::$debug = true;

Swoole::$php->addHook(Swoole::HOOK_INIT, function(){
    $php = Swoole::getInstance();
    //动态配置系统
    $php->tpl->assign('_site_','/site/'.SITENAME);
    $php->tpl->assign('_static_', $php->config['site']['static']);
});

Swoole::$php->tpl->compile_dir = WEBPATH . "/cache/";
Swoole::$php->tpl->cache_dir = WEBPATH . "/cache/";

//指定国际编码的方式
mb_internal_encoding('utf-8');
//$php->gzip();
