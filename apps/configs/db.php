<?php
$db['master'] = array(
		'type'    => Swoole\Database::TYPE_MYSQLi, //Database Driver，可以选择PdoDB , MySQL, MySQL2(MySQLi) , AdoDb(需要安装adodb插件)
		'host'    => "127.0.0.1",
		'dbms'    => 'mysql',
		'engine'  => 'MyISAM',
		'user'    => "root",
		'passwd'  => 'root',
		'name'    => "www4swoole",
		'charset' => "utf8",
		'setname' => true,
);

return $db;
