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
        $this->getComments();
        $this->swoole->tpl->display("wiki/noframe/index.html");
    }

    /**
     * 高亮关键词
     * @param \XSSearch $xs
     * @param $subject
     * @return mixed
     */
    protected static function highlight($xs, $subject)
    {
        return $xs->highlight(htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'));
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

        $pagesize = 10;
        $page = empty($_GET['page']) ? 1: intval($_GET['page']);

        /**
         * 搜索类型
         */
        $type = empty($_GET['type']) ? 'wiki' : trim($_GET['type']);
        $iniFile = Swoole::$app_path . '/configs/search/' . $type . '.ini';
        if (!is_file($iniFile))
        {
            $this->http->status(403);
            return "bad request";
        }

        $link_tpl = '';
        $page_tpl = "/wiki/search/?type={$type}&q=" . urlencode($_GET['q']) . '&page={page}';
        $s = microtime(true);
        if ($type == 'wiki')
        {
            $link_tpl = '/wiki/page/{id}.html';
        }
        elseif ($type == 'question')
        {
            $link_tpl = 'http://group.swoole.com/question/{id}';
        }
        elseif ($type == 'answer')
        {
            $link_tpl = 'http://group.swoole.com/question/{question_id}#answer_list_{id}';
        }

        $xs = new \XS($iniFile);
        $search = $xs->getSearch();
        $q = trim($_GET['q']);
        $search->setQuery($q);
        $total = $search->count();
        if (($page - 1) * $pagesize > $total)
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
            $li['title'] = self::highlight($search, $doc->subject);
            $li['desc'] = self::highlight($search, $doc->message);
            if ($type == 'answer')
            {
                $li['question_id'] = $doc->question_id;
            }
            $list[] = $li;
        }
        $pager->page_tpl = $page_tpl;
        $this->assign('list', $list);
        $this->assign('cost_time', round(microtime(true) - $s, 3));
        $this->assign('count', $total);
        $this->assign('link_tpl', $link_tpl);
        $this->assign('pager', $pager->render());
        $this->display("wiki/noframe/search.php");
    }

    function main()
    {
        $this->page();
    }

    protected function getComments()
    {
        $thread_key = 'wiki-'.$this->tpl->_tpl_vars['id'];
        $t = table('duoshuo_posts');
        $list = $t->gets(array('thread_key' => $thread_key, 'order' => 'id asc'));
        $uids = [];
        foreach($list as $li)
        {
            if ($li['uid'])
            {
                $uids[$li['uid']] = 1;
            }
        }
        if (count($uids) > 0)
        {
            $users = model('UserInfo')->getMap(array('in' => ['id', array_keys($uids)], 'select' => 'id, nickname, avatar'));
        }
        else
        {
            $users = [];
        }
        foreach($list as &$li)
        {
            $li['message'] = App\Content::parseMarkdown($li['message']);
            if ($li['uid'])
            {
                $user = $users[$li['uid']];
                App\Api::updateAvatarUrl($user, true);
                $li['author_name'] = $user['nickname'];
                $li['avatar'] = $user['avatar'];
                $li['author_url'] = "http://www.swoole.com/page/user/uid-" . $li['uid'];
            }
        }
        $this->tpl->assign('comments', $list);
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
        $this->getComments();
        $this->swoole->tpl->display("wiki/noframe/index.html");
    }

    //获取项目信息
    protected function getProjectInfo()
    {
        $this->project = createModel('WikiProject')->get($this->project_id);
        if (empty($this->project))
        {
            $this->http->finish("您访问的项目不存在");
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
        $html = App\Content::getWikiHtml($wiki_id, $text);
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

    function upload()
    {
        return App\Content::upload();
    }

    protected function createPage($pid)
    {

    }

    function edit()
    {
        $this->session->start();
        $this->user->loginRequire();
        if (empty($_GET['id']))
        {
            return "error: requirer miki_page id";
        }

        $uid = $_SESSION['user_id'];
        if ($info = App\Api::badUser($uid))
        {
            $this->http->header('Content-Type', 'text/html; charset=utf-8');
            return "您已被列入黑名单，请联系管理员。<br />操作时间：{$info['created_time']}<br />原因：{$info['remarks']}";
        }

        $id = (int)$_GET['id'];
        $_cont = model('WikiContent');
        $_tree = model('WikiTree');

        if (!empty($_GET['create']))
        {
            $cont = $_cont->get();
            $node = $_tree->get();
            $create = true;
        }
        else
        {
            $cont = $_cont->get($id);
            $node = $_tree->get($id);
            $create = false;

            if ($cont->close_edit == 1)
            {
                return "管理员已禁止编辑本页面。";
            }
        }

        if (!empty($_POST))
        {
            if (empty($_POST['title']))
            {
                $this->assign("info", ['message' => "标题不能为空！", 'code' => 4001]);
                goto display;
            }
            if (empty($_POST['content']))
            {
                $this->assign("info", ['message' => "内容不能为空！", 'code' => 4002]);
                goto display;
            }
            if (!empty($_POST['content']) and $_POST['content'][0] == '`')
            {
                $_POST['content'] = ' ' . $_POST['content'];
            }
            //检查内容是否变更
            if ($_POST['content'] === $cont->content and trim($_POST['title']) == $cont->title)
            {
                $this->assign("info", ['message' => "标题和内容无任何修改", 'code' => 4003]);
                goto display;
            }

            //编辑
            if (!$create)
            {
                $cont->title = trim($_POST['title']);
                $cont->content = $_POST['content'];
                $cont->uptime = time();

                //更新节点
                $node->update_uid = $uid;
                $node->text = $cont->title;

                //写入历史记录
                $_historyTable = table('wiki_history');
                $_historyTable->put(array(
                    'wiki_id' => $node->id,
                    'uid' => $uid,
                    'content' => $cont->content,
                    'title' => $cont->title,
                    'version' => intval($cont->version),
                ));
                //更新索引
                if ($this->project_id == 1)
                {
                    $index = new App\Indexer('wiki');
                    $index->update([
                        'pid' => $node->id,
                        'subject' => $cont->title,
                        'message' => $cont->content,
                        'chrono' => time()
                    ]);
                }
                //增加版本号
                $cont->version = intval($cont->version) + 1;

                //更新缓存
                App\Content::clearCache($node->id);
                if (!$node->save())
                {
                    error:
                    $this->assign("info", ['message' => "提交失败，请稍后重试！", 'code' => 201]);
                    goto display;
                }
                if (!$cont->save())
                {
                    goto error;
                }
                $this->assign("info", ['message' => "编辑成功，感谢您的贡献！", 'code' => 0]);
            }
            else
            {
                $page = $_tree->get($_GET['id'])->get();

                $cont->title = trim($_POST['title']);
                $cont->content = $_POST['content'];

                $node->project_id = $page['project_id'];
                $node->update_uid = $node->create_uid = $uid;
                $node->text = $cont->title;

                //创建子页面
                if ($_GET['create'] == 'child')
                {
                    $node->pid = $_GET['id'];
                }
                //创建同级页面
                else
                {
                    $node->pid = $page['pid'];
                }
                $node->publish = 1;
                if (!$node->save())
                {
                    goto error;
                }
                $cont->id = $node->_current_id;
                $cont->uptime = time();
                $cont->version = 1;
                //写入历史记录
                $_historyTable = table('wiki_history');
                $_historyTable->put(array(
                    'wiki_id' => $node->id,
                    'uid' => $uid,
                    'content' => $cont->content,
                    'title' => $cont->title,
                    'version' => intval($cont->version),
                ));
                //更新索引
                if ($this->project_id == 1)
                {
                    $index = new App\Indexer('wiki');
                    $index->add([
                        'pid' => $node->id,
                        'subject' => $cont->title,
                        'message' => $cont->content,
                        'chrono' => time()
                    ]);
                }
                if (!$cont->save())
                {
                    goto error;
                }
                $this->assign("info", ['message' => "添加成功，感谢您的贡献！", 'code' => 0]);
            }
        }
        display:
        $_node = $node->get();
        if (empty($_node['id']))
        {
            $_node['id'] = $node->_current_id;
        }
        $this->assign("node", $_node);
        $this->assign("page", $cont->get());
        $this->display();
    }
}

