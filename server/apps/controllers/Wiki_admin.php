<?php
namespace App\Controller;
use App;
use Swoole;

require_once __DIR__ . '/../classes/php-markdown/Michelf/Markdown.php';
require_once __DIR__ . '/../classes/php-markdown/Michelf/MarkdownExtra.php';
require_once __DIR__ . '/../classes/Content.php';

use \Michelf;

class Wiki_admin extends Swoole\Controller
{
    public $if_filter = false;

    protected $project_id;
    protected $project;
    protected $uid;

    function __construct($swoole)
    {
        session();
        //$this->swoole->db->debug = true;

        App\Content::$php = $swoole;
        parent::__construct($swoole);

        if (isset($_GET['prid']))
        {
            $this->project_id = intval($_GET['prid']);
            if (!isset($_COOKIE['wiki_project_id']) or $_COOKIE['wiki_project_id'] != $this->project_id)
            {
                $this->http->setcookie('wiki_project_id', $this->project_id, time() + 86400 * 30, '/');
            }
        }
        elseif (!empty($_COOKIE['wiki_project_id']))
        {
            $this->project_id = intval($_COOKIE['wiki_project_id']);
        }
        else
        {
            $this->project_id = 1;
        }

        //未登陆用户
        if (!isset($_SESSION['user_id']))
        {
            Swoole::$php->http->redirect('/page/login/?refer='.urlencode('/wiki_admin/index/'));
            Swoole::$php->http->finish();
        }

        $this->uid = $_SESSION['user_id'];
        $this->project = createModel('WikiProject')->get($this->project_id);

        $this->assign("project_id", $this->project_id);
        $this->assign("project", $this->project);

        //非管理员不允许登陆
        if ($this->ifDeny())
        {
            Swoole::$php->http->finish(Swoole\JS::js_goto('您没有编辑权限', '/wiki/index/'));
        }
    }

    function index_frames()
    {
        if (empty($this->project))
        {
            return Swoole\JS::js_goto("您访问的项目不存在", "/");
        }
        if (!empty($_GET['p']))
        {
            $this->assign('p', trim($_GET['p']));
        }
        $this->display("wiki/index.html");
    }

    function index()
    {
        if (empty($this->project))
        {
            return Swoole\JS::js_goto("您访问的项目不存在", "/");
        }
        if (!empty($_GET['p']))
        {
            $this->assign('p', trim($_GET['p']));
        }
        $this->getTopData();
        $this->getTreeData();
        $this->getMainData();
        $this->display();
    }

    function main()
    {
        $this->getMainData();
        $this->display();
    }

    function history()
    {
        if (empty($_GET['id']))
        {
            throw new Swoole\Exception\InvalidParam("缺少ID");
        }
        $this->getMainData();
        $_table = table('wiki_history');
        $list = $_table->gets(array('select' => 'id, version, title, uid, addtime',
            'wiki_id' => intval($_GET['id'])));

        $uid_list = array();
        foreach($list as $li)
        {
            $uid_list[] = $li['uid'];
        }
        $uid_list = array_unique($uid_list);
        $users = Model('UserInfo')->getMap(array('in' => array('id', $uid_list)), 'nickname');
        $this->assign('users', $users);
        $this->assign('list', $list);
        $this->display();
    }

    function diff()
    {
        if (empty($_GET['id']))
        {
            throw new Swoole\Exception\InvalidParam("缺少ID");
        }
        if (!isset($_GET['version']))
        {
            throw new Swoole\Exception\InvalidParam("需要version参数");
        }
        $this->getMainData();
        $_table = table('wiki_history');
        list($res) = $_table->gets(array(
            'version' => intval($_GET['version']),
            'wiki_id' => $_GET['id']
        ));
        $this->assign('a', $res['content']);
        if (isset($_GET['compare']) and $_GET['compare'] == 'last')
        {
            $version_b = intval($_GET['version']) - 1;
            list($res) = $_table->gets(array(
                'version' => $version_b,
                'wiki_id' => $_GET['id']
            ));
            $this->assign('b', $res['content']);
            $this->assign('version_b', $version_b);
        }
        else
        {
            $this->assign('b', $this->tpl_var['wiki_page']['content']);
            $this->assign('version_b', $this->tpl_var['wiki_page']['version']);
        }
        $this->display();
    }

