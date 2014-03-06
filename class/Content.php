<?php
namespace MdWiki;

class Content
{
    static $php;
    static $order = "pid asc, orderid desc";
    static function getTree($project_id)
    {
        $data = self::$php->db->query("select id,text,link,pid from wiki_tree where project_id = $project_id order by ".self::$order)->fetchall();
        return $data;
    }
    static function getTree2($project_id)
    {
        $data = self::$php->db->query("select id,text,link,pid from wiki_tree where project_id = $project_id order by".self::$order)->fetchall();
        return $data;
    }
    static function newPage($content)
    {
        $_cont = createModel('WikiContent');
        $in2['title'] = $content['title'];
        if(strlen($content['content']) > 0 and $content['content']{0} == '`')
        {
            $content['content'] = ' '.$content['content'];
        }
        $in2['content'] = $content['content'];
        $in2['id'] = $content['id'];
        $_cont->put($in2);
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
}
