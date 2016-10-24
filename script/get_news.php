<?php
define('ROOT',  dirname(__DIR__));
require ROOT.'/server/config.php';

$curl = new \Swoole\Client\CURL();
$html = $curl->get('https://www.oschina.net/p/swoole-server/news');
$key = preg_match('#location\.href\="\?fromerr\=([a-z0-9]+)";#i', $html, $match);

$html = $curl->get('https://www.oschina.net/p/swoole-server/news?fromerr='.$match[1]);
$dom = new Swoole\DOM\Tree($html);
$list = $dom->find('ul.List > h3 > a');

$news = array();
foreach ($list as $li)
{
    /**
     * @var $li  Swoole\DOM\Node
     */
    $news[] = array('title' => $li->text(), 'link' => $li->getAttribute('href'));
    if (count($news) > 5) break;
}

ob_start();
include __DIR__.'/templates/index.php';
$html = ob_get_clean();
file_put_contents(ROOT.'/web/index.html', $html);
