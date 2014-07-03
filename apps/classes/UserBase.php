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
        Swoole\Auth::$login_url = '/page/login/?';
        if (Swoole\Auth::login_require() === false)
        {
            return $this->swoole->http->finish();
        }
        $this->uid = $_SESSION['user_id'];
    }
}
