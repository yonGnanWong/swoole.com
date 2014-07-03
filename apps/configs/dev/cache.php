<?php
$cache['master'] = array(
    'type' => 'Memcache',
);
$cache['session'] = array(
    'type' => 'FileCache',
    'cache_dir' => WEBPATH.'/cache/filecache',
);
return $cache;