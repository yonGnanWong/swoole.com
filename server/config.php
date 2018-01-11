<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

if (!empty($_SERVER['SERVER_NAME']))
{
    $protocol = 'http';
    if ($_SERVER['SERVER_PORT'] == 443)
    {
        $protocol .= 's';
    }
    define("WEBROOT", $protocol . '://' . $_SERVER['SERVER_NAME']);
}
else
{
    define("WEBROOT", 'https://www.swoole.com');
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
require __DIR__.'/apps/include/func.php';



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

Swoole::$php->beforeRequest(function () {
    $app = Swoole::$php;
    $list = $app->config['disabled'];
    if (!empty($list[$app->env['mvc']['controller']]))
    {
        $value = $list[$app->env['mvc']['controller']];
        if ($value == '*' or _string($value)->split(',')->contains($app->env['mvc']['view']))
        {
            $app->http->status(403);
            $error = true;
            $info = "当前网站板块已停止服务。";
            $links = [
                [
                    'url' => '/page/index/',
                    'text' => '返回首页',
                    'type' => 'default',
                ],
                [
                    'url' => '//group.swoole.com/',
                    'text' => 'Swoole 文档',
                    'type' => 'success',
                ],
                [
                    'url' => '//group.swoole.com/',
                    'text' => 'Swoole 社区',
                    'type' => 'success',
                ],
            ];
            include $app->tpl->template_dir . '/include/page.php';
            $app->http->finish();
        }
    }
});

//指定国际编码的方式
mb_internal_encoding('utf-8');
//$php->gzip();
