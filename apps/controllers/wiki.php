<?php
require_once __DIR__ . '/../classes/php-markdown/Michelf/Markdown.php';
require_once __DIR__ . '/../classes/php-markdown/Michelf/MarkdownExtra.php';
require_once __DIR__ . '/../classes/Content.php';

use \Michelf;

class wiki extends Swoole\Controller
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
        if(isset($_GET['prid']))
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

        if (!empty($_GET['p']))
        {
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
        $this->pageInfo =  $_cont->get($wiki_id)->get();
        $this->nodeInfo =  $_tree->get($wiki_id)->get();
        $text =  $this->pageInfo['content'];
        $this->swoole->tpl->assign("id", $wiki_id);
        $this->swoole->tpl->assign("wiki_page",  $this->pageInfo);

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
        //所有子节点的Tree
        //$data = App\Content::getTree($this->project_id);
        //$tree =  App\Content::parseTreeArray($this->project['home_id'], $data);
        $node_id = isset($_GET['id']) ? intval($_GET['id']) : $this->project['home_id'];
        //仅当前树
        $data = App\Content::getTree3($this->project_id, $node_id);
        $tree = App\Content::parseTreeArray($this->project['home_id'], $data);
        //debug($tree);
//        echo json_encode($tree);exit;
        $this->swoole->tpl->assign("tree", $tree);
    }

    function tree()
    {
        $this->swoole->tpl->assign("tree", json_encode(App\Content::getTree($this->project_id)));
        $this->swoole->tpl->display("wiki/tree.html");
    }
}

