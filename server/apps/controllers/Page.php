<?php
namespace App\Controller;
use App;
use Swoole;
use ZenAPI\Exception;
use ZenAPI\QqClient;
use ZenAPI\QqOAuth2;

require_once APPSPATH.'/include/libweibo/saetv2.ex.class.php';

class Page extends App\FrontPage
{
	public $pagesize = 10;
	function __construct($swoole)
	{
		parent::__construct($swoole);
	}

    function verify()
    {
        $this->session->start();
        $this->http->header('Content-Type', 'image/jpeg');
        $verifyCode = Swoole\Image::verifycode_gd();
        $_SESSION['authcode'] = $verifyCode['code'];
        return $verifyCode['image'];
    }

	function callback_weibo()
	{
        session();
        if (empty($_GET['code']))
        {
            $this->http->redirect('/page/login/');
            return;
        }

        $conf = $this->config['oauth']['weibo'];
        $oauth = new \SaeTOAuthV2($conf['appid'], $conf['skey']);
        $keys['code'] = $_GET['code'];
        $keys['redirect_uri'] = $conf['callback'];

        $token = $oauth->getAccessToken('code', $keys);
        if ($token)
        {
            $_SESSION['weibo_token'] = $token;
            $client = new \SaeTClientV2($conf['appid'], $conf['skey'], $token['access_token']);
            $uid = $client->get_uid();
            $userinfo = $client->show_user_by_id($uid['uid']);
            if (!isset($userinfo['id']))
            {
                return "请求错误.";
            }
            $model = createModel('UserInfo');
            $username = 'sina_' . $userinfo['id'];
            $u = $model->get($username, 'username');
            //不存在，则插入数据库
            if (!$u->exist())
            {
                $u =  $model->get($username, 'qq_uid');
                if (!$u->exist())
                {
                    $user['username'] = $username;
                    $user['nickname'] = $userinfo['name'];
                    $user['avatar'] = $userinfo['avatar_large'];
                    $user['blog'] = $userinfo['url'];
                    $user['lastlogin'] = Swoole\Tool::now();
                    $user['lastip'] = $this->request->getClientIP();
                    list($user['province'], $user['city']) = explode(' ', $userinfo['location']);
                    //插入到表中
                    $user['id'] = $model->put($user);
                    $uid = $user['id'];
                }
                else
                {
                    goto update;
                }
            }
            else
            {
                update:
                $u->nickname = $userinfo['name'];
                $u->avatar = $userinfo['avatar_large'];
                $u->blog = $userinfo['url'];
                $u->save();
                $user = $u->get();
                $uid = $user['id'];
            }
            //写入SESSION
            $_SESSION['isLogin'] = 1;
            $_SESSION['user_id'] = $uid;
            $_SESSION['user'] = $user;
            $this->loginSucess();
        }
	}

    /**
     * 登录成功
     * @param $returnJson
     * @return string
     */
    protected function loginSucess($returnJson = false)
    {
        $this->setLoginStat();
        $refer = isset($_GET['refer']) ? $_GET['refer'] : WEBROOT . '/person/index/';
        if (!empty($_GET['return_token']))
        {
            $token = Swoole\RandomKey::string(32);
            $user = $_SESSION['user'];
            App\Api::userInfoSafe($user);
            App\Api::updateAvatarUrl($user);
            $this->cache->set('login_token_' . $token, $user, 86400);
            $refer = Swoole\Tool::urlAppend($refer, array('token' => $token));
        }
        if (!$returnJson)
        {
            $this->http->redirect($refer);
        }
        else
        {
            return $this->json(['url' => $refer]);
        }
    }

