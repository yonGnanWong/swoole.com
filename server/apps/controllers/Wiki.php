<?php
namespace App\Controller;
use App;
use Swoole;

require_once dirname(__DIR__) . '/classes/Content.php';
require_once dirname(__DIR__) . '/classes/xunsearch/lib/XS.php';

class Wiki extends Swoole\Controller
{
    public $if_filter = false;

    protected $project_id;
    protected $project;
    protected $pageInfo;
    protected $nodeInfo;

    function __construct($swoole)
    {
        App\Content::$php = $swoole;
        parent::__construct($swoole);
    }

    function index()
    {
        if (isset($_GET['prid']))
        {
            $this->project_id = intval($_GET['prid']);
        }
        else
        {
            $this->project_id = 1;
        }

        $this->getProjectInfo();
        $this->getProjectLinks();
        $this->getTreeData();
        $_GET['id'] = $this->project['home_id'];
        $this->getPageInfo();
        $this->swoole->tpl->display("wiki/noframe/index.html");
    }

    function search()
    {
        if (isset($_GET['prid']))
        {
            $this->project_id = intval($_GET['prid']);
        }
        else
        {
            $this->project_id = 1;
        }

        if (empty($_GET['q']))
        {
            return "请输入搜索的关键词";
        }

        $this->getProjectInfo();
        $this->getProjectLinks();
        $this->getTreeData();
        $_GET['id'] = $this->project['home_id'];

        $pagesize = 10;
        $page = empty($_GET['page']) ? 1: intval($_GET['page']);
        $xs = new \XS(WEBPATH.'/search.ini');
        $search = $xs->getSearch();
        $q = trim($_GET['q']);
        $search->setQuery($q);
        $total = $search->count();
        if ($page * $pagesize > $total)
        {
            $page = 1;
        }
        $search->setLimit($pagesize, ($page - 1) * $pagesize);
        $pager = new Swoole\Pager(array('page' => $page, 'perpage' => $pagesize, 'total' => $total));
        $docs = $search->search();
        $list = array();
        foreach ($docs as $doc)
        {
            $li['id'] = $doc->pid;
            $li['title'] = $doc->subject;
            $li['desc'] = $doc->message;
            $list[] = $li;
        }
        $pager->page_tpl = "/wiki/search/?q=".urlencode($_GET['q']).'&page={page}';
        $this->tpl->assign('list', $list);
        $this->tpl->assign('pager', $pager->render());
        $this->tpl->display("wiki/noframe/search.html");
    }

    function main()
    {
        $this->page();
    }

    function page()
    {
        $this->getPageInfo();
        if(!empty($this->nodeInfo))
        {
            $this->project_id = $this->nodeInfo['project_id'];
        }
        else
        {
            $this->project_id = 1;
        }
        $this->getProjectInfo();
        $this->getTreeData();
        $this->getProjectLinks();
        $this->swoole->tpl->display("wiki/noframe/index.html");
    }

    //获取项目信息
    protected function getProjectInfo()
    {
        $this->project = createModel('WikiProject')->get($this->project_id);
        if(empty($this->project))
        {
            echo "您访问的项目不存在";
            exit;
        }
        $this->swoole->tpl->assign("project_id", $this->project_id);
        $this->swoole->tpl->assign("project", $this->project);
    }

    //相关的项目
    protected function getProjectLinks()
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

    private function getPageInfo()
    {
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');
        $node = false;

        if (!empty($_GET['p']))
        {
            $node = $_tree->get($_GET['p'], 'link')->get();
            if (empty($node))
            {
                $text = "您访问的页面不存在！[点击跳转到首页](http://www.swoole.com/wiki/index/)";
                goto markdown;
            }
            $_GET['id'] = $wiki_id = $node['id'];
        }
        else if (!empty($_GET['id']))
        {
            $wiki_id = intval($_GET['id']);
        }
        if (!$node)
        {
            $node = $_tree->get($wiki_id)->get();
        }
        $this->pageInfo =  $_cont->get($wiki_id)->get();

        if (empty($this->pageInfo))
        {
            $this->http->status(404);
            $this->http->finish("<h1>Page#{$wiki_id} Not Found.</h1>");
        }

        //更新阅读计数
        $_cont->set($wiki_id, array('read_count' => $this->pageInfo['read_count'] + 1));

        $this->nodeInfo =  $_tree->get($wiki_id)->get();
        if (!empty($node['markdown_file']))
        {
            $text = file_get_contents($this->config['site']['git_path'].'/'.$node['markdown_file']);
        }
        else
        {
            $text = $this->pageInfo['content'];
        }
        $this->swoole->tpl->assign("id", $wiki_id);
        $this->swoole->tpl->assign("wiki_page",  $this->pageInfo);

        markdown:
        $html = App\Content::md2html($wiki_id, $text);
        $this->swoole->tpl->assign("content", $html);
    }

    private function getTreeData()
    {
        //所有子节点的Tree
        //$data = App\Content::getTree($this->project_id);
        //$tree =  App\Content::parseTreeArray($this->project['home_id'], $data);
        $node_id = !empty($_GET['id']) ? intval($_GET['id']) : $this->project['home_id'];
        //仅当前树
        $data = App\Content::getTree3($this->project_id, $node_id);
        $tree = App\Content::parseTreeArray($this->project['home_id'], $data);
        //debug($tree);
//        echo json_encode($tree);exit;
        $this->swoole->tpl->assign("tree", $tree);
    }

    function edit()
    {
        $this->user->loginRequire();
        if (empty($_GET['id']))
        {
            return "error: requirer miki_page id";
        }

        $id = (int)$_GET['id'];
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');

        $cont = $_cont->get($id);
        $node = $_tree->get($id);

        if ($cont->close_edit == 1)
        {
            return "管理员已禁止编辑本页面。";
        }

        if (!empty($_POST))
        {
            if (!empty($_POST['content']) and $_POST['content'][0] == '`')
            {
                $_POST['content'] = ' '.$_POST['content'];
            }

            $uid =  $_SESSION['user_id'];
            //更新内容和标题
            if (!($_POST['content'] === $cont->content and trim($_POST['title']) == $cont->title))
            {
                //写入历史记录
                $_historyTable = table('wiki_history');
                $_historyTable->put(array(
                    'wiki_id' => $node->id,
                    'uid' => $uid,
                    'content' => $cont->content,
                    'title' => $cont->title,
                    'version' => intval($cont->version),
                ));
                //增加版本号
                $cont->version = intval($cont->version) + 1;
            }
            else
            {
                goto display;
            }

            $cont->title = trim($_POST['title']);
            $cont->content = $_POST['content'];
            $cont->uptime = time();

            //更新节点
            $node->update_uid = $uid;
            $node->text = $cont->title;
            $node->link = trim($_POST['link']);

            //更新缓存
            App\Content::clearCache($node->id);
            if (!$node->save())
            {
                error:
                $this->assign("info", "提交失败，请稍后重试！");
            }
            if (!$cont->save())
            {
                goto error;
            }
            $this->assign("info", "编辑成功，感谢您的贡献！");
        }
        display:
        $this->assign("node", $node->get());
        $this->assign("page", $cont->get());
        $this->display();
    }
}