    function revert()
    {
        if (empty($_GET['id']))
        {
            throw new Swoole\Exception\InvalidParam("缺少ID");
        }
        if (!isset($_GET['version']))
        {
            throw new Swoole\Exception\InvalidParam("需要version参数");
        }
        $wiki_id = intval($_GET['id']);
        $_table = table('wiki_history');
        list($res) = $_table->gets(array(
            'version' => intval($_GET['version']),
            'wiki_id' => $wiki_id,
        ));

        $_cont = model('WikiContent');
        $_tree = model('WikiTree');
        $cont = $_cont->get($wiki_id);
        $node = $_tree->get($wiki_id);
        if (!$cont->exist())
        {
            throw new Swoole\Exception\NotFound("页面不存在");
        }

        $newVersion = intval($cont->version) + 1;
        //写入历史记录
        $_historyTable = table('wiki_history');
        $_historyTable->put(array(
            'wiki_id' => $wiki_id,
            'uid' => $this->uid,
            'content' => $cont->content,
            'title' => $cont->title,
            'version' => $cont->version,
        ));
        //增加版本号
        $cont->version = $newVersion;
        $cont->title = $res['title'];
        $cont->content = $res['content'];
        $cont->uptime = time();
        //更新节点
        $node->link = $res['title'];
        $node->update_uid = $this->uid;
        $node->save();
        $cont->save();
        $this->http->redirect('/wiki_admin/main/?id='.$wiki_id);
    }

    function order()
    {
        if (empty($_GET['id']))
        {
            return "错误：父页面id为空";
        }

        $parent_id = intval($_GET['id']);
        $model = createModel('WikiTree');
        if(!empty($_POST['order']))
        {
            $order = explode(',', $_POST['order']);
            $n = count($order);
            if($n < 2)
            {
                return $this->message(501, '错误的请求');
            }
            foreach($order as $k=>$id)
            {
                $model->set($id, array('orderid' => $n - $k));
            }
            return $this->message(0, '排序操作成功');
        }
        else
        {
            $node = $model->get($parent_id);
            if ($node['order_by_time'])
            {
                return Swoole\JS::js_back("已启用自动时间排序，无法使用手工排序功能。");
            }
            $gets['pid'] = $parent_id;
            $gets['order'] = App\Content::$order;
            $childs = $model->gets($gets);
            $this->tpl->assign('childs', $childs);
            $this->tpl->display("wiki/order.html");
        }
    }

    private function getTopData()
    {
        $projects_link[] = $this->project;
        if (!empty($this->project['links']))
        {
            $projects = model('WikiProject')->all();
            $projects->order('id asc');
            $projects->in('id', $this->project['links']);
            $_projects_link = $projects->fetchall();
            if (count($_projects_link) > 0)
            {
                $projects_link = array_merge($projects_link, $_projects_link);
            }
        }
        $this->assign("projects", $projects_link);
    }

