<?php
class UserBase extends Controller
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
        session();
        Auth::$login_url = '/page/login/?';
        if(Auth::login_require() === false)
        {
            return Swoole\Http::finish();
        }
        $this->uid = $_SESSION['user_id'];
    }
}
