<?php
namespace App;

use Swoole;

class UserBase extends Swoole\Controller
{
    public $uid;

    function __construct($swoole)
    {
        parent::__construct($swoole);
        if (isset($_POST["PHPSESSID"]))
        {
            $_COOKIE['PHPSESSID'] = $_POST["PHPSESSID"];
            session_id($_COOKIE['PHPSESSID']);
        }
        Swoole::$php->session->start();
        Swoole\Auth::loginRequire();
        $this->uid = $_SESSION['user_id'];
    }
}
