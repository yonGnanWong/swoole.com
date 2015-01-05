<?php
namespace App\Model;
use Swoole;
class AskContent extends Swoole\Model
{
    public $table = 'ask_content';
    public $primary = 'aid';
}