    private function getMainData()
    {
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');

        $page_id = basename($_SERVER['REQUEST_URI'], '.html');
        if (is_numeric($page_id))
        {
            $_GET['id'] = $page_id;
        }
        if (!empty($_GET['p']))
        {
            $node = $_tree->get($_GET['p'], 'link')->get();
            if (empty($node))
            {
                $file = WEBPATH . "/wiki/" . $_GET['p'] . '.md';
                if (!is_file($file))
                {
                    $text = "您访问的页面不存在！[点击跳转到首页](http://www.swoole.com/wiki/index/)";
                }
                else
                {
                    $text = file_get_contents($file);
                }
                goto markdown;
            }
            $wiki_id = $node['id'];
        }
        elseif (!empty($_GET['id']))
        {
            $wiki_id = intval($_GET['id']);
        }
        else
        {
            $wiki_id = $this->project['home_id'];
        }
        if (!$node)
        {
            $node = $_tree->get($wiki_id)->get();
        }
        $wiki_page =  $_cont->get($wiki_id)->get();
        if (!empty($node['markdown_file']))
        {
            $text = file_get_contents($this->config['site']['git_path'].'/'.$node['markdown_file']);
        }
        else
        {
            $text = $wiki_page['content'];
        }
        //历史版本
        if (isset($_GET['version']) and $_GET['version'] != $wiki_page['version'])
        {
            $version = intval($_GET['version']);
            $historyVersion = table('wiki_history')->gets(array(
                'wiki_id' => $wiki_id,
                'version' => $version,
                'limit' => 1,
                'select' => 'content',
            ));
            if (empty($historyVersion))
            {
                throw new \Exception("页面不存在");
            }
            $text = $historyVersion[0]['content'];
            $wiki_page['version'] = $version;
            $this->assign("history", true);
        }
        $this->assign("id", $wiki_id);
        $this->assign("wiki_page", $wiki_page);

        markdown:
        //GitHub Code Parse
        $text = str_replace('```', '~~~', $text);
        $parser = new Michelf\MarkdownExtra;
        $parser->fn_id_prefix = "post22-";
        $parser->code_attr_on_pre = false;
        $parser->tab_width = 4;
        $html = $parser->transform($text);

        $this->assign("content", $html);
    }

    private function getTreeData()
    {
        $data = App\Content::getTree($this->project_id);
        $tree =  App\Content::parseTreeArray($this->project['home_id'], $data);
//        echo json_encode($tree);exit;
        $this->assign("tree", $tree);
    }

    function page()
    {
        $this->index();
    }

    function top()
    {
        $this->getTopData();
        $this->display();
    }

    function tree()
    {
        $this->assign("tree", json_encode(App\Content::getTree($this->project_id)));
        $this->display();
    }

    function create_project()
    {
        if (!empty($_POST['name']))
        {
            $project = model('WikiProject')->get();
            $project->name = trim($_POST['name']);
            $project->links = trim($_POST['links']);
            $project->owner = trim($_POST['owner']);
            $project->close_comment = intval($_POST['close_comment']);
            if (false === $project->save())
            {
                return Swoole\JS::js_back("创建失败，请稍后重试");
            }

            //保存node
            $node = model('WikiTree')->get();
            $node->text = $project->name;
            $node->pid = -1;
            $node->project_id = $project->_current_id;
            $node->save();

            //project的首页修改当前页
            $project->home_id = $node->_current_id;
            $project->save();

            //创建内容页
            $cont = model('WikiContent')->get();
            $cont->id = $node->_current_id;
            $cont->title = $project->name;
            $cont->content = 'nothing';
            $cont->save();

            return Swoole\JS::echojs("alert(\"创建成功\");parent.window.location.href = \"/wiki/index/prid-".$project->_current_id."\"");
        }
        $form['comment'] = Swoole\Form::radio('close_comment',
            array('0'=>'开启', '1'=>'关闭'), 0, false, null, 'radio-inline');
        $this->assign("form", $form);
        $this->display();
    }

    function setting()
    {
        if (!empty($_POST['name']))
        {
            $this->project->name = trim($_POST['name']);
            $this->project->home_id = intval($_POST['home_id']);
            $this->project->links = trim($_POST['links']);
            $this->project->owner = trim($_POST['owner']);
            $this->project->git_repo = trim($_POST['git_repo']);
            $this->project->close_comment = intval($_POST['close_comment']);
            $this->project->save();
            $this->reflushPage("修改成功");
            return;
        }
        $form['comment'] = Swoole\Form::radio('close_comment',
            array('0'=>'开启', '1'=>'关闭'), $this->project['close_comment'], false, null, 'radio-inline');
        $this->assign("form", $form);
        $this->display();
    }

    private function reflushPage($info)
    {
        echo Swoole\JS::js_alert($info);
        $js = "parent.window.frames['tree'].location.reload();";
        $js .= "history.back();";
        echo Swoole\JS::echojs($js);
    }

