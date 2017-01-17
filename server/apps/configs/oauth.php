<?php
$oauth['weibo']['appid'] = '1418646107';
$oauth['weibo']['skey'] = '8b1fed32df42548d71acac00dddb05bb';
$oauth['weibo']['callback'] = WEBROOT.'/page/callback_weibo/';

$oauth['qq']['appid'] = '221403';
$oauth['qq']['skey'] = 'f3f2490a725f75e154bf2a37773213b8';
$oauth['qq']['callback'] = WEBROOT.'/page/callback_qq/';
$oauth['qq']['scope'] = 'get_user_info';

return $oauth;
