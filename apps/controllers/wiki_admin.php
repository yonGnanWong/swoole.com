<?php
require_once __DIR__ . '/../classes/php-markdown/Michelf/Markdown.php';
require_once __DIR__ . '/../classes/php-markdown/Michelf/MarkdownExtra.php';
require_once __DIR__ . '/../classes/Content.php';

use \Michelf;

class wiki_admin extends Swoole\Controller
{
    public $if_filter = false;

    protected $project_id;
    protected $project;

    function __construct($swoole)
    {
        session();
        //$this->swoole->db->debug = true;

        App\Content::$php = $swoole;
        parent::__construct($swoole);
        if(isset($_GET['prid']))
        {
            $this->project_id = intval($_GET['prid']);
            if(!isset($_COOKIE['wiki_project_id']) or $_COOKIE['wiki_project_id']!=$this->project_id)
            {
                Swoole\Cookie::set('wiki_project_id', $this->project_id, 86400*30);
            }
        }
        elseif (isset($_COOKIE['wiki_project_id']))
        {
            $this->project_id = intval($_COOKIE['wiki_project_id']);
        }
        else
        {
            $this->project_id = 1;
        }

        //未登陆用户
        if(!isset($_SESSION['user_id']))
        {
            Swoole::$php->http->redirect('/page/login/');
            Swoole::$php->http->finish();
        }

        $this->project = createModel('WikiProject')->get($this->project_id);
        $this->swoole->tpl->assign("project_id", $this->project_id);
        $this->swoole->tpl->assign("project", $this->project);

        //非管理员不允许登陆
        if($this->ifDeny())
        {
            Swoole\JS::js_goto('您没有编辑权限', '/wiki/index/');
            Swoole::$php->http->finish();
        }
    }

    function index_frames()
    {
        if(empty($this->project))
        {
            return Swoole\JS::js_goto("您访问的项目不存在", "/");
        }
        if(!empty($_GET['p']))
        {
            $this->swoole->tpl->assign('p', trim($_GET['p']));
        }
        $this->swoole->tpl->display("wiki/index.html");
    }

    function index()
    {
        if(empty($this->project))
        {
            return Swoole\JS::js_goto("您访问的项目不存在", "/");
        }
        if(!empty($_GET['p']))
        {
            $this->swoole->tpl->assign('p', trim($_GET['p']));
        }
        $this->getTopData();
        $this->getTreeData();
        $this->getMainData();
        $this->swoole->tpl->display("wiki/index.html");
    }

    function main()
    {
        $this->getMainData();
        $this->swoole->tpl->display("wiki/main.html");
    }

