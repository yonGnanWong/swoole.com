<?php
class myphoto extends App\UserBase
{
    /**
     * 相片的呈现
     * @return unknown_type
     */
    function index()
    {
        $gets['uid'] = $this->uid;
        $gets['select'] = 'id,imagep';
        $gets['page'] = empty($_GET['page'])?1:(int)$_GET['page'];
        $gets['pagesize'] =15;

        $photo = $this->swoole->model->UserPhoto->gets($gets,$pager);
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
        if($_FILES)
        {
            global $php;
            $php->upload->thumb_width = 136;
            $php->upload->thumb_height = 136;
            $php->upload->max_width = 500;
            $php->upload->max_height = 500;
            $php->upload->thumb_qulitity = 100;
            
            if(class_exists('SaeStorage', false))
            {
            	$s = new SaeStorage;
            	$file_id = uniqid('pic_', false).mt_rand(1, 100);
            	$tmp_file = SAE_TMP_PATH.'/thum_'.$file_id.'.jpg';
            	Image::thumbnail($_FILES['Filedata']['tmp_name'], 
            			$tmp_file, 
            			$php->upload->thumb_width, 
            			$php->upload->thumb_height, 
            			$php->upload->thumb_qulitity, 
            			false);
            	
            	$pic = '/uploads/'.$file_id.".jpg";
            	$ret = $s->upload('static' , $pic, $_FILES['Filedata']['tmp_name']);
            	if($ret)
            	{
            		$data['picture'] = $s->getUrl('static', $pic);
            	}
            	else
            	{
            		echo $s->errmsg().' : '.$s->errno();
                    return;
            	}
            	
            	$thum_pic = '/uploads/thum_'.$file_id.'.jpg';
            	$ret = $s->upload('static' , $thum_pic, $tmp_file);
            	if($ret)
            	{
            		$data['imagep'] = $s->getUrl('static', $thum_pic);
            	}
            	else
            	{
            		echo $s->errmsg().' : '.$s->errno();
                    return;
            	}
            }
            else
           {
            	$php->upload->sub_dir = 'user_images';
            	$up_pic = $php->upload->save('Filedata');
            	if(empty($up_pic))
            	{
                    echo '上传失败，请重新上传！ Error:'.$php->upload->error_msg;
                    return;
            	}
            	$data['picture'] = $up_pic['name'];
            	$data['imagep'] = $up_pic['thumb'];
            }
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
        Widget::photoDetail($pid,$this->uid);
        $this->swoole->tpl->display();
    }
    
    function delete()
    {
    	if(empty($_GET['id'])) error(409);
    	$id = (int)$_GET['id'];
    	if($this->swoole->model->UserPhoto->del($id))
    	{
    		return Swoole_js::js_back('删除成功');
    	}
    }
}