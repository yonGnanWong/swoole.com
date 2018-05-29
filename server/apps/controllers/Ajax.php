<?php
namespace App\Controller;
use App;
use Swoole;

class Ajax extends Swoole\Controller
{
    public $is_ajax = true;

    function check_email()
    {
        if (!empty($_GET['email']))
        {
            return $this->model->UserInfo->exists($_GET['email']);
        }
    }

    function check_vcode()
    {
        if (!empty($_GET['vcode']))
        {
            \Swoole::$php->session->start();
            return array('data' => strtoupper($_GET['vcode']) == $_SESSION['authcode']);
        }
        return "bad request";
    }

    function send_smscode_2()
    {
        require_once WEBPATH.'/vendor/autoload.php';
        $code = rand(1000, 9999);
        if (empty($_GET['mobile']))
        {
            return ['code' => 1002, 'message' => '缺少参数'];
        }
        if (strlen($_GET['mobile']) != 11)
        {
            return ['code' => 1004, 'message' => '错误的手机号码，必须为11位有效号码'];
        }
        $table = table('user_smscode');
        if ($table->count([
                'sms_code' => $_POST['smscode'],
                'mobile' => $_POST['mobile'],
                'where' => [['unix_timestamp(created_time) > ' . strtotime(date('Y-m-d'))]],
            ]) > 5)
        {
            return ['code' => 1005, 'message' => '发生次数超过限制，同一个手机号每天只允许发送5条短信。'];
        }
        $user = Model('UserInfo')->get($this->user->getUid())->get();
        if ($user['mobile_verification'])
        {
            return ['code' => 1006, 'message' => '您的手机号码已通过验证，无需再次验证。'];
        }
        try
        {
            if (!table('user_smscode')->put(['sms_code' => $code, 'uid' => $this->user->getUid(), 'mobile' => $_GET['mobile']]))
            {
                return ['code' => 6001, 'message' => '写入数据库失败'];
            }
            $sender = new \Qcloud\Sms\SmsSingleSender($this->config['sms']['appid'], $this->config['sms']['appkey']);
            $ret = $sender->send(0, "86", $_GET['mobile'], "您的验证码为{$code}，请于15分钟内填写", "", "");
            $result = json_decode($ret, true);
            return ['code' => $result['result'], 'message' => $result['errmsg']];
        }
        catch (\Exception $e)
        {
            return array('code' => $e->getCode(), 'message' => $e->getMessage());
        }
    }

    function send_smscode()
    {
        require_once WEBPATH.'/vendor/autoload.php';
        $code = rand(1000, 9999);
        Swoole::$php->session->start();
        if (!$this->user->isLogin())
        {
            return ['code' => 1001, 'message' => '未登录'];
        }
        if (empty($_GET['mobile']) or empty($_GET['vcode']))
        {
            return ['code' => 1002, 'message' => '缺少参数'];
        }
        if (strtoupper($_GET['vcode']) != $_SESSION['authcode'])
        {
            return ['code' => 1003, 'message' => '错误的图形验证码'];
        }
        if (strlen($_GET['mobile']) != 11)
        {
            return ['code' => 1004, 'message' => '错误的手机号码，必须为11位有效号码'];
        }
        $table = table('user_smscode');
        if ($table->count([
            'sms_code' => $_POST['smscode'],
            'mobile' => $_POST['mobile'],
            'where' => [['unix_timestamp(created_time) > ' . strtotime(date('Y-m-d'))]],
        ]) > 5)
        {
            return ['code' => 1005, 'message' => '发生次数超过限制，同一个手机号每天只允许发送5条短信。'];
        }
        $user = Model('UserInfo')->get($this->user->getUid())->get();
        if ($user['mobile_verification'])
        {
            return ['code' => 1006, 'message' => '您的手机号码已通过验证，无需再次验证。'];
        }
        try
        {
            if (!table('user_smscode')->put(['sms_code' => $code, 'uid' => $this->user->getUid(), 'mobile' => $_GET['mobile']]))
            {
                return ['code' => 6001, 'message' => '写入数据库失败'];
            }
            $sender = new \Qcloud\Sms\SmsSingleSender($this->config['sms']['appid'], $this->config['sms']['appkey']);
            $ret = $sender->send(0, "86", $_GET['mobile'], "您的验证码为{$code}，请于15分钟内填写", "", "");
            $result = json_decode($ret, true);
            return ['code' => $result['result'], 'message' => $result['errmsg']];
        }
        catch (\Exception $e)
        {
            return array('code' => $e->getCode(), 'message' => $e->getMessage());
        }
    }
    
