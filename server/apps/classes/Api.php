<?php
namespace App;

class Api
{
	static $swoole;
	static function sendmail($to, $from, $title, $msg)
	{
		$model = createModel('UserMail');
		$put['title'] = $title;
		$put['content'] = $msg;
		$put['fid'] = $from;
		$put['tid'] = $to;
		return $model->put($put);
	}
	
	static function feed($type, $uid, $tid=0, $event_id=0)
	{
		return \Swoole::$php->model->Feeds->send($type, $uid, $tid, $event_id);
	}
}
