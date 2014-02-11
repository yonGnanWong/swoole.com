<?php
$cache['master'] = array(
	'type'    => 'CMemcache',
    'host' => '127.0.0.1',
);
$cache['session'] = array(
    'type'    => 'CMemcache',
    'host' => '127.0.0.1',
);
return $cache;
