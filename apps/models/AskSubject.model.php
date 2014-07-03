<?php
class AskSubject extends Swoole\Model
{
    public $table = 'ask_subject';

    function getForms()
    {
        $forms['gold'] = Swoole\Form::select('gold',range(0,200,5),0,true);
        $gets['order'] = '';
        $category = Model('AskCategory')->getMap($gets,'name');
        $forms['category'] = Swoole\Form::select('category',$category);
        return $forms;
    }
}
