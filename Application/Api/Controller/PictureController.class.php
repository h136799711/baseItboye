<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/24
 * Time: 15:44
 */

namespace Api\Controller;

use Think\Controller;
use Think\Image;

/**
 * 图片相关控制器
 * Class PictureController
 * @package Api\Controller
 */
class PictureController extends Controller
{

    //默认图片
    protected $default = "iVBORw0KGgoAAAANSUhEUgAAAWgAAAFoCAIAAAD1h/aCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpENjdGOTU2NUMwQjQxMUU1QjEyQzhCRThDMTk4OUM0QyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpENjdGOTU2NkMwQjQxMUU1QjEyQzhCRThDMTk4OUM0QyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkQ2N0Y5NTYzQzBCNDExRTVCMTJDOEJFOEMxOTg5QzRDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkQ2N0Y5NTY0QzBCNDExRTVCMTJDOEJFOEMxOTg5QzRDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+BwHAbwAAA11JREFUeNrs1DEBAAAIwzDAv8ZpQcP+REKPbpIBaJwEgHEAxgEYB2AcgHEAGAdgHIBxAMYBGAeAcQDGARgHYByAcQAYB2AcgHEAxgEYB2AcAMYBGAdgHIBxAMYBYByAcQDGARgHYBwAxgEYB2AcgHEAxgFgHIBxAMYBGAdgHIBxABgHYByAcQDGARgHgHEAxgEYB2AcgHEAGAdgHIBxAMYBGAeAcQDGARgHYByAcQDGAWAcgHEAxgEYB2AcAMYBGAdgHIBxAMYBYByAcQDGARgHYBwAxgEYB2AcgHEAxgEYB4BxAMYBGAdgHIBxABgHYByAcQDGARgHgHEAxgEYB2AcgHEAxgFgHIBxAMYBGAdgHADGARgHYByAcQDGAWAcgHEAxgEYB2AcAMYBGAdgHIBxAMYBGAeAcQDGARgHYByAcQAYB2AcgHEAxgEYB4BxAMYBGAdgHIBxABgHYByAcQDGARgHYBwAxgEYB2AcgHEAxgFgHIBxAMYBGAdgHADGARgHYByAcQDGAWAcgHEAxgEYB2AcgHEAGAdgHIBxAMYBGAeAcQDGARgHYByAcQAYB2AcgHEAxgEYB2AcAMYBGAdgHIBxAMYBYByAcQDGARgHYBwAxgEYB2AcgHEAxgFgHIBxAMYBGAdgHIBxABgHYByAcQDGARgHgHEAxgEYB2AcgHEAGAdgHIBxAMYBGAeAcQDGARgHYByAcQDGAWAcgHEAxgEYB2AcAMYBGAdgHIBxAMYBYByAcQDGARgHYBwAxgEYB2AcgHEAxgEYB4BxAMYBGAdgHIBxABgHYByAcQDGARgHgHEAxgEYB2AcgHEAxiEBYByAcQDGARgHYBwAxgEYB2AcgHEAxgFgHIBxAMYBGAdgHADGARgHYByAcQDGARgHgHEAxgEYB2AcgHEAGAdgHIBxAMYBGAeAcQDGARgHYByAcQAYB2AcgHEAxgEYB2AcAMYBGAdgHIBxAMYBYByAcQDGARgHYBwAxgEYB2AcgHEAxgFgHIBxAMYBGAdgHIBxABgHYByAcQDGARgHgHEAxgEYB2AcgHEAGAdgHIBxAMYBGAeAcQDGARgHYByAcQDGAWAcgHEAxgEYB2AcAMYBGAdgHIBxAMYBYByAcQDGARgHYByAcQB0XgABBgDQoQVqnkEqpwAAAABJRU5ErkJggg==";
    //支持裁减大小宽度
    protected  $accept_size = array(60,120,150,160,180,200,240,360,480,640,720,960);

    public function returnDefaultImage(){
        header("Content-type:image/png");
        echo base64_decode($this->default);
        exit();
    }