    function comment()
    {
    	\Swoole::$php->session->start();
    	if(!$_SESSION['isLogin']) return 'nologin';
    	$uid = $_SESSION['user_id'];
    	$post['aid'] = (int)$_POST['aid'];
    	$post['app'] = $_POST['app'];
    	$post['content'] = $_POST['content'];
    	$post['uid'] = $uid;
        $post['uname'] = $_SESSION['user']['nickname'];
        if ($post['app'] === 'mblog')
        {
    		$m = createModel('MicroBlog');
    		$entity = $m->get($post['aid']);
    		$entity->reply_count ++;
    		$entity->save();
    		if($entity->uid!=$uid)
    		{
    			App\Api::sendmail($entity->uid, $uid, "【系统】{$post['uname']}评论了你的微博", $post['content']);
    		}
    	}
    	elseif($post['app']==='blog')
    	{
    		$m = createModel('UserLogs');
    		$entity = $m->get($post['aid']);
    		$entity->reply_count ++;
    		$entity->save();
    		if($entity->uid!=$uid)
    		{
                App\Api::sendmail($entity->uid, $uid, "【系统】{$post['uname']}评论了你的日志.({$entity['title']})", $post['content']);
    		}
    	}
    	createModel('UserComment')->put($post);
    	$return = array('id'=>$_SESSION['user']['id'],
    			'addtime'=>Swoole\Tool::howLongAgo(date('Y-m-d H:i:s')),
    			'nickname'=>$_SESSION['user']['nickname']);
    	if(empty($_SESSION['user']['avatar'])) $return['avatar'] = Swoole::$php->config['user']['default_avatar'];
    	else $return['avatar'] = $_SESSION['user']['avatar'];
    	return $return;
    }
    
    function ask_best()
    {
        Swoole::$php->session->start();
    	if(!$_SESSION['isLogin']) return 'nologin';
    	$reid = (int)$_POST['reid'];
    	$reply = createModel('AskReply')->get($reid);
    	$ask = createModel('AskSubject')->get($reply['aid']);
    
    	if($ask->uid!=$_SESSION['user_id']) return 'notowner';
    	//已有最佳答案
    	if($ask->mstatus == 2) return 'nobest';
    
    	//设置为最佳答案
    	$reply->best = 1;
    	$reply->save();
    
    	$user = createModel('UserInfo')->get($reply['uid']);
    	$user->gold += 20; //采纳为最佳答案+20分
    	$user->gold += $ask->gold; //另外加悬赏分数
    	$user->save();
    
    	//设置为已有最佳答案
    	$ask->mstatus = 2;
    	$ask->save();
    	return 'ok';
    }
    
    function ask_vote()
    {
    	global $php;
        Swoole::$php->session->start();
    	if(!$_SESSION['isLogin']) return 'nologin';
    	$reid = (int)$_POST['reid'];
    	$reply = createModel('AskReply')->get($reid);
    	$reply->vote+=1;
    	$reply->save();
    	$put['uid'] = $_SESSION['user_id'];
    	$put['aid'] = $reply['aid'];
    	$put['reply_id'] = $reid;
    	$php->db->insert($put,'ask_vote');
    	return 'ok';
    }
    
    function checklogin()
    {
        Swoole::$php->session->start();
    	if(!empty($_SESSION['isLogin']))
        {
            return $_SESSION['user']['nickname'];
        }
    	else return false;
    }
}
