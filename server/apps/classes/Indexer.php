<?php
namespace App;

use Swoole;

require_once dirname(__DIR__) . '/classes/xunsearch/lib/XS.php';

class Indexer
{
    static $types = [
        'wiki' => [],
        'answer' => [],
        'question' => [],
    ];

    protected $xs;

    function __construct($type)
    {
        $iniFile = \Swoole::$app_path . '/configs/search/' . $type . '.ini';
        if (!is_file($iniFile))
        {
            throw new Swoole\Exception\NotFound("type[$type] not found.");
        }
        $this->xs = new \XS($iniFile);
    }

    function add($data)
    {
        $doc = new \XSDocument;
        $doc->setFields($data);
        return $this->xs->getIndex()->add($doc);
    }

    function update($data)
    {
        $doc = new \XSDocument;
        $doc->setFields($data);
        return $this->xs->getIndex()->update($doc);
    }

    function del($id)
    {
        return $this->xs->getIndex()->del($id);
    }

    function beginRebuild()
    {
        return $this->xs->getIndex()->beginRebuild();
    }

    function endRebuild()
    {
        return $this->xs->getIndex()->endRebuild();
    }
}