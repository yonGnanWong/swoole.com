<?php
namespace App\Model;
use Swoole;

class UserSkill extends Swoole\Model
{
	public $table = 'user_skill';

	function getMap($gets, $field = null)
	{
	    $_skill = $this->swoole->cache->get('user_skill_map');
	    if(empty($r))
	    {
    	    $all = $this->all();
    	    $all->order('');
    	    $skill = $all->fetchall();
    		foreach($skill as $sk)
    		{
    		    $_skill[$sk['id']] = $sk['name'];
    		}
    		$this->swoole->cache->set('user_skill_map', $_skill, 3600);
	    }
		return $_skill;
	}
}