    function callback_qq()
    {
        session();
        if (empty($_GET['code']))
        {
            $this->http->redirect('/page/login/');
            return;
        }

        $conf = $this->config['oauth']['qq'];
        Swoole\Loader::addNameSpace('ZenAPI', APPSPATH . '/include/zenapi');
        $oauth = new QqOAuth2($conf['appid'], $conf['skey']);
        $keys['code'] = $_GET['code'];
        $keys['redirect_uri'] = $conf['callback'];

        $token = $oauth->getAccessToken('code', $keys);
        if ($token)
        {
            $openid = $oauth->getOpenid($token['access_token']);
            if (empty($openid['openid']))
            {
                return "请求错误. 错误码：{$openid['ret']}\n";
            }

            $_SESSION['qq_token'] = $token;
            $client = new QqClient($token['access_token'], $conf['appid'], $openid['openid']);
            $userinfo = $client->get('user/get_user_info');
            if (!isset($userinfo['ret']) and $userinfo['ret'] != 0)
            {
                return "请求错误. 错误码：{$userinfo['ret']}\n";
            }
            $model = createModel('UserInfo');
            $username = trim($openid['openid']);
            if (empty($username))
            {
                throw new Exception("QQ登录出错了");
            }

            $u = $model->get($username, 'username');
            //不存在，则插入数据库
            if (!$u->exist())
            {
                $u =  $model->get($username, 'qq_uid');
                if (!$u->exist())
                {
                    $user['qq_uid'] = $username;
                    $user['nickname'] = $userinfo['nickname'];
                    $user['avatar'] = $userinfo['figureurl_2'];
                    $user['birth_year'] = $userinfo['year'];
                    $user['province'] = $userinfo['province'];
                    $user['city'] = $userinfo['city'];
                    $user['sex'] = $userinfo['gender'] == '男' ? 1 : 2;
                    $user['lastlogin'] = Swoole\Tool::now();
                    $user['lastip'] = $this->request->getClientIP();
                    //插入到表中
                    $user['id'] = $model->put($user);
                    $uid = $user['id'];
                }
                else
                {
                    goto update;
                }
            }
            else
            {
                update:
                $u->nickname = $userinfo['nickname'];
                $u->avatar = $userinfo['figureurl_2'];
                $u->province = $userinfo['province'];
                $u->city = $userinfo['city'];
                $u->qq_uid = $username;
                $u->save();
                $user = $u->get();
                $uid = $user['id'];
            }
            //写入SESSION
            $_SESSION['isLogin'] = 1;
            $_SESSION['user_id'] = $uid;
            $_SESSION['user'] = $user;
            $this->loginSucess();
        }
    }

	function flist()
	{
		//Error::dbd();
		//查询根分类
		$ftype = $this->swoole->model->CmsType->get($_GET['f'],'name')->get();

		//查询相关链接
		$param_rel['limit'] = 10;
		$param_rel['gfid'] = $ftype['id'];
		$rel_news = $this->swoole->model->CmsNews->gets($param_rel);
		$this->swoole->tpl->assign('rel_news',$rel_news);
		$this->swoole->tpl->assign('rel','rel');//标识为有相关链接的页面

		//查询新闻动态分类
		$param['gfid']  = $ftype['id'];
		$param['order'] = 'id asc';
		$type = $this->swoole->model->CmsType->gets($param);

		foreach($type as $key => &$val){
			$val['title'] = $val['typename'];
		}

		$gets['limit'] = 6;
		$gets['select'] = 'id,ftitle,addtime';
		foreach($type as $key => &$val)
		{
			$gets['tid'] = $val['id'];
			$val['list'] = $this->swoole->model->CmsNews->gets($gets);
			$val['stitle'] = $val['typename'];
			$val['tid'] = $val['id'];
		}
		$this->swoole->tpl->assign('ftype',$ftype);
		$this->swoole->tpl->assign('pagelist',$type);
		$this->swoole->tpl->assign('ltitle',$ftype['typename']);
		$this->swoole->tpl->display('page_news_index.html');
	}

