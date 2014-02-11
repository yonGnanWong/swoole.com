<?php
$array = array();
//频道页
$array[] = array(
    'regx' => '^/([a-zA-Z_0-9]{2,16})\.html$',
    'mvc' => array('controller' => 'page', 'view' => 'index'),
    'get' => 'p',
);
//WIKI
$array[] = array(
    'regx' => '^/([a-zA-Z\_0-9\/]{2,32})\.md',
    'mvc' => array('controller' => 'wiki', 'view' => 'main'),
    'get' => 'p',
);

//详情页
$array[] = array(
    'regx' => '^/([a-zA-Z]{2,10})/(\d+)\.html$',
    'mvc' => array('controller' => 'cms', 'view' => 'detail'),
    'get' => 'app,id',
);
//APP首页
$array[] = array(
    'regx' => '^([a-zA-Z]{2,10})/(index\.htm|index\.html)$',
    'mvc' => array('controller' => 'cms', 'view' => 'index'),
    'get' => 'p',
);
//列表页首页
$array[] = array(
    'regx' => '^/([a-zA-Z]{2,10})/list_(\d+)\.html$',
    'mvc' => array('controller' => 'cms', 'view' => 'category'),
    'get' => 'app,cid',
);
//列表页分页
$array[] = array(
    'regx' => '^/([a-zA-Z]{2,10})/list_(\d+)_(\d+)\.html$',
    'mvc' => array('controller' => 'cms', 'view' => 'category'),
    'get' => 'app,cid,page',
);
return $array;