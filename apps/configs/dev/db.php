<?php
$db['master'] = array(
		'type'    => Swoole\Database::TYPE_MYSQLi, //Database Driver，可以选择PdoDB , MySQL, MySQL2(MySQLi) , AdoDb(需要安装adodb插件)
		'host'    => SAE_MYSQL_HOST_M,
		'port'    => SAE_MYSQL_PORT,
		'dbms'    => 'mysql',
		'engine'  => 'MyISAM',
		'user'    => SAE_MYSQL_USER,
		'passwd'  => SAE_MYSQL_PASS,
		'name'    => SAE_MYSQL_DB,
		'charset' => "utf8",
		'setname' => true,
);
return $db;