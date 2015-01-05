<?php
namespace App\Model;
use Swoole;

class Cpage extends Swoole\Model
{
	//Here write Database table's name
	public $table = 'st_page';
	public $catelog_table ='st_catelog';
	public $pagesize=25;
}
