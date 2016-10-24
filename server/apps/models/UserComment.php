<?php
namespace App\Model;
use Swoole;

class UserComment extends Swoole\Model
{
	//Here write Database table's name
	var $table = 'user_comment';

	function getByAid($app, $aid, &$pager = null)
	{
	    $gets['leftjoin'] = array(createModel('UserInfo')->table, createModel('UserInfo')->table.'.id='.$this->table.'.uid');
	    $gets['select'] = 'content,uid,uname,avatar,addtime';
	    $gets['aid'] = $aid;
	    $gets['app'] = $app;
	    $gets['order'] = 'addtime desc';
	    $gets['limit'] = 50;
	    return $this->gets($gets);
	}
}