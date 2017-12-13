<?php
namespace App;

use Swoole;

class UserBase extends Swoole\Controller
{
    public $uid;
    protected $user;

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
        $this->user = Model('UserInfo')->get($this->uid)->get();
        $this->tpl->assign('_user', $this->user);
    }

    function infoPage($info, $detail = '', $error = false)
    {
        $this->assign('info', $info);
        $this->assign('detail', $detail);
        $this->display('include/page.php');
    }

    protected function isActiveMenu($m, $v = '')
    {
        if ($this->env['mvc']['controller'] == $m)
        {
            if (!empty($v))
            {
                return $this->env['mvc']['view'] == $v;
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    protected function getMenuItem($c, $v, $name)
    {
        $html = "<li ";
        if ($this->isActiveMenu($c, $v))
        {
            $html .= 'class="active"';
        }
        $html .= '>';
        $html .= '<a href="/' . $c . '/' . $v . '/" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span
        class="menu-item-parent">' . $name . '</span></a></li>';
        return $html;
    }
}