    function create()
    {
        if (!empty($_POST))
        {
            $_tree = model('WikiTree');
            $in['text'] = $_POST['title'];
            $id =  intval($_GET['id']);
            if($id == 0)
            {
                $in['pid'] = 0;
                $in['project_id'] = $_COOKIE['wiki_project_id'];
            }
            else
            {
                $cnode = $_tree->get($id)->get();
                //作为父页面
                if(isset($_GET['parent']))
                {
                    $in['pid'] = $id;
                }
                //同级页面
                else
                {
                    $in['pid'] = empty($cnode)?0:$cnode['pid'];
                }
                if(!empty($_POST['link']))
                {
                    $in['link'] = trim($_POST['link']);
                }
                $in['markdown_file'] = trim($_POST['markdown_file']);
                $in['project_id'] = $cnode['project_id'];
            }

            $in['publish'] = intval($_POST['publish']);
            $in['order_by_time'] = intval($_POST['order_by_time']);

            $in['create_uid'] = $this->uid;
            $node_id = $_tree->put($in);
            $_cont = createModel('WikiContent');

            $in2['title'] = $in['text'];
            if (strlen($_POST['content']) > 0 and $_POST['content']{0} == '`')
            {
                $_POST['content'] = ' ' . $_POST['content'];
            }
            $in2['content'] = $_POST['content'];
            $in2['id'] = $node_id;
            $in2['close_comment'] = intval($_POST['close_comment']);
            $in2['close_edit'] = intval($_POST['close_edit']);
            $_cont->put($in2);

            //写入历史记录
            $_historyTable = table('wiki_history');
            $_historyTable->put(array(
                'wiki_id' => $node_id,
                'uid' => $this->uid,
                'content' => $_POST['content'],
                'title' => $in['text'],
                'version' => 0,
            ));
            $this->reflushPage('增加成功');
        }
        else
        {
            $this->isUseEditor();
            $form['comment'] = Swoole\Form::radio(
                'close_comment',
                array('0' => '开启', '1' => '关闭'), 0, false, null, 'radio-inline'
            );
            //关闭编辑
            $form['close_edit'] = Swoole\Form::radio('close_edit',
                array('0' => '允许', '1' => '禁止'), 0, false, null, 'radio-inline');
            $form['order_by_time'] = Swoole\Form::radio(
                'order_by_time',
                array('0'=>'手工排序', '1'=>'按添加时间自动排序'), 0, false, null, 'radio-inline'
            );
            $form['publish'] = Swoole\Form::radio('publish',
                array('0' => '关闭', '1' => '开启'), 1, false, null, 'radio-inline');
            $this->assign("page", array());
            $this->assign("form", $form);
            $this->display();
        }
    }

    function paste()
    {
        if(empty($_GET['id'])) return "error: requirer miki_page id";
        $id = (int)$_GET['id'];
        //作为子页面
        $cut_node = model('WikiTree')->get($_COOKIE['wiki_cut_id']);
        if(isset($_GET['child']))
        {
            if (count($cut_node->get()) < 1)
            {
                return Swoole\JS::js_back("页面不存在");
            }
            else
            {
                $cut_node->pid = $id;
            }
        }
        //同级页面
        else
        {
            $node = model('WikiTree')->get($id)->get();
            $cut_node->pid = $node['pid'];
        }
        $cut_node->save();
        Swoole\Cookie::delete('wiki_cut_id');
        $this->reflushPage('剪切成功');
    }

    function cut()
    {
        if (empty($_GET['id']))
        {
            return "error: requirer miki_page id";
        }
        Swoole\Cookie::set('wiki_cut_id', $_GET['id'], 86400);

        return Swoole\JS::js_back("剪切成功，请到目标页面粘贴");
    }

    private function ifDeny()
    {
        $owners = explode(',', $this->project['owner']);
        if (!in_array($_SESSION['user_id'], $owners))
        {
            return true;
        }
        else
        {
            if (!isset($_COOKIE['wiki_admin']))
            {
                Swoole\Cookie::set('wiki_admin', '1', 86400 * 30);
            }
            return false;
        }
    }

    function delete()
    {
        if(empty($_GET['id'])) return "error: requirer miki_page id";
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');
        $id = (int)$_GET['id'];
        $_cont->del($id);
        $_tree->del($id);
        $this->reflushPage('删除成功');
    }

