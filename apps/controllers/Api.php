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

    function latest()
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
        $pager = null;
        $list = table('aws_question')->gets($gets, $pager);

        $_uids = array();
        foreach($list as $li)
        {
            $_uids[$li['published_uid']] = 1;
        }

        $_user = table('aws_users');
        $_user->primary = 'uid';
        $users = $_user->getMap(['in' => array('uid', implode(',', array_keys($_uids)))]);

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

    function topic()
    {
        echo file_get_contents(__DIR__.'/data.json');
    }

    function nodes()
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

        $list = table('aws_topic')->gets(array('limit' => 100, 'order' => 'topic_id asc'));
        $result = [];
        foreach($list as $li)
        {
            $tpl['id'] = $li['topic_id'];
            $tpl['title_alternative'] = $tpl['title'] = $tpl['name'] = $li['topic_title'];
            $tpl['created'] = $li['add_time'];
            $tpl['topics'] = $li['discuss_count'];
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
}
