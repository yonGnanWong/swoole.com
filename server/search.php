<?php
require dirname(__DIR__).'/config.php';
require APPSPATH.'/classes/xunsearch/lib/XS.php';

$xs = new XS(WEBPATH.'/search.ini');
$search = $xs->getSearch();
$search->setLimit(2);
$docs = $search->search($argv[1]);

foreach ($docs as $doc)
{
    echo '#'.$doc->pid. '. ' . $doc->subject . " [" . $doc->percent() . "%] - ";
    echo date("Y-m-d", $doc->chrono) . "\n";
}
