<?php
$client_time = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0);
$now = time();
$now_list = time() - 60 * 5;
if ($client_time < $now and $client_time > $now_list)
{
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $client_time) . 'GMT', true, 304);
}
else
{
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $now) . ' GMT', true, 200);
}
$js['sh'] = array(
    '/static/scripts/shCore.js',
    '/static/scripts/shBrushBash.js',
    '/static/scripts/shBrushCpp.js',
    '/static/scripts/shBrushCSharp.js',
    '/static/scripts/shBrushCss.js',
    '/static/scripts/shBrushDelphi.js',
    '/static/scripts/shBrushDiff.js',
    '/static/scripts/shBrushGroovy.js',
    '/static/scripts/shBrushJava.js',
    '/static/scripts/shBrushJScript.js',
    '/static/scripts/shBrushPhp.js',
    '/static/scripts/shBrushPlain.js',
    '/static/scripts/shBrushPython.js',
    '/static/scripts/shBrushRuby.js',
    '/static/scripts/shBrushScala.js',
    '/static/scripts/shBrushSql.js',
    '/static/scripts/shBrushVb.js',
    '/static/scripts/shBrushXml.js'
);
define("WEBPATH", realpath('../'));
if (isset($_GET['file']))
{
    echo getJS(explode('|', $_GET['file']));
}
elseif (isset($_GET['g']))
{
    echo getJS($js[$_GET['g']]);
}

function getJS($files)
{
    $js = '';
    foreach ($files as $f)
    {
        $js .= file_get_contents(WEBPATH . $f);
        $js .= "\n";
    }

    return $js;
}