	function detail()
	{
		$pagenews = $this->swoole->model->CmsNews->get((int)$_GET['d'])->get();
		//查询根分类
		$ftype = $this->swoole->model->CmsType->get($pagenews['gfid'])->get();
		$this->swoole->tpl->assign('ftype',$ftype);

		//查询相关链接
		$param_rel['limit'] = 10;
		$param_rel['gfid'] = $ftype['id'];
		$rel_news = $this->swoole->model->CmsNews->gets($param_rel);
		$this->swoole->tpl->assign('rel_news',$rel_news);
		$this->swoole->tpl->assign('rel','rel');//标识为有相关链接的页面

		//查询新闻动态分类
		$param['fid']  = $ftype['id'];
		$param['order'] = 'id asc';
		$type = $this->swoole->model->CmsType->gets($param);

		foreach($type as $key => &$val){
			$val['title'] = $val['typename'];
		}
		$this->swoole->tpl->assign('pagelist',$type);

		$this->swoole->tpl->assign('ftitle',$pagenews['ftitle']);
		#######为了兼容标题硬性修改##########
		$newst = $this->swoole->model->CmsType->get($pagenews['fid'])->get();
		$newst['title'] = $newst['typename'];
		$newst['content'] = $pagenews['content'];

		$news['title'] = $typename;
		$news['descript'] = $typename;
		$news['name'] = 'news';
		$this->swoole->tpl->assign('news',$news);
		$this->swoole->tpl->assign('page',$newst);
		#######################
		$this->swoole->tpl->display('page_news_detail.html');
	}

    function index()
    {
        if (_string($_SERVER['HTTP_ACCEPT_LANGUAGE'])->startsWith('en') and
            !_string($_SERVER['HTTP_REFER'])->contains('swoole.co.uk')
        )
        {
            $this->http->redirect('https://www.swoole.co.uk/');
        }
        $this->display();
    }

	function cms_index()
	{
        if (empty($_GET['p']) or $_GET['p'] == 'index')
        {
			$_user = createModel('UserInfo');
			//微博客列表
			$this->getMblogs(14);

			$gets['select'] = 'id,title,cname,cid,addtime';
			$gets['limit'] = 10;
			$gets['fid'] = 9;
			$model = createModel('News');
			$list = $model->gets($gets);

			$userlist = $this->getActiveUsers(50);

			$this->swoole->tpl->assign('userlist', $userlist);
			$this->swoole->tpl->assign('list', $list);
			$this->swoole->tpl->display('index.html');
		}
		else
		{
			$page = $_GET['p'];
			$model = createModel('Cpage');

			$det = $model->get($page,'pagename');
			$this->swoole->tpl->assign('det',$det);
			$this->swoole->tpl->display('index_page.html');
		}
	}

	/**
	 * 个人用户登录
	 */
    function login()
    {
        session();
        if ($this->user->isLogin())
        {
            $this->loginSucess();
            return;
        }
	
        if (isset($_POST['username']) and $_POST['username'] != '')
		{
            //if (!isset($_POST['authcode']) or strtoupper($_POST['authcode']) !== $_SESSION['authcode'])
            //{
            //    return Swoole\JS::js_back('验证码错误！');
            //}

            $_POST['username'] = strtolower(trim($_POST['username']));
            $_POST['password'] = trim($_POST['password']);

            /**
             * 密码输入错误超过了6次
             */
            if ($this->limit->exceed('password_error:' . $_POST['username'], 6))
            {
                return $this->json('', 1, "密码输入错误超过频率限制，您的帐号已被冻结，请在24小时后重试");
            }

            if ($this->user->login($_POST['username'], $_POST['password'], isset($_POST['auto']) ? 1 : 0))
            {
                $userinfo = $this->swoole->model->UserInfo->get($_SESSION['user_id'])->get();
                $_SESSION['user'] = $userinfo;
                return $this->loginSucess(!empty($_GET['ret']) and $_GET['ret'] == 'json');
            }
            else
            {
                //统计一天内的密码错误次数
                $this->limit->addCount('password_error:' . $_POST['username'], 86400);
                //return Swoole\JS::js_goto('用户名或密码错误！', '/page/login/');
                return $this->json('', 1, "用户名或密码错误, 请重新输入");
            }
        }
        else
        {
            $refer = isset($_GET['refer']) ? $_GET['refer'] : WEBROOT . '/person/index/';
            $conf = $this->config['oauth']['weibo'];
            $weibo_oauth = new \SaeTOAuthV2($conf['appid'], $conf['skey']);

            $params = array(
                'refer' => $refer,
                'return_token' => !empty($_GET['return_token'])
            );

            $weibo_login_url = $weibo_oauth->getAuthorizeURL(Swoole\Tool::urlAppend($conf['callback'], $params));

            Swoole\Loader::addNameSpace('ZenAPI', APPSPATH . '/include/zenapi');
            $conf = $this->config['oauth']['qq'];
            $qq_oauth = new QqOAuth2($conf['appid'], $conf['skey']);
            $qq_login_url = $qq_oauth->getAuthorizeURL(array(
                'client_id' => $conf['appid'],
                'redirect_uri' => Swoole\Tool::urlAppend($conf['callback'], $params),
                'response_type' => 'code',
                'display' => null,
                'scope' => $conf['scope'],
            ));
            //$this->tpl->assign('weibo_login_url', $weibo_login_url);
            //$this->tpl->assign('qq_login_url', $qq_login_url);
            //$this->tpl->display();
            $this->assign('weibo_login_url', $weibo_login_url);
            $this->assign('qq_login_url', $qq_login_url);
            $this->display();
        }
    }

