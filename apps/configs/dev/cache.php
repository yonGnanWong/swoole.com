<?php
$cache['master'] = array(
    'type' => 'CMemcache',
);
$cache['session'] = array(
    'type' => 'FileCache',
    'cache_dir' => WEBPATH.'/cache/filecache',
);
return $cache;