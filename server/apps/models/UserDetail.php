<?php
namespace App\Model;
use Swoole;

class UserDetail extends Swoole\Model
{
    public $table = 'user_detail';
    public $primary = 'uid';
}