<?php
namespace App\Controller;
use Swoole;

class Person extends \App\UserBase
{
    function notes()
    {
        $model = model('UserNote');
        if($_POST)
        {
            if(empty($_POST['title']) or empty($_POST['content']))
            {
                return Swoole\JS::js_back('标题和内容不能为空！');
            }
            $nid = (int)$_POST['id'];
            $in['title'] = trim($_POST['title']);
            $in['content'] = trim($_POST['content']);
            $in['uid'] = $this->uid;
            if($nid===0)
            {
                $in['addtime'] = date('Y-m-d H:i:s');
                $nid = $model->put($in);
            }
            else
            {
                $model->set($nid,$in);
            }
            $in['id'] = $nid;
            $this->swoole->tpl->assign('note',$in);
        }
        elseif(isset($_GET['id']))
        {
            $nid = (int)$_GET['id'];
            $note = $model->get($nid)->get();
            if($note['uid']!=$this->uid) exit;
            $this->swoole->tpl->assign('note',$note);
        }

        $gets['select'] = 'id,title,addtime';
        $gets['uid'] = $this->uid;
        $gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
        $gets['pagesize'] =15;
        $pager = '';
        $list = $model->gets($gets,$pager);
        $this->swoole->tpl->assign('list',$list);
        $pager->span_open = array();
        $pager = array('total'=>$pager->total,'render'=>$pager->render());
        $this->swoole->tpl->assign('pager',$pager);
        $this->swoole->tpl->display();
    }
    function question()
    {
        if($_POST)
        {
        	$_POST['title'] = trim($_POST['title']);
        	$_POST['content'] = trim($_POST['content']);

            if(empty($_POST['title']) or empty($_POST['content']))
            {
                return Swoole\JS::js_back('标题和内容不能为空！');
            }
            $q['gold'] = (int)$_POST['gold'];
            if($q['gold']>200)
            {
                return Swoole\JS::js_back('金币不得超过200');
            }
            $category = model('AskCategory')->get((int)$_POST['category']);
            $user = model('UserInfo')->get($this->uid);
            if($q['gold']>$user->gold)
            {
                return Swoole\JS::js_back('您没有足够的金币');
            }
            $q['title'] = $_POST['title'];
            $q['cid'] = $category['id'];
            $q['cname'] = $category['name'];
            $q['gold'] = (int)$_POST['gold'];
            $q['expire'] = time()+1296000;
            $q['uid'] = $this->uid;

            $cont['aid'] = model('AskSubject')->put($q);
            $cont['content'] = $_POST['content'];

            model('AskContent')->put($cont);
            $user->gold -= $q['gold'];
            $user->save();
            return Swoole\JS::js_goto('发布成功！','/ask/index/');
        }
        else
        {
            $user = model('UserInfo')->get($this->uid)->get();
            $forms = model('AskSubject')->getForms();
            $this->swoole->tpl->assign('user',$user);
            $this->swoole->tpl->assign('forms',$forms);
            $this->swoole->tpl->display();
        }
    }
    function myquestion()
    {
        $model = model('AskSubject');
        $gets['uid'] = $this->uid;
        $gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
        $gets['pagesize'] =15;
        if(isset($_GET['act']))
        {
            if($_GET['act']==1) $gets['mstatus'] = 2;
            else $gets['where'][] = 'mstatus<2';
        }
        $list = $model->gets($gets,$pager);
        $pager = array('total'=>$pager->total,'render'=>$pager->render());
        $this->swoole->tpl->assign('list',$list);
        $this->swoole->tpl->assign('pager',$pager);
        $this->swoole->tpl->display();
    }
    function post_mblog()
    {
        if(!empty($_POST['microblog']))
        {
            $model = model('MicroBlog');
            $in['content'] = trim($_POST['microblog']);
            $in['uid'] = $this->uid;
            $in['url_id'] = (int)$_POST['mblog_url'];
            $in['pic_id'] = (int)$_POST['mblog_pic'];
            $model->put($in);
            return Swoole\JS::js_goto('发布成功','/person/mblog/');
        }
    }
    function mblog()
    {
        $model = model('MicroBlog');
        $_user = model('UserInfo');

        if(!empty($_GET['del']))
        {
        	$del = (int)$_GET['del'];
        	$model->del($del);
        }

        $gets['uid'] = $this->uid;
        $gets['select'] = $model->table.'.id as id,uid,sex,content,nickname,avatar,addtime,reply_count';
        $gets['order'] = $model->table.'.id desc';
        $gets['leftjoin'] = array($_user->table,$_user->table.'.id='.$model->table.'.uid');
        $gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
        $gets['pagesize'] =15;
        $pager = '';
        $list = $model->gets($gets,$pager);
        $this->swoole->tpl->assign('list',$list);
        $pager->span_open = array();
        $pager = array('total'=>$pager->total,'render'=>$pager->render());
        $this->swoole->tpl->assign('pager',$pager);
        $this->swoole->tpl->display();
    }
    function comment()
    {

    }
    function mylinks()
    {
        if(isset($_GET['add']))
        {
            $add['title'] = trim($_POST['title']);
            $add['url'] = Func::parse_url(trim($_POST['url']));
            $add['uid'] = $this->uid;
            return model('UserLink')->put($add);
        }
    }

