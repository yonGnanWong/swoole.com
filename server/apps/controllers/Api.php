<?php
namespace App\Controller;
use App;
use Swoole;
use \Michelf;

require_once __DIR__ . '/../classes/php-markdown/Michelf/Markdown.php';
require_once __DIR__ . '/../classes/php-markdown/Michelf/MarkdownExtra.php';
require_once __DIR__ . '/../classes/Content.php';

class Api extends Swoole\Controller
{
    const AVATAR_URL = 'http://182.254.148.72:9502/uploads/avatar/';
    const NO_AVATAR = 'http://182.254.148.72:9502/static/common/';

    function getLoginInfo()
    {
        if (empty($_COOKIE['PHPSESSID']))
        {
            not_found:
            return $this->json([], 404);
        }
        $this->session->start();
        if (!empty($_SESSION['user']))
        {
            $user = $_SESSION['user'];
            $user['admin'] = false;
            App\Api::userInfoSafe($user);

            if (!empty($_GET['prid']))
            {
                $proj = table('wiki_project')->get(intval($_GET['prid']))->get();
                if (!empty($proj['owner']))
                {
                    $user['admin'] = (new Swoole\StringObject($proj['owner']))->split(',')->contains($user['id']);
                }
            }

            return $this->json($user);
        }
        goto not_found;
    }

    function delComment()
    {
        if (empty($_COOKIE['PHPSESSID']))
        {
            return $this->json([], 403);
        }
        $this->session->start();
        if (empty($_SESSION['user']))
        {
            return $this->json(['login' => $this->config['user']['login_url']], 403);
        }
        if (empty($_POST['id']))
        {
            return $this->json(null, 1001);
        }
        $table = table('duoshuo_posts');
        if ($table->del($_POST['id']))
        {
            return $this->json([]);
        }
        else
        {
            return $this->json(null, 500);
        }
    }

    function postComment()
    {
        if (empty($_COOKIE['PHPSESSID']))
        {
            return $this->json([], 403);
        }
        $this->session->start();
        if (empty($_SESSION['user']))
        {
            return $this->json(['login' => $this->config['user']['login_url']], 403);
        }
        if (empty($_POST['content']) or empty($_POST['app']) or empty($_POST['id']))
        {
            return $this->json(null, 1001);
        }

        if (App\Api::badUser($_SESSION['user']['id']))
        {
            return $this->json(null, 1006, "您的账户已被列入黑名单，请联系网站管理员。");
        }

        $table = table('duoshuo_posts');
        Swoole\Filter::safe($_POST['content']);
        Swoole\Loader::addNameSpace('Stauros', Swoole::$app_path.'/include/Stauros/lib/Stauros');
        $clean = strip_tags($_POST['content']);
        $ret = $table->put(array(
            'uid' => $_SESSION['user']['id'],
            'created_at' => Swoole\Tool::now(),
            'thread_id' => intval($_POST['id']),
            'thread_key' => $_POST['app'] . '-' . intval($_POST['id']),
            'message' => $clean,
        ));
        if ($ret)
        {
            return $this->json(['id' => $ret]);
        }
        else
        {
            return $this->json(null, 500);
        }
    }

    static function parseMarkdown($text)
    {
        //GitHub Code Parse
        $text = str_replace('```', '~~~', $text);
        $parser = new Michelf\MarkdownExtra;
        $parser->fn_id_prefix = "post22-";
        $parser->code_attr_on_pre = false;
        $parser->tab_width = 4;
        return $parser->transform($text);
    }