    /**
     *
     */
    protected function isUseEditor()
    {
        if (!isset($_GET['editor']) and !empty($_COOKIE['wiki_use_editor']))
        {
            $use_editor = true;
        }
        else
        {
            if (!empty($_GET['editor']))
            {
                $this->http->setcookie('wiki_use_editor', '1', time() + 86400 * 30);
                $use_editor = $_GET['editor'];
            }
            else
            {
                $this->http->setcookie('wiki_use_editor', '');
                $use_editor = false;
            }
        }
        $this->assign('use_editor', $use_editor);
    }

    /**
     * @return string
     */
    function modify()
    {
        if (empty($_GET['id']))
        {
            return "error: requirer miki_page id";
        }
        $id = (int)$_GET['id'];
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');

        $cont = $_cont->get($id);
        $node = $_tree->get($id);
        //关闭评论
        $form['comment'] = Swoole\Form::radio('close_comment',
            array('0' => '开启', '1' => '关闭'), $cont['close_comment'], false, null, 'radio-inline');
        //关闭编辑
        $form['close_edit'] = Swoole\Form::radio('close_edit',
            array('0' => '允许', '1' => '禁止'), $cont['close_edit'], false, null, 'radio-inline');
        $form['order_by_time'] = Swoole\Form::radio('order_by_time',
            array('0' => '手工排序', '1' => '按添加时间自动排序'), $node['order_by_time'], false, null, 'radio-inline');
        $form['publish'] = Swoole\Form::radio('publish',
            array('0' => '关闭', '1' => '开启'), $node['publish'], false, null, 'radio-inline');

        $this->assign("form", $form);

        if (!empty($_POST))
        {
            if (!empty($_POST['content']) and $_POST['content'][0] == '`')
            {
                $_POST['content'] = ' '.$_POST['content'];
            }

            //更新内容和标题
            if (!($_POST['content'] === $cont->content and trim($_POST['title']) == $cont->title))
            {
                //写入历史记录
                $_historyTable = table('wiki_history');
                $_historyTable->put(array(
                    'wiki_id' => $node->id,
                    'uid' => $this->uid,
                    'content' => $cont->content,
                    'title' => $cont->title,
                    'version' => intval($cont->version),
                ));
                //增加版本号
                $cont->version = intval($cont->version) + 1;
            }

            $cont->title = trim($_POST['title']);
            $cont->content = $_POST['content'];
            $cont->close_comment = intval($_POST['close_comment']);
            $cont->close_edit = intval($_POST['close_edit']);
            $cont->uptime = time();

            //更新节点
            $node->update_uid = $this->uid;
            $node->text = $cont->title;
            $node->link = trim($_POST['link']);
            $node->order_by_time = $_POST['order_by_time'];
            $node->publish = intval($_POST['publish']);
            $node->markdown_file = trim($_POST['markdown_file']);

            if (!empty($node->markdown_file) and !is_file($this->config['site']['git_path'].'/'.$node->markdown_file))
            {
                $this->assign("info", "修改失败，文件不存在。");
            }
            else
            {
                $node->save();
                $cont->save();
                $this->assign("info", "修改成功");
            }
        }
        $this->isUseEditor();
        $this->assign("node", $node->get());
        $this->assign("page", $cont->get());
        $this->display("wiki_admin/create.php");
    }

    function upload()
    {
        if (empty($_FILES['editormd-image-file']))
        {
            return "error: requirer editormd-image-file";
        }

        $this->upload->sub_dir = 'wiki';
        $up_pic = Swoole::$php->upload->save('editormd-image-file');

        if (empty($up_pic))
        {
            $result = array('success' => 0,
                'message' => '上传失败，请重新上传！ Error:' . $this->upload->error_msg);
            goto return_json;
        }

        $data['url'] = $up_pic['url'];
        $data['page_id'] = (int)$_GET['id'];
        $data['user_id'] = $_SESSION['user_id'];

        $id = table('wiki_image')->put($data);
        if ($id)
        {
            $result['success'] = 1;
            $result['url'] = $data['url'];
        }
        else
        {
            $result['success'] = 0;
            $result['message'] = "插入数据库失败";
        }
        return_json:
        return json_encode($result);
    }
}