    function passwd()
    {
        if ($_POST)
        {
            if (empty($_POST['repass']) or empty($_POST['oldpass']) or empty($_POST['newpass']))
            {
                return Swoole\JS::js_back('参数不能为空！');
            }
            if ($_POST['repass'] != $_POST['newpass'])
            {
                return Swoole\JS::js_back('两次输入的密码不一致！');
            }
            if (strlen($_POST['repass']) < 6)
            {
                return Swoole\JS::js_back('密码长度不得少于6位！');
            }
            $u = model('UserInfo')->get($this->uid);
            if ($u['password'] != Swoole\Auth::makePasswordHash($u['username'], $_POST['oldpass']))
            {
                return Swoole\JS::js_back('旧密码错误！');
            }
            $u->password = Swoole\Auth::makePasswordHash($u['username'], $_POST['newpass']);
            $u->save();
            return Swoole\JS::js_back('修改成功！');
        }
        else
        {
            $this->swoole->tpl->display();
        }
    }

    function profile()
    {
        if($_POST)
        {
            if(empty($_POST['nickname']))
            {
                return Swoole\JS::js_back('昵称不能为空！');
            }
            if(!empty($_FILES['avatar']['name']))
            {
                global $php;
                $php->upload->thumb_width = 90;
                $php->upload->thumb_height = 120;
                $php->upload->thumb_qulitity = 90;
                $php->upload->base_dir = "/static/uploads/avatar";
                $upfile = $php->upload->save('avatar');
                if($upfile===false)
                {
                    return Swoole\JS::js_back('上传失败！');
                }
                $set['avatar'] = $_SESSION['user']['avatar'] = $upfile['thumb'];
            }

            $set['nickname'] = trim($_POST['nickname']);
            $set['intro'] = trim($_POST['intro']);
            $set['company'] = $_POST['company'];
            $set['blog'] = $_POST['blog'];
            $set['mobile'] = $_POST['mobile'];
            $set['sex'] = (int)$_POST['sex'];
            $set['education'] = (int)$_POST['education'];
            $set['skill'] = implode(',',$_POST['skill']);
            $set['php_level'] = (int)$_POST['php_level'];

            $u = model('UserInfo');
            $u->set($this->uid,$set);
            $_SESSION['user']['realname'] = $set['realname'];
            $_SESSION['user']['mobile'] = $set['mobile'];
            return Swoole\JS::js_back('修改成功！');
        }
        else
        {
            require WEBPATH.'/dict/forms.php';
            $_u = model('UserInfo');
            $u = $_u->get($this->uid)->get();

            $_skill = model('UserSkill')->getMap(array());
            $_forms['sex'] = Swoole\Form::radio('sex',$forms['sex'],$u['sex']);
            $_forms['education'] = Swoole\Form::select('education',$forms['education'],$u['education']);
            $_forms['skill'] = Swoole\Form::checkbox('skill',$_skill,$u['skill']);
            $_forms['level'] = Swoole\Form::radio('php_level',$forms['level'],$u['php_level']);

            $this->swoole->tpl->assign('user',$u);
            $this->swoole->tpl->assign('forms',$_forms);
            $this->swoole->tpl->display();
            //$this->view->showTrace();
        }
    }
    function index()
    {
        $this->mails();
    }
    function readmail()
    {
        //Error::dbd();
        if(empty($_GET['mid'])) die();
        $id = (int)$_GET['mid'];
        $_m = model('UserMail');
        $ms = $_m->get($id);
        if($ms->tid!=$this->uid and $ms->fid!=$this->uid) die('Access deny!');
        else
        {
            if($ms->tid == $this->uid and $ms->mstatus==0)
            {
                $ms->mstatus = 1;
                $ms->save();
            }

            $_e = model('UserInfo');
            $_e->select = 'id,nickname';
            $fuser = $_e->get($ms->fid)->get();
            $this->swoole->tpl->assign('ftype','user');
            $this->swoole->tpl->assign('fuser',$fuser);
            $this->swoole->tpl->assign('mail',$ms->get());
            $this->swoole->tpl->display();
        }
    }