    /**
     * 个人用户登录
     */
    function sms_login()
    {
        session();

        if (!empty($_POST['mobile']))
        {
            if (!isset($_POST['mobile']))
            {
                return $this->json(null, __LINE__, '缺少手机号码！');
            }

            if (!isset($_POST['sms_code']))
            {
                return $this->json(null, __LINE__,'缺少短信验证码！');
            }

            $_POST['mobile'] = strtolower(trim($_POST['mobile']));
            $_POST['sms_code'] = intval(trim($_POST['sms_code']));

            if ($this->limit->exceed('sms_code_error:' . $_POST['mobile'], 6))
            {
                return $this->json(null, __LINE__, "重试次数超过频率限制，您的帐号已被冻结，请在24小时后重试");
            }

            if (!isset($_POST['sms_code']) or strlen($_POST['sms_code']) != 4)
            {
                return $this->json(null, __LINE__, '短信验证码格式错误，必须为4位数字！');
            }

            $table = table('user_smscode');
            $ret = $table->exists(['sms_code' => $_POST['sms_code'], '']);

            if (!$ret)
            {
                $this->limit->addCount('sms_code_error:' . $_POST['mobile'], 86400);

                return $this->json(null, __LINE__, '短信验证码错误！');
            }

            $_SESSION['isLogin'] = 1;

            $uTable =  $this->model->UserInfo;
            $userinfo = $uTable->get($_POST['mobile'], 'mobile')->get();
            if (!$userinfo)
            {
                $id = $uTable->put([
                    'username' =>  $_POST['mobile'],
                    'nickname' => '新用户(手机注册)',
                    'realname' => '新用户(手机注册)',
                    'mobile' => $_POST['mobile'],
                    'mobile_verification' => 1,
                    'reg_ip' => $this->request->getClientIP(),
                    'reg_time' => Swoole\Tool::now(),
                    'lastlogin' => time(),
                    'lastip' => $this->request->getClientIP()
                ]);
                if ($id === false)
                {
                    return $this->json(null, __LINE__, '注册新用户失败，错误码' . $this->db->errno());
                }
                $_SESSION['user_id'] = $id;
                $_SESSION['user'] = $uTable->get($id)->get();
            }
            else
            {
                $_SESSION['user_id'] = $userinfo['id'];
                $_SESSION['user'] = $userinfo;
            }

            return $this->loginSucess(true);
        }
        else
        {
            if ($this->user->isLogin())
            {
                $this->loginSucess();

                return;
            }
            $refer = isset($_GET['refer']) ? $_GET['refer'] : WEBROOT . '/person/index/';
            $this->assign('refer', $refer);
            $this->display();
        }
    }


    function logout()
    {
        $this->http->setcookie('uname', '');
        $this->user->logout();
        $this->swoole->http->redirect('/page/login/');
    }

