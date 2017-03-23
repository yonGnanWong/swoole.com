<?php
namespace App;

require_once dirname(__DIR__) . '/classes/php-markdown/Michelf/Markdown.php';
require_once dirname(__DIR__) . '/classes/php-markdown/Michelf/MarkdownExtra.php';
require_once dirname(__DIR__) . '/classes/Content.php';

use \Michelf;
use \Swoole;

class Content
{
    static $php;
    static $order = "pid asc, orderid desc";
    static $select = "id,text,link,pid,order_by_time,publish";

    static function getTree($project_id)
    {
        $data = self::$php->db->query("select ".self::$select." from wiki_tree where project_id = $project_id order by ".self::$order)->fetchall();
        return $data;
    }

    static function getTree2($project_id)
    {
        $data = self::$php->db->query("select ".self::$select." from wiki_tree where project_id = $project_id order by ".self::$order)->fetchall();
        return $data;
    }

    static function getTree3($project_id, $node_id = 0)
    {
        if ($node_id)
        {
            $self = self::$php->db->query("select ".self::$select." from wiki_tree where id = $node_id limit 1")->fetch();
            if ($self['order_by_time'])
            {
                $order = 'id desc';
            }
            else
            {
                $order = self::$order;
            }
            //获取子节点
            $nodes = self::$php->db->query(
                "select " . self::$select . " from wiki_tree where project_id = $project_id
                and pid = $node_id and publish=1 order by $order"
            )->fetchall();
            $nodes[] = $self;
        }
        else
        {
            //获取Node本身和子节点
            $nodes = self::$php->db->query(
                "select " . self::$select . " from wiki_tree where project_id = $project_id
                and (pid = $node_id or id = $node_id) and publish=1 order by " . self::$order
            )->fetchall();
        }

        $map = array();
        foreach($nodes as $k => $node)
        {
            $map[$node['id']] = $node;
            //删除掉自己
            if ($node['id'] == $node_id and $node['pid'] != -1)
            {
                unset($nodes[$k]);
            }
        }

        //根节点直接返回
        if ($map[$node_id]['pid'] == -1)
        {
            return $nodes;
        }

        //非根节点，向上寻找根节点
        $find_node_id = $map[$node_id]['pid'];

        //循环获取到顶点
        while (true)
        {
            //父节点
            $parent_node = self::$php->db->query("select " . self::$select . " from wiki_tree where id = $find_node_id limit 1")->fetch();
            if ($parent_node['order_by_time'])
            {
                $order = 'id desc';
            }
            else
            {
                $order = self::$order;
            }
            //兄弟节点
            $childs = self::$php->db->query("select " . self::$select . " from wiki_tree where pid = $find_node_id and publish=1 order by $order")->fetchall();
            $nodes = array_merge($nodes, $childs);

            //达到根节点，退出循环
            if ($parent_node['pid'] == -1)
            {
                $nodes[] = $parent_node;
                break;
            }
            else
            {
                $find_node_id = $parent_node['pid'];
            }
        }
        return $nodes;
    }

    static function getPage($id)
    {

    }

    static $node_list;
    static $child_list;

    static function parseTreeArray($root_id, $data)
    {
        self::$node_list = array();
        self::$child_list = array();
        foreach($data as $add)
        {
            self::$node_list[$add['id']] = $add;
        }
        //去掉root节点
        $result = self::$node_list[$root_id];
        foreach(self::$node_list as $node)
        {
            if($node['id'] == $root_id) continue;
            self::$child_list[$node['pid']][] = $node;
        }
        $result['child'] = self::getTreeChilds($root_id);
        return $result;
    }

    static private function getTreeChilds($id)
    {
        //有子节点，查询子节点是否还有子节点
        if(isset(self::$child_list[$id]))
        {
            $childs = self::$child_list[$id];
            foreach($childs as &$c)
            {
                $c['child'] =  self::getTreeChilds($c['id']);
            }
            return $childs;
        }
    }

    static function parseMarkdown($text)
    {
        $text = str_replace('```', '~~~', $text);
        $parser = new Michelf\MarkdownExtra;
        $parser->fn_id_prefix = "post22-";
        $parser->code_attr_on_pre = false;
        $parser->tab_width = 4;
        return $parser->transform($text);
    }

    /**
     * @param $wiki_id
     * @param $text
     * @return string
     */
    static function getWikiHtml($wiki_id, $text)
    {
        $key = 'wiki_page_'.$wiki_id;
        $html = \Swoole::$php->cache->get($key);
        if (!$html)
        {
            //GitHub Code Parse
            $html = self::parseMarkdown($text);
            \Swoole::$php->cache->set($key, $html, 0);
        }
        else
        {
            \Swoole::$php->http->header('X-Cache', 'Memcache');
        }
        return $html;
    }

    /**
     * 更新页面
     * @param $wiki_id
     * @return bool
     */
    static function clearCache($wiki_id)
    {
        $key = 'wiki_page_' . $wiki_id;
        return \Swoole::$php->cache->delete($key);
    }
}
