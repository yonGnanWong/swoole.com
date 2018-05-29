<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>Swoole 社区手机号码登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/smartadmin-production.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/smartadmin-skins.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/demo.css">
    <link rel="icon" href="/static/smartadmin/img/favicon/favicon.ico" type="image/x-icon">
    <style type="text/css">
      @media screen and (max-width: 800px) {
        #main {
          margin-top: auto !important;
        }
      }
      @media screen and (max-width: 372px) {
        #main {
          width: auto !important;
        }
      }
      @media screen and (max-width: 300px) {
        #main {
          width: 300px !important;
        }
      }
    </style>
</head>
<body style="height:auto">
<?php
$display = empty($_GET['display']) ? '' : $_GET['display'];
if ($display != 'popup'): ?>
    <header id="header">
        <div>
            <h1>&nbsp;&nbsp;Swoole 社区登录</h1>
        </div>
    </header>
<?php endif; ?>

<div id="main" role="main"
     style="width: 360px; margin: 0 auto; margin-top: <?= ($display == 'popup' ? '0' : '130') ?>px;">
    <div id="content">
        <!-- row -->
        <div class="row">
        <div class="alert alert-warning fade in" id="msg" style="display: none;">
              <i class="fa-fw fa fa-times"></i>
            <span id="msg_content"></span>
        </div>
            </div>
        <div class="row">