	function register()
	{
		if ($_POST)
		{
			Swoole::$php->session->start();
            //if (!isset($_POST['authcode']) or strtoupper($_POST['authcode']) !== $_SESSION['authcode'])
            //{
            //    Swoole\JS::js_back('验证码错误！');
            //    exit;
            //}
			if ($_POST['password']!==$_POST['rpassword'])
			{
				//Swoole\JS::js_back('两次输入的密码不一致！');
				//exit;
                return $this->json('', 1, "两次输入的密码不一致");
			}
			if (empty($_POST['nickname']))
			{
				//Swoole\JS::js_back('昵称不能为空！');
				//exit;
                return $this->json('', 1, "昵称不能为空！");
			}
			if (empty($_POST['sex']))
			{
				//Swoole\JS::js_back('性别不能为空！');
				//exit;
                return $this->json('', 1, "性别不能为空！");
			}
			$userInfo = createModel('UserInfo');
			$login['email'] = trim($_POST['email']);
			if ($userInfo->exists($login['email']))
			{
				//Swoole\JS::js_back('已存在此用户，同一个Email不能注册2次！');
				//exit;
                return $this->json('', 1, "已存在此用户，同一个Email不能注册2次！");
			}

            $login['password'] = Swoole\Auth::makePasswordHash($login['email'], $_POST['password']);
            $login['username'] = $login['email'];
			$login['reg_ip'] = $this->request->getClientIP();
            $login['nickname'] = $_POST['nickname'];
            $login['sex'] = (int)$_POST['sex'];
            //$login['skill'] = implode(',',$_POST['skill']);
            // $login['php_level'] = (int)$_POST['php_level'];
            $login['lastlogin'] = date('Y-m-d h:i:s');
            $uid = $userInfo->put($login);
            $_SESSION['isLogin'] = true;
            $_SESSION['user_id'] = $uid;
            $login['id'] = $uid;
            $_SESSION['user'] = $login;
			//return Swoole\JS::js_goto('注册成功！','/person/index/');
            return $this->json(['url' => '/person/index/']);
		}
		else
		{
            $forms = require WEBPATH . '/dict/forms.php';
            $_forms['sex'] = Swoole\Form::radio('sex', $forms['sex']);
			//$_forms['level'] = Form::radio('php_level',$forms['level'],'');
			//$this->tpl->assign('forms',$_forms);
			//$this->tpl->display();
			$this->assign('forms',$_forms);
			$this->display();
		}
	}

	/**
	 * 忘记密码
	 */
	function forgot()
    {
        if ($_POST)
        {
            $gets['realname'] = $_POST['realname'];
            $gets['username'] = $_POST['email'];
            $gets['mobile'] = $_POST['mobile'];
            $gets['select'] = 'id';
            $ul = $this->model->UserInfo->gets($gets);
            if (count($ul) != 0)
            {
                $password = App\Func::randomkeys(6);
                $this->model->UserInfo->set($ul[0]['id'],
                    array('password' => Auth::mkpasswd($gets['username'], $password)));
                App\Func::success('找回成功！', '您的新密码是 <span style="color:#fe7e00;">' . $password . '</a>');
            }
		}
		else
		{
			$this->swoole->tpl->display();
		}
	}

	function test()
	{
		$me = createModel('Me');
		if($_POST)
		{
			if(!$me->checkForm($_POST,'add',$error))
			{
				Swoole\JS::js_back($error);
				return;
			}
			echo 'ok';
		}
		else
		{
			$form = $me->getForm();
			$this->swoole->tpl->assign('head', Swoole\Form::head('me_add','post','',true));
			$this->swoole->tpl->assign('js', Swoole\Form::js('me_add'));
			$this->swoole->tpl->assign('form',$form);
			$this->swoole->tpl->display('test.html');
		}
	}