    function order()
    {
        if(empty($_GET['id']))
        {
            return "错误：父页面id为空";
        };

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
            $pid = $_GET['id'];
            $gets['pid'] = $pid;
            $gets['order'] = App\Content::$order;
            $childs = $model->gets($gets);
            $this->tpl->assign('childs', $childs);
            $this->tpl->display("wiki/order.html");
        }
    }

    private function getTopData()
    {
        $projects_link[] = $this->project;
        if(!empty($this->project['links']))
        {
            $projects = model('WikiProject')->all();
            $projects->order('id asc');
            $projects->in('id', $this->project['links']);
            $_projects_link = $projects->fetchall();
            if(count($_projects_link) > 0)
            {
                $projects_link = array_merge($projects_link, $_projects_link);
            }
        }
        $this->swoole->tpl->assign("projects", $projects_link);
    }

    private function getMainData()
    {
        $_cont = model('WikiContent');
        $page_id = basename($_SERVER['REQUEST_URI'], '.html');
        if(is_numeric($page_id))
        {
            $_GET['id'] = $page_id;
        }
        if (!empty($_GET['p']))
        {
            $_tree = model('WikiTree');
            $node = $_tree->get($_GET['p'], 'link')->get();
            if(empty($node))
            {
                $file = WEBPATH."/wiki/".$_GET['p'].'.md';
                if(!is_file($file))
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
        elseif(!empty($_GET['id']))
        {
            $wiki_id = intval($_GET['id']);
        }
        else
        {
            $wiki_id = $this->project['home_id'];
        }
        $wiki_page =  $_cont->get($wiki_id)->get();
        $text = $wiki_page['content'];
        $this->swoole->tpl->assign("id", $wiki_id);
        $this->swoole->tpl->assign("wiki_page", $wiki_page);

        markdown:
        //GitHub Code Parse
        $text = str_replace('```', '~~~', $text);
        $parser = new Michelf\MarkdownExtra;
        $parser->fn_id_prefix = "post22-";
        $parser->code_attr_on_pre = false;
        $parser->tab_width = 4;
        $html = $parser->transform($text);

        $this->swoole->tpl->assign("content", $html);
    }

    private function getTreeData()
    {
        $data = App\Content::getTree($this->project_id);
        $tree =  App\Content::parseTreeArray($this->project['home_id'], $data);
//        echo json_encode($tree);exit;
        $this->swoole->tpl->assign("tree", $tree);
    }

    function page()
    {
        $this->index();
    }

    function top()
    {
        $this->getTopData();
        $this->swoole->tpl->display("wiki/top.html");
    }

    function tree()
    {
        $this->swoole->tpl->assign("tree", json_encode(App\Content::getTree($this->project_id)));
        $this->swoole->tpl->display("wiki/tree.html");
    }

    function create_project()
    {
        if(!empty($_POST['name']))
        {
            $project = model('WikiProject')->get();
            $project->name = trim($_POST['name']);
            $project->links = trim($_POST['links']);
            $project->owner = trim($_POST['owner']);
            $project->close_comment = intval($_POST['close_comment']);
            if(false === $project->save())
            {
                return Swoole_js::js_back("创建失败，请稍后重试");
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

            return Swoole_js::echojs("alert(\"创建成功\");parent.window.location.href = \"/wiki/index/prid-".$project->_current_id."\"");
        }
        $form['comment'] = Form::radio('close_comment',
            array('0'=>'开启', '1'=>'关闭'), 0, false, null, 'radio-inline');
        $this->swoole->tpl->assign("form", $form);
        $this->swoole->tpl->display("wiki/create_project.html");
    }

    function setting()
    {
        if(!empty($_POST['name']))
        {
            $this->project->name = trim($_POST['name']);
            $this->project->home_id = intval($_POST['home_id']);
            $this->project->links = trim($_POST['links']);
            $this->project->owner = trim($_POST['owner']);
            $this->project->close_comment = intval($_POST['close_comment']);
            $this->project->save();
            $this->reflushPage("修改成功");
            return;
        }
        $form['comment'] = Form::radio('close_comment',
            array('0'=>'开启', '1'=>'关闭'), $this->project['close_comment'], false, null, 'radio-inline');
        $this->swoole->tpl->assign("form", $form);
        $this->swoole->tpl->display("wiki/setting.html");
    }

    private function reflushPage($info)
    {
        echo Swoole_js::js_alert($info);
        //$js = "parent.window.location.reload();";
        $js = "history.back();";
        echo Swoole_js::echojs($js);
    }

    function create()
    {
        if(!empty($_POST))
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
                $in['project_id'] = $cnode['project_id'];
            }
            $_POST['id'] = $_tree->put($in);
            App\Content::newPage($_POST);
            $this->reflushPage('增加成功');
        }
        else
        {
            $form['comment'] = Form::radio('close_comment',
                array('0'=>'开启', '1'=>'关闭'), 0, false, null, 'radio-inline');
            $this->swoole->tpl->assign("form", $form);
            $this->swoole->tpl->display("wiki/create.html");
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
            if(count($cut_node->get()) < 1)
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
        if (empty($_GET['id'])) return "error: requirer miki_page id";
        Swoole\Cookie::set('wiki_cut_id', $_GET['id'], 86400);
        return Swoole\JS::js_back("剪切成功，请到目标页面粘贴");
    }

    private function ifDeny()
    {
        $owners = explode(',', $this->project['owner']);
        if(!in_array($_SESSION['user_id'], $owners))
        {
            return true;
        }
        else
        {
            if(!isset($_COOKIE['wiki_admin']))
            {
                Swoole\Cookie::set('wiki_admin', '1', 86400*30);
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

    function modify()
    {
        if(empty($_GET['id'])) return "error: requirer miki_page id";
        $id = (int)$_GET['id'];
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');

        $cont = $_cont->get($id);
        $node = $_tree->get($id);
        $form['comment'] = Form::radio('close_comment',
            array('0'=>'开启', '1'=>'关闭'), $cont['close_comment'], false, null, 'radio-inline');
        $this->swoole->tpl->assign("form", $form);
        
        if(!empty($_POST))
        {
            $cont->title = trim($_POST['title']);
            if($_POST['content']{0} == '`')
            {
                $_POST['content'] = ' '.$_POST['content'];
            }
            $cont->content = $_POST['content'];
            $cont->close_comment = $_POST['close_comment'];
            $cont->uptime = time();

            $node->text = $cont->title;
            $node->link = trim($_POST['link']);

            $node->save();
            $cont->save();
            $this->swoole->tpl->assign("info", "修改成功");
        }
        $this->swoole->tpl->assign("node", $node->get());
        $this->swoole->tpl->assign("page", $cont->get());
        $this->swoole->tpl->display("wiki/create.html");
    }
}