    function delmail()
    {
        if(empty($_GET['mid'])) die();
        $id = (int)$_GET['mid'];
        $_m = model('UserMail');
        $ms = $_m->get($id);
        //发信人
        if($ms->fid==$this->uid)
        {
            if($ms->mstatus==5) $ms->delete();
            else
            {
                $ms->mstatus=4;
                $ms->save();
            }
            return Swoole\JS::js_back('删除成功');
        }
        //收信人
        elseif($ms->tid==$this->uid)
        {
            if($ms->mstatus==4) $ms->delete();
            else
            {
                $ms->mstatus=5;
                $ms->save();
            }
            return Swoole\JS::js_back('删除成功');
        }
        else Swoole\Http::response('Error!');
    }

    function mails()
    {
        //Error::dbd();
        $_m = model('UserMail');
        if(isset($_GET['act']) and $_GET['act']=='send')
        {
            $gets['fid'] = $this->uid;
            $gets['where'][] = 'mstatus!=4';
        }
        else
        {
            $gets['tid'] = $this->uid;
            $gets['where'][] = 'mstatus!=5';
        }
        $gets['pagesize'] = 12;
        $gets['page'] = isset($_GET['page'])?(int)$_GET['page']:1;
        $list = $_m->gets($gets,$pager);
        $pager = array('total'=>$pager->total,'render'=>$pager->render());
        $this->swoole->tpl->assign('pager',$pager);
        $this->swoole->tpl->assign('list',$list);
        $this->swoole->tpl->display();
    }

    function sendmail()
    {
        if($_POST)
        {
            if(empty($_POST['tid']) or empty($_POST['title']) or empty($_POST['content'])) die('错误的请求');
            $post['fid'] = $this->uid;
            $post['title'] = mb_substr($_POST['title'],0,48);
            $post['content'] = mb_substr($_POST['content'],0,300);
            $post['tid'] = $_POST['tid'];
            $_m = model('UserMail');
            $_m->put($post);
            return Swoole\JS::js_goto('发送成功','/person/mails/?act=send');
        }
        else
        {
            if(!empty($_GET['to']))
            {
                $u = model('UserInfo')->get((int)$_GET['to'])->get();
                $this->swoole->tpl->assign('to',$u);
            }
            $this->swoole->tpl->display();
        }
    }
    /**
     * 添加好友
     */
	function friend()
	{
		if(!empty($_GET['add']))
		{
			$fm = model('UserFriend');
			$get['frid'] = (int)$_GET['add'];
			$get['uid'] = $this->uid;
			$c = $fm->count($get);
			if($c>0)
			{
				return Swoole\JS::js_goto('你们已经是好友了！','/person/myfriends/');
			}
			else
			{
				$fm->put($get);
                return Swoole\JS::js_goto('添加好友成功！','/person/myfriends/');
			}
		}
	}
	/**
	 * 我的好友
	 */
	function myfriends()
	{
		$gw = new Swoole\GeneralView($this->swoole);
		$gw->setModel('UserFriend');
		$gets['uid'] = $this->uid;
		$gets['select'] = 'frid as uid,addtime,nickname,avatar,sex,addtime,lastlogin';
		$gets['leftjoin'] = array('user_login','frid=user_login.id');
		$gw->setParam($gets);
		$gw->action_list();
		$this->swoole->tpl->display();
	}
}