	private function fulltext($q,$page)
	{
		$cl = new SphinxClient ();
		$cl->SetServer('localhost',9312);
		$cl->SetArrayResult(true);

		$cl->SetLimits(($page-1)*$this->pagesize,$this->pagesize);
		$res = $cl->Query($q,"news");
		$model = createModel('News');

		foreach($res['matches'] as $m) $ids[] = $m['id'];
		if(empty($ids)) $res['list'] = array();
		else
		{
			$gets['in'] = array('id',implode(',',$ids));
			$gets['limit'] = $this->pagesize;
			$gets['order'] = '';
			$gets['select'] = "id,title,addtime";
			$list = $model->gets($gets);
			$res['list'] = $list;
		}
		return $res;
	}

	function search()
	{
		$keyword = mb_substr(trim($_GET['k']),0,32);
		if(empty($keyword))
		{
			Swoole\JS::js_back('关键词不能为空！');
			exit;
		}
		$page = empty($_GET['page'])?1:(int)$_GET['page'];
		$res = $this->fulltext($keyword,$page);
		$pager = new Swoole\Pager(array('page'=>$page,'perpage'=>$this->pagesize,'total'=>$res['total']));
		$this->swoole->tpl->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
		$this->swoole->tpl->assign('forms', $_forms);
		$this->swoole->tpl->assign("list", $res['list']);
		$this->swoole->tpl->display();
	}

	function guestbook()
	{
        if ($_POST)
        {
			if(empty($_POST['realname']))
			{
				Swoole\JS::js_back('姓名不能为空！');
				exit;
			}
			if(empty($_POST['mobile']))
			{
				Swoole\JS::js_back('电话不能为空！');
				exit;
			}
			unset($_POST['x'],$_POST['y']);
			$_POST['product'] = implode(',',$_POST['product']);
			$_POST['source'] = implode(',',$_POST['source']);
			$php->model->Guestbook->put($_POST);
			Swoole\JS::js_goto('注册成功！','guestbook.php');
		}

		if (!empty($_GET['id']))
		{
			$gb = $php->model->Guestbook->get($_GET['id'])->get();
			$php->tpl->assign('gb',$gb);
			$php->tpl->display('guestbook_detail.html');
		}
		else
		{
			require 'dict/forms.php';
			$pager = null;
			$gets['page'] = empty($_GET['page'])?1:$_GET['page'];
			$gets['pagesize'] =  12;
			$gets['select'] = "id,username,title,addtime";
			$gets['where'][] = "reply!=''";
			$list = $php->model->Guestbook->gets($gets,$pager);

			$_forms['title'] = Swoole\Form::radio('title', $forms['title'],null,true,array('empty'=>'请选择称谓'));
			$_forms['age'] = Swoole\Form::select('age', $forms['age'],null,true,array('empty'=>'请选择年龄阶段'));
			$_forms['ctime'] = Swoole\Form::select('ctime',$forms['ctime'],null,true,array('empty'=>'请选择方便沟通的时间'));
			$_forms['product'] = Swoole\Form::checkbox('product',$forms['product'],null,true);
			$_forms['source'] = Swoole\Form::checkbox('source',$forms['source'],null,true);

			$pager = array('total'=>$pager->total,'render'=>$pager->render());
			$php->tpl->assign('pager',$pager);
			$php->tpl->assign('forms',$_forms);
			$php->tpl->assign("list",$list);
			$php->tpl->display('guestbook.html');
		}
	}

    function user()
    {
        if (empty($_GET['uid']))
        {
            exit('Uid is empty');
        }
        $uid = (int)$_GET['uid'];
        $this->userinfo($uid);
        $this->getMblogs(10, $uid);
        $this->swoole->tpl->display();
    }

	private function setLoginStat()
	{
		$tm = time();
        Swoole\Cookie::set('uname', $_SESSION['user']['nickname'], $tm + 86400 * 30, '/');
        Swoole\Cookie::set('uid', $_SESSION['user_id'], $tm + 86400 * 30, '/');
        $user = $this->model->UserInfo->get($_SESSION['user_id']);
        $user->lastlogin = Swoole\Tool::now();
        $user->lastip = $this->request->getClientIP();
        $user->save();
	}
}