    public function index(){

        $id = I('get.id',0);
        $size = I('get.size',0,'intval');
        //TODO: 带图片类型，对不同类型分批处理
        if($id <= 0){
            $this->returnDefaultImage();
        }

        if(in_array($size,$this->accept_size) === false){
            $size = 0;
        }

        $Picture = D('UserPicture');
        $result = $Picture->where(array('id'=>$id))->find();

        if(empty($result)){
            $this->returnDefaultImage();
        }

        $url = '.'.$result['path'];


        if($size > 30 && $size < 1024){
            //TODO:不能太大、太小，可配置
            $url = $this->generate($result,$size);

        }
        if($url === false){
            $this->returnDefaultImage();
        }

        $time = filemtime("img.gif");
        $dt =date("D, d M Y H:m:s GMT", $time );
        header("Last-Modified: $dt");
        header("Cache-Control: max-age=844000");
//If-Modified-Since
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE']==$dt) {
            header("HTTP/1.0 304 Not Modified");
            exit;
        }


        $image = @readfile($url);
        if ($image == false) {
            $this->returnDefaultImage();
        }
        header('Content-type: image/'.$result['ext']);
        echo $image;
        exit();

    }

//    public function avatar()
//    {
//
//        if (IS_GET) {
//            $uid = I('get.uid', 0);
//            $size = I('get.size','');
//
//            $Picture = D('UserPicture');
//            $result = $Picture->where(array('uid' => $uid, 'type' => 'avatar'))->order('create_time desc')->find();
//
//            if ($result === false || empty($result['imgurl'])) {
//                $this->returnDefaultImage();
//            }
//
//            $pic_id = $result['id'];
//            $imgurl = $result['imgurl'];
//
//            if($size > 30 && $size < 1024){
//                $imgurl = $this->generate($result,$size);
//            }
//
//            $image = @file_get_contents($imgurl);
//            if ($image == false) {
//                $this->returnDefaultImage();
//            }
//            header('Content-type: image/'.$result['ext']);
//            echo $image;
//            exit();
//        }
//
//    }


    /**
     * 生成缩略图
     * @param $info
     * @param $size
     * @return string
     */
    protected function generate($info,$size){

        $thumbnail_path = C('THUMBNAIL_PATH').'/w'.$size.'/';

        $save_name = $info['savename'];

        $relative_path = $thumbnail_path.$save_name;

        if(file_exists($relative_path)){
            return $relative_path;
        }
//        $thumbnail = M('Thumbnail');
//
//        $result = $thumbnail->where(array('width'=>$size,'pic_id'=>$info['id']))->find();
//
//        if(!empty($result)){
//            return $result['url'];
//        }



        $image = new Image();

        if(!file_exists('.'.$info['path'])){
            return false;
        }

        $image->open( realpath('.'.$info['path']));

        if(!is_dir(($thumbnail_path))){
            if(!mkdir(($thumbnail_path))){
                return false;
            }
        }

        $result = $image->thumb($size, $size,Image::IMAGE_THUMB_CENTER)->save($relative_path);

        if(!file_exists($relative_path)){
            return false;
        }

        return $relative_path;
//        $entity = array(
//            'pic_id'=>$info['id'],
//            'path'=>ltrim($thumbnail_path.$save_name,'.'),
//            'url'=> $this->getSiteURL().ltrim($thumbnail_path.$save_name,"."),
//            'width'=>$size,
//            'create_time'=>time(),
//        );
//
//
//        $thumbnail_url = "";
//
//        if($thumbnail->create($entity)){
//
//            $thumbnail_id = $thumbnail->add();
//            if($thumbnail_id === false){
//                LogRecord($thumbnail->getDbError(),__FILE__.',行:'.__LINE__);
//            }else{
//                $thumbnail_url = $thumbnail_path.$save_name;
//            }
//        }
//
//        return $thumbnail_url;

    }

    protected function getSiteURL(){
        return C('SITE_URL');
    }

    public function test(){

        $image = new Image();
        $info['path'] = '/';
        if(!file_exists('.'.$info['path'])){
            return false;
        }

        $image->open( realpath('.'.$info['path']));

        $thumbnail_path = C('THUMBNAIL_PATH').'/'.date('Y-m-d',time()).'/';

        if(!is_dir(($thumbnail_path))){
            if(!mkdir(($thumbnail_path))){
                return false;
            }
        }
    }

}