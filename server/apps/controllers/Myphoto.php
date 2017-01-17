<?php
namespace App\Controller;
use Swoole;
use App;

class Myphoto extends App\UserBase
{
    /**
     * 相片的呈现
     */
    function index()
    {
        $gets['uid'] = $this->uid;
        $gets['select'] = 'id,imagep';
        $gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
        $gets['pagesize'] =15;
        $pager = null;
        $photo = $this->swoole->model->UserPhoto->gets($gets, $pager);
        $this->swoole->tpl->assign('photo',$photo);
        $this->swoole->tpl->assign('count',$pager->total);
        $this->swoole->tpl->assign('pager',$pager->render());
        if(isset($_GET['from'])) $this->swoole->tpl->display('myphoto_insert.html');
        else $this->swoole->tpl->display('myphoto_index.html');
    }

    /**
     * 用flash添加照片
     */
    function add_photo()
    {
        if ($_FILES)
        {
            global $php;
            $php->upload->thumb_width = 136;
            $php->upload->thumb_height = 136;
            $php->upload->max_width = 1280;
            $php->upload->max_height = 1280;
            $php->upload->thumb_qulitity = 90;
            $php->upload->sub_dir = 'user_images';
            $up_pic = Swoole::$php->upload->save('Filedata');
            if (empty($up_pic))
            {
                return '上传失败，请重新上传！ Error:' . $php->upload->error_msg;
            }
            $data['picture'] = $up_pic['url'];
            $data['imagep'] = $up_pic['thumb'];

            $data['uid'] = $_POST['uid'];
            $up_pic['photo_id'] = $this->swoole->model->UserPhoto->put($data);
            
            /* if(isset($_POST['post']))
            {
            	Api::feed('photo', $data['uid'], 0, $up_pic['photo_id']);
            } */
            return json_encode($up_pic);
        }
        else $this->swoole->tpl->display('myphoto_add_photo.html');
    }
    function show()
    {
        $pid = (int)$_GET['id'];
        App\Widget::photoDetail($pid,$this->uid);
        $this->swoole->tpl->display();
    }
    
    function delete()
    {
    	if(empty($_GET['id'])) error(409);
    	$id = (int)$_GET['id'];
    	if($this->swoole->model->UserPhoto->del($id))
    	{
    		return Swoole\JS::js_back('删除成功');
    	}
    }
}