    function topic()
    {
        $tpl = array(
            'id' => 0,
            'title' => '',
            'url' => 'http://www.v2ex.com/t/188087',
            'content' => '',
            'content_rendered' => '',
            'replies' => 0,
            'created' => 0,
            'last_modified' => 0,
            'last_touched' => 1430627831,
            'member' =>
                array(
                    'id' => 0,
                    'username' => '',
                    'tagline' => '',
                    'avatar_mini' => '',
                    'avatar_normal' => '',
                    'avatar_large' => '',
                ),
            'node' =>
                array(
                    'id' => 1,
                    'name' => 'create',
                    'title' => '分享创造',
                    'title_alternative' => 'Create',
                    'url' => 'http://www.v2ex.com/go/create',
                    'topics' => 3800,
                    'avatar_mini' => '//cdn.v2ex.co/navatar/70ef/df2e/17_mini.png?m=1430065455',
                    'avatar_normal' => '//cdn.v2ex.co/navatar/70ef/df2e/17_normal.png?m=1430065455',
                    'avatar_large' => '//cdn.v2ex.co/navatar/70ef/df2e/17_large.png?m=1430065455',
                ),
        );

        $gets = array('order' => 'question_id desc',
            'pagesize' => 20);
        $gets['page'] = empty($_GET['page']) ? 1 : intval($_GET['page']);

        $_user = table('aws_users');
        $_user->primary = 'uid';

        if (!empty($_GET['category']))
        {
            $gets['category_id'] = intval($_GET['category']);
        }
        if (!empty($_GET['username']))
        {
            $user = $_user->get($_GET['username'], 'user_name');
            $gets['published_uid'] = $user['uid'];
        }

        $pager = null;
        $list = table('aws_question')->gets($gets, $pager);

        $_uids = array();
        $_categorys = array();
        foreach($list as $li)
        {
            $_uids[$li['published_uid']] = true;
            $_categorys[$li['category_id']] = true;
        }

        if (!empty($_uids))
        {
            $users = $_user->getMap(['in' => array('uid', implode(',', array_keys($_uids)))]);
        }
        else
        {
            $users = array();
        }

        if (!empty($_uids))
        {
            $categorys = table('aws_category')->getMap(['in' => array('id', implode(',', array_keys($_categorys)))]);
        }
        else
        {
            $categorys = array();
        }

        $result = array();
        foreach($list as $li)
        {
            $tpl['id'] = $li['question_id'];
            $tpl['title'] = $li['question_content'];
            $tpl['content'] = $li['question_detail'];
            $tpl['created'] = $li['add_time'];
            $tpl['last_modified'] = $li['update_time'];
            $tpl['replies'] = $li['answer_count'];

            //用户信息
            $uid = $li['published_uid'];
            $tpl['member']['id'] = $uid;
            $tpl['member']['username'] = $users[$uid]['user_name'];

            $_category_id = $li['category_id'];
            $tpl['node']['id'] = $_category_id;
            $tpl['node']['title_alternative'] = $tpl['node']['title'] = $tpl['node']['name'] = $categorys[$_category_id]['title'];
            $tpl['node']['name'] = $categorys[$_category_id]['title'];

            if (empty($users[$uid]['avatar_file']))
            {
                $tpl['member']['avatar_mini'] = self::NO_AVATAR.'avatar-min-img.jpg';
                $tpl['member']['avatar_normal'] = self::NO_AVATAR.'avatar-mid-img.jpg';
                $tpl['member']['avatar_large'] = self::NO_AVATAR.'avatar-max-img.jpg';
            }
            else
            {
                $tpl['member']['avatar_mini'] = self::AVATAR_URL.$users[$uid]['avatar_file'];
                $tpl['member']['avatar_normal'] = self::AVATAR_URL . str_replace('_min.', '_mid.', $users[$uid]['avatar_file']);
                $tpl['member']['avatar_large'] = self::AVATAR_URL . str_replace('_min.', '_max.', $users[$uid]['avatar_file']);
            }

            $tpl['content_rendered'] = self::parseMarkdown($li['question_detail']);
            $result[] = $tpl;
        }
        echo json_encode($result);
    }

    function category()
    {
        $tpl = array (
            'id' => 0,
            'name' => '',
            'url' => 'http://www.v2ex.com/go/babel',
            'title' => '',
            'title_alternative' => '',
            'topics' => 0,
            'header' => '',
            'footer' => '',
            'created' => 0,
        );

        $counts = $this->db->query("SELECT count(*) as c, category_id FROM `aws_question` WHERE 1 group by category_id")
            ->fetchall();
        $topic_num = [];
        foreach($counts as $c)
        {
            $topic_num[$c['category_id']] = $c['c'];
        }

        $list = table('aws_category')->gets(array('limit' => 100, 'order' => 'id asc'));
        $result = [];
        foreach ($list as $li)
        {
            $tpl['id'] = $li['id'];
            $tpl['title_alternative'] = $tpl['title'] = $tpl['name'] = $li['title'];
            $tpl['created'] = 0;
            if (isset($topic_num[$li['id']]))
            {
                $tpl['topics'] = $topic_num[$li['id']];
            }
            else
            {
                $tpl['topics'] = 0;
            }
            $result[] = $tpl;
        }
        echo json_encode($result);
    }

