<?php
class cms extends Controller
{
	private $app;
    private $_model;
	private function common()
	{
		if(empty($_GET['app'])) exit;
		$this->app = ucwords(substr($_GET['app'],0,10));
		$this->swoole->tpl->assign('app', $this->app);
		$this->_model = createModel($this->app);
	}
	
	function index()
	{
		
	}

	function detail()
	{
		if(empty($_GET['id']))
		{
			return "Access Deny";
		}
        $this->common();
		$aid = (int)$_GET['id'];
		//模板名称
		$tplname = strtolower($this->app).'_detail.html';

		//获取详细内容
		$det = $this->_model->get($aid)->get();
        if(!empty($det['uptime']) and Swoole\Tool::httpExpire($det['uptime']) === false)
        {
            exit;
        }
		//阅读次数增加
		$this->_model->set($aid,array('click_num'=>'`click_num`+1'));

		//关键词
		if(!empty($_GET['q']))
		{
			$det['content'] = preg_replace("/({$_GET['q']})/i","<font color=red>\\1</font>",$det['content']);
		}

		//获取小分类信息
		$cate = getCategory($det['cid']);
		$this->swoole->tpl->assign("cate",$cate);

		$ccate = getCategory($det['fid']);
		$this->swoole->tpl->assign("ccate",$ccate);

		$comments = createModel('UserComment')->getByAid($this->app,$det['id']);
		//是否使用特殊模板
		if($ccate['tpl_detail']) $tplname = $ccate['tpl_detail'];
		if($cate['tpl_detail']) $tplname = $cate['tpl_detail'];
		$this->swoole->tpl->assign('comments',$comments);
		$this->swoole->tpl->assign('det',$det);
		$this->swoole->tpl->display($tplname);

	}

	function category()
	{

		if(empty($_GET['cid']))
		{
			return "Access Deny";
		}

		//Error::dbd();
		$this->common();
		$tplname = strtolower($this->app).'_list.html';
		$cate_id = (int)$_GET['cid'];
		$cate = getCategory($cate_id);
		if(empty($cate))
		{
			Swoole_js::js_back('不存在的分类！','/index.php');
			exit;
		}
        if(!empty($cate['uptime']) and Swoole\Tool::httpExpire($cate['uptime']) === false)
        {
            exit;
        }
		if($cate['fid']==0)
		{
			$this->swoole->tpl->assign("fid",$cate_id);
			$this->swoole->tpl->assign("ccate",$cate);
			if($cate['tplname']) $tplname = $cate['tplname'];
			$gets['fid'] = $cate_id;
		}
		else
		{
			if($cate['tplname']) $tplname = $cate['tplname'];
			$this->swoole->tpl->assign("cate",$cate);
			$ccate = $this->swoole->db->query("select * from st_catelog where id={$cate['fid']} limit 1")->fetch();
			$this->swoole->tpl->assign("ccate",$ccate);
			$gets['cid'] = $cate_id;
		}

		$pager = null;
		$gets['order'] = 'addtime desc';
		$gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
		$gets['pagesize'] = empty($this->_model->pagesize)?$this->swoole->config['cms']['pagesize']:$this->_model->pagesize;
		$gets['select'] = "id,title,addtime";
		$list = $this->_model->gets($gets,$pager);
		if($this->swoole->config['cms']['html_static']) $pager->page_tpl = WEBROOT."/$this->app/list_{$cate_id}_%s.html";

		$pager = array('total'=>$pager->total,'render'=>$pager->render());
		$this->swoole->tpl->assign('pager',$pager);
		$this->swoole->tpl->assign("list",$list);
		$this->swoole->tpl->assign('cid',$cate_id);
		$this->swoole->tpl->display($tplname);
	}
}