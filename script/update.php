<?php
define('ROOT', dirname(__DIR__));
const NEWS_MAX = 5;

require ROOT.'/server/config.php';

function getNews()
{
    $curl = new \Swoole\Client\CURL();
    $curl->addHeaders(array(
        'Referer' => 'https://www.oschina.net/p/swoole-server',
    ));
    $html = $curl->get('https://www.oschina.net/search?scope=news&q=swoole&sort_by_time=1');


    $dom = new Swoole\DOM\Tree($html);
    $list = $dom->find('ul#results > h3 > a');

    $news = array();
    foreach ($list as $li)
    {
        /**
         * @var $li  Swoole\DOM\Node
         */
        $news[] = array('title' => $li->text(), 'link' => $li->getAttribute('href'));
        if (count($news) > 5) break;
    }

    return $news;
}

function getLastVersion()
{
    $curl = new \Swoole\Client\CURL();
    $html = $curl->get('http://git.oschina.net/swoole/swoole/tags', null, 30);
    if ($html and preg_match('#/swoole/swoole/tree/v(1\.9\.\d+)#i', $html, $match))
    {
        return $match[1];
    }
    return false;
}

$news = getNews();
$version = getLastVersion();
if ($version === false)
{
    exit("get version failed.\n");
}

ob_start();
include __DIR__.'/templates/index.php';
$html = ob_get_clean();
file_put_contents(ROOT.'/web/index.html', $html);