    function reply()
    {
        if (empty($_GET['topic_id']))
        {
            no_reply:
            return json_encode([]);
        }

        $_reply = table('aws_answer');
        $list = $_reply->gets(['question_id' => intval($_GET['topic_id']), 'order' => 'answer_id asc']);

        if (empty($list))
        {
            goto no_reply;
        }
        $_uids = array();
        foreach($list as $li)
        {
            $_uids[$li['uid']] = 1;
        }

        $_user = table('aws_users');
        $_user->primary = 'uid';
        $users = $_user->getMap(['in' => array('uid', implode(',', array_keys($_uids)))]);

        $result = array();
        foreach($list as $li)
        {
            $tpl['id'] = $li['answer_id'];
            $tpl['content'] = $li['answer_content'];
            $tpl['content_rendered'] = self::parseMarkdown($li['answer_content']);

            $tpl['created'] = $li['add_time'];
            $tpl['last_modified'] = $li['add_time'];
            $tpl['thanks'] = $li['thanks_count'];

            //用户信息
            $uid = $li['uid'];
            $tpl['member']['id'] = $uid;
            $tpl['member']['username'] = $users[$uid]['user_name'];
            $tpl['member']['tagline'] = '';
            if (empty($users[$uid]['avatar_file']))
            {
                $tpl['member']['avatar_mini'] = self::NO_AVATAR.'avatar-min-img.jpg';
                $tpl['member']['avatar_normal'] = self::NO_AVATAR.'avatar-mid-img.jpg';
                $tpl['member']['avatar_large'] = self::NO_AVATAR.'avatar-max-img.jpg';
            }
            else
            {
                $tpl['member']['avatar_mini'] = self::AVATAR_URL.$users[$uid]['avatar_file'];
                $tpl['member']['avatar_normal'] = self::AVATAR_URL . str_replace('_min.', '_mid.', $users[$uid]['avatar_file']);
                $tpl['member']['avatar_large'] = self::AVATAR_URL . str_replace('_min.', '_max.', $users[$uid]['avatar_file']);
            }
            $result[] = $tpl;
        }
        echo json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    function get_user_info()
    {
        if (empty($_GET['token']))
        {
            $this->http->status(403);
            return "access deny\n";
        }

        $token = trim($_GET['token']);
        $user = $this->cache->get('login_token_'.$token);
        if ($user)
        {
            return json_encode($user);
        }
        else
        {
            return json_encode(false);
        }
    }

    function login()
    {
        if (empty($_POST['password']) or empty($_POST['username']))
        {
            $this->http->status(403);
            return "access deny\n";
        }

        $this->session->start();

        $_user = table('aws_users');
        $_user->primary = 'uid';

        $userinfo = $_user->get(trim($_POST['username']), 'user_name');
        if ($userinfo->exist())
        {
            if (self::check_password($userinfo, $_POST['password']) === false)
            {
                goto error_user;
            }
            else
            {
                $_SESSION['login'] = true;
                $_SESSION['user'] = $userinfo->get();
                return $this->json();
            }
        }
        else
        {
            error_user:
            return $this->json('', 403, "错误的用户名或密码");
        }
    }

    function profile()
    {
        $this->session->start();
        if (empty($_SESSION['user']))
        {
            return $this->json('', 403, "需要登录");
        }
        $user = $_SESSION['user'];
        $categorys = table('aws_category')->gets(array('limit' => 100, 'order' => 'id asc'));
        $collections = [];
        foreach($categorys as $c)
        {
            $collections[] = $c['title'];
        }
        return $this->json(['username' => $user['user_name'], 'collections' => $collections]);
    }

    static function check_password($userinfo, $post_password)
    {
        $salt = $userinfo['salt'];
        if (strlen($post_password) == 32)
        {
            $md5 = md5($post_password . $salt);
        }
        else
        {
            $md5 = md5(md5($post_password) . $salt);
        }
        return $md5 == $userinfo['password'];
    }

    function post_comment()
    {
        if (empty($_POST['content']) or empty($_POST['topic_id']))
        {
            $this->http->status(403);
            return "access deny\n";
        }
        $this->session->start();
        if (empty($_SESSION['user']))
        {
            return $this->json('', 403, "需要登录");
        }

        $topic_id = intval($_POST['topic_id']);
        $_table = table('aws_question');
        $_table->primary = 'question_id';
        $topic = $_table->get($topic_id);

        if ($topic->exist() === false)
        {
            return $this->json('', 404, "主题不存在");
        }

        $user = $_SESSION['user'];
        $put['question_id'] = $topic_id;
        $put['uid'] = $user['uid'];
        $put['add_time'] = time();
        $put['answer_content'] = trim($_POST['content']);
        $put['ip'] = ip2long(Swoole\Client::getIP());
        $put['category_id'] = $topic['category_id'];
        $id = table('aws_answer')->put($put);

        if ($id)
        {
            return $this->json(['commit_id' => $id]);
        }
        else
        {
            return $this->json('', 500, "操作失败，请稍后重试");
        }
    }

    function post_topic()
    {
        if (empty($_POST['content']) or empty($_POST['title']) or empty($_POST['category_id']))
        {
            $this->http->status(403);
            return "access deny\n";
        }
        $this->session->start();
        if (empty($_SESSION['user']))
        {
            return $this->json('', 403, "需要登录");
        }

        $user = $_SESSION['user'];
        $put['question_content'] = trim($_POST['title']);
        $put['question_detail'] = trim($_POST['content']);
        $put['published_uid'] = $user['uid'];
        $put['update_time'] = $put['add_time'] = time();
        $put['ip'] = ip2long(Swoole\Client::getIP());
        $put['category_id'] = intval($_POST['category_id']);
        $id = table('aws_question')->put($put);

        if ($id)
        {
            return $this->json(['topic_id' => $id]);
        }
        else
        {
            return $this->json('', 500, "操作失败，请稍后重试");
        }
    }

    function new_message()
    {
        $this->session->start();
        if (empty($_SESSION['user']))
        {
            return $this->json('', 403, "需要登录");
        }
        $user = $_SESSION['user'];
        $gets['to_uid'] = $user['uid'];
        $gets['isread'] = 0;
        $gets['select'] = "question_id as topic_id, title, message as content, time, uid";
        $message = table('aws_question_comments')->gets($gets);
        return $this->json($message);
    }
}
