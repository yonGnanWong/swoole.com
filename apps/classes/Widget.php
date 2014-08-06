<?php
namespace App;
use Swoole;

class Widget
{
    static function photoDetail($pid,$uid)
    {
        $param['uid'] = $uid;
        $param['id'] = $pid;
        $param['limit'] = 1;
        $photo = \Swoole::$php->model->UserPhoto->gets($param);

        if(empty($photo[0]['id']))
        {
            Swoole\JS::js_back('还没有上传照片！');
        	exit;
        }
        $photo = $photo[0];

        $param1['uid'] = $uid;
        $param1['where'] = "id>".$pid;
        $param1['select'] = 'id';
        $param1['order'] = 'id asc';
        $param1['limit'] = 1;
        $nextid = \Swoole::$php->model->UserPhoto->gets($param1);

        if(empty($nextid))
        {
            $first['uid'] = $param['uid'];
            $first['limit'] = 1;
            $first['order'] = 'id ASC';
            $first['select'] = 'id';
            $nextid = \Swoole::$php->model->UserPhoto->gets($first);
        }

        $param2['uid'] = $param['uid'];
        $param2['where'] = 'id<'.$pid;
        $param2['select'] = 'id';
        $param2['limit'] = 1;
        $perid = \Swoole::$php->model->UserPhoto->gets($param2);

        if(empty($perid))
        {
            $second['uid'] = $param['uid'];
            $second['limit'] = 1;
            $second['select'] = 'id';
            $second['order'] = 'id DESC';
            $perid = \Swoole::$php->model->UserPhoto->gets($second);
        }
        \Swoole::$php->tpl->assign('perid',$perid);
        \Swoole::$php->tpl->assign('nextid',$nextid);
        \Swoole::$php->tpl->assign('photo',$photo);
    }

    static function comment($app,$aid)
    {
        $model = model('UserComment');
        $userinfo = model('UserInfo');

        $gets['leftjoin'] = array($userinfo->table,$userinfo->table.'.id='.$model->table.'.uid');
	    $gets['select'] = 'content,uid,uname,avatar,addtime';
	    $gets['aid'] = $aid;
	    $gets['app'] = $app;
	    $gets['order'] = 'addtime desc';
	    $gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
	    $gets['pagesize'] = Swoole::$php->config['comment']['pagesize'];
	    $comments = $model->gets($gets,$pager);
	    $pager->fragment = 'comment';
	    $pager = array('total'=>$pager->total,'render'=>$pager->render());
        \Swoole::$php->tpl->assign('comments',$comments);
        \Swoole::$php->tpl->assign('pager',$pager);
    }
}