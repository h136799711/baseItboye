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
    protected function _initialize(){
        header("X-Copyright:http://www.itboye.com");
    }

    //默认图片
    protected $default = "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MDER88d8/w/Qf8y8BEvTLKZp2yw23ncDb5+kE8rlWnKRdI90HkVU1JYp6Tz3ZuWvrq6kZ/7617/+MrMQIECAAAECBAiUEfhDmUoVSoAAAQIECBAgMBcQAHUEAgQIECBAgEAxAQGwWIMrlwABAgQIECAgAOoDBAgQIECAAIFiAgJgsQZXLgECBAgQIEBAANQHCBAgQIAAAQLFBATAYg2uXAIECBAgQICAAKgPECBAgAABAgSKCQiAxRpcuQQIECBAgAABAVAfIECAAAECBAgUExAAizW4cgkQIECAAAECAqA+QIAAAQIECBAoJiAAFmtw5RIgQIAAAQIEBEB9gAABAgQIECBQTEAALNbgyiVAgAABAgQICID6AAECBAgQIECgmIAAWKzBlUuAAAECBAgQEAD1AQIECBAgQIBAMQEBsFiDK5cAAQIECBAgIADqAwQIECBAgACBYgICYLEGVy4BAgQIECBAQADUBwgQIECAAAECxQQEwGINrlwCBAgQIECAgACoDxAgQIAAAQIEigkIgMUaXLkECBAgQIAAAQFQHyBAgAABAgQIFBMQAIs1uHIJECBAgAABAgKgPkCAAAECBAgQKCYgABZrcOUSIECAAAECBARAfYAAAQIECBAgUExAACzW4MolQIAAAQIECAiA+gABAgQIECBAoJiAAFiswZVLgAABAgQIEBAA9QECBAgQIECAQDEBAbBYgyuXAAECBAgQICAA6gMECBAgQIAAgWICAmCxBlcuAQIECBAgQEAA1AcIECBAgAABAsUEBMBiDa5cAgQIECBAgIAAqA8QIECAAAECBIoJCIDFGly5BAgQIECAAAEBUB8gQIAAAQIECBQTEACLNbhyCRAgQIAAAQICoD5AgAABAgQIECgmIAAWa3DlEiBAgAABAgQEQH2AAAECBAgQIFBMQAAs1uDKJUCAAAECBAgIgPoAAQIECBAgQKCYgABYrMGVS4AAAQIECBAQAPUBAgQIECBAgEAxAQGwWIMrlwABAgQIECAgAOoDBAgQIECAAIFiAgJgsQZXLgECBAgQIEBAANQHCBAgQIAAAQLFBATAYg2uXAIECBAgQICAAKgPECBAgAABAgSKCQiAxRpcuQQIECBAgAABAVAfIECAAAECBAgUExAAizW4cgkQIECAAAECAqA+QIAAAQIECBAoJiAAFmtw5RIgQIAAAQIEBEB9gAABAgQIECBQTEAALNbgyiVAgAABAgQICID6AAECBAgQIECgmIAAWKzBlUuAAAECBAgQEAD1AQIECBAgQIBAMQEBsFiDK5cAAQIECBAgIADqAwQIECBAgACBYgICYLEGVy4BAgQIECBAQADUBwgQIECAAAECxQQEwGINrlwCBAgQIECAgACoDxAgQIAAAQIEigkIgMUaXLkECBAgQIAAAQFQHyBAgAABAgQIFBMQAIs1uHIJECBAgAABAgKgPkCAAAECBAgQKCYgABZrcOUSIECAAAECBARAfYAAAQIECBAgUExAACzW4MolQIAAAQIECAiA+gABAgQIECBAoJiAAFiswZVLgAABAgQIEBAA9QECBAgQIECAQDEBAbBYgyuXAAECBAgQICAA6gMECBAgQIAAgWICAmCxBlcuAQIECBAgQEAA1AcIECBAgAABAsUEBMBiDa5cAgQIECBAgIAAqA8QIECAAAECBIoJCIDFGly5BAgQIECAAAEBUB8gQIAAAQIECBQTEACLNbhyCRAgQIAAAQICoD5AgAABAgQIECgmIAAWa3DlEiBAgAABAgQEQH2AAAECBAgQIFBMQAAs1uDKJUCAAAECBAgIgPoAAQIECBAgQKCYgABYrMGVS4AAAQIECBAQAPUBAgQIECBAgEAxAQGwWIMrlwABAgQIECAgAOoDBAgQIECAAIFiAgJgsQZXLgECBAgQIEBAANQHCBAgQIAAAQLFBATAYg2uXAIECBAgQICAAKgPECBAgAABAgSKCQiAxRpcuQQIECBAgAABAVAfIECAAAECBAgUExAAizW4cgkQIECAAAECAqA+QIAAAQIECBAoJiAAFmtw5RIgQIAAAQIEBEB9gAABAgQIECBQTEAALNbgyiVAgAABAgQICID6AAECBAgQIECgmIAAWKzBlUuAAAECBAgQEAD1AQIECBAgQIBAMQEBsFiDK5cAAQIECBAgIADqAwQIECBAgACBYgICYLEGVy4BAgQIECBAQADUBwgQIECAAAECxQQEwGINrlwCBAgQIECAgACoDxAgQIAAAQIEigkIgMUaXLkECBAgQIAAAQFQHyBAgAABAgQIFBMQAIs1uHIJECBAgAABAgKgPkCAAAECBAgQKCYgABZrcOUSIECAAAECBARAfYAAAQIECBAgUExAACzW4MolQIAAAQIECAiA+gABAgQIECBAoJiAAFiswZVLgAABAgQIEBAA9QECBAgQIECAQDEBAbBYgyuXAAECBAgQICAA6gMECBAgQIAAgWICAmCxBlcuAQIECBAgQEAA1AcIECBAgAABAsUEBMBiDa5cAgQIECBAgIAAqA8QIECAAAECBIoJCIDFGly5BAgQIECAAAEBUB8gQIAAAQIECBQTEACLNbhyCRAgQIAAAQICoD5AgAABAgQIECgmIAAWa3DlEiBAgAABAgQEQH2AAAECBAgQIFBMQAAs1uDKJUCAAAECBAgIgPoAAQIECBAgQKCYgABYrMGVS4AAAQIECBAQAPUBAgQIECBAgEAxAQGwWIMrlwABAgQIECAgAOoDBAgQIECAAIFiAgJgsQZXLgECBAgQIEBAANQHCBAgQIAAAQLFBATAYg2uXAIECBAgQICAAKgPECBAgAABAgSKCQiAxRpcuQQIECBAgAABAVAfIECAAAECBAgUExAAizW4cgkQIECAAAECAqA+QIAAAQIECBAoJiAAFmtw5RIgQIAAAQIEBEB9gAABAgQIECBQTEAALNbgyiVAgAABAgQICID6AAECBAgQIECgmIAAWKzBlUuAAAECBAgQEAD1AQIECBAgQIBAMQEBsFiDK5cAAQIECBAgIADqAwQIECBAgACBYgICYLEGVy4BAgQIECBAQADUBwgQIECAAAECxQQEwGINrlwCBAgQIECAgACoDxAgQIAAAQIEigkIgMUaXLkECBAgQIAAAQFQHyBAgAABAgQIFBMQAIs1uHIJECBAgAABAgKgPkCAAAECBAgQKCYgABZrcOUSIECAAAECBARAfYAAAQIECBAgUExAACzW4MolQIAAAQIECAiA+gABAgQIECBAoJiAAFiswZVLgAABAgQIEBAA9QECBAgQIECAQDEBAbBYgyuXAAECBAgQICAA6gMECBAgQIAAgWICAmCxBlcuAQIECBAgQEAA1AcIECBAgAABAsUEBMBiDa5cAgQIECBAgIAAqA8QIECAAAECBIoJCIDFGly5BAgQIECAAAEBUB8gQIAAAQIECBQTEACLNbhyCRAgQIAAAQICoD5AgAABAgQIECgmIAAWa3DlEiBAgAABAgQEQH2AAAECBAgQIFBMQAAs1uDKJUCAAAECBAgIgPoAAQIECBAgQKCYgABYrMGVS4AAAQIECBAQAPUBAgQIECBA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AQGwXpurmAABAgQIECguIAAW7wDKJ0CAAAECBOoJCID12lzFBAgQIECAQHEBAbB4B1A+AQIECBAgUE9AAKzX5iomQIAAAQIEigsIgMU7gPIJECBAgACBegICYL02VzEBAgQIECBQXEAALN4BlE+AAAECBAjUExAA67W5igkQIECAAIHiAgJg8Q6gfAIECBAgQKCegABYr81VTIAAAQIECBQXEACLdwDlEyBAgAABAvUEBMB6ba5iAgQIECBAoLiAAFi8AyifAAECBAgQqCcgANZrcxUTIECAAAECxQUEwOIdQPkECBAgQIBAPQEBsF6bq5gAAQIECBAoLiAAFu8AyidAgAABAgTqCQiA9dpcxQQIECBAgEBxAQGweAdQPgECBAgQIFBPQACs1+YqJkCAAAECBIoLCIDFO4DyCRAgQIAAgXoCAmC9NlcxAQIECBAgUFxAACzeAZRPgAABAgQI1BMQAOu1uYoJECBAgACB4gICYPEOoHwCBAgQIECgnoAAWK/NVUyAAAECBAgUFxAAi3cA5RMgQIAAAQL1BATAem2uYgIECBAgQKC4gABYvAMonwABAgQIEKgnIADWa3MVEyBAgAABAsUFBMDiHUD5BAgQIECAQD0BAbBem6uYAAECBAgQKC4gABbvAMonQIAAAQIE6gkIgPXaXMUECBAgQIBAcQEBsHgHUD4BAgQIECBQT0AArNfmKiZAgAABAgSKCwiAxTuA8gkQIECAAIF6AgJgvTZXMQECBAgQIFBcQAAs3gGUT4AAAQIECNQTEADrtbmKCRAgQIAAgeICAmDxDqB8AgQIECBAoJ6AAFivzVVMgAABAgQIFBcQAIt3AOUTIECAAAEC9QQEwHptrmICBAgQIECguIAAWLwDKJ8AAQIECBCoJyAA1mtzFRMgQIAAAQLFBQTA4h1A+QQIECBAgEA9AQGwXpurmAABAgQIECguIAAW7wDKJ0CAAAECBOoJCID12lzFBAgQIECAQHEBAbB4B1A+AQIECBAgUE9AAKzX5iomQIAAAQIEigsIgMU7gPIJECBAgACBegICYL02VzEBAgQIECBQXEAALN4BlE+AAAECBAjUExAA67W5igkQIECAAIHiAgJg8Q6gfAIECBAgQKCegABYr81VTIAAAQIECBQXEACLdwDlEyBAgAABAvUEBMB6ba5iAgQIECBAoLiAAFi8AyifAAECBAgQqCcgANZrcxUTIECAAAECxQUEwOIdQPkECBAgQIBAPQEBsF6bq5gAAQIECBAoLiAAFu8AyidAgAABAgTqCfx/7LwwNoiRs90AAAAASUVORK5CYII=";
    //支持裁减大小宽度
    protected  $accept_size = array(60,120,150,160,180,200,240,360,480,640,720,960);

    public function returnDefaultImage(){
//        header("Content-type:image/png");
        header('Content-Type:image/png');
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
//      图片缓存设置
        $time = filemtime($url);
        $dt =date("D, d M Y H:m:s GMT", $time );
        header("Last-Modified: $dt");
        header("Cache-Control: max-age=844000");
//        header('Content-type: image/'.$result['ext']);
        header('Content-Type: image/'.$result['ext']);
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE']==$dt) {
            header("HTTP/1.0 304 Not Modified");
            exit;
        }

        $image = @readfile($url);
        if ($image == false) {
            $this->returnDefaultImage();
        }
        echo $image;
        exit();

    }



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
        //
//        $thumbnail = M('Thumbnail');
//
//        $result = $thumbnail->where(array('width'=>$size,'pic_id'=>$info['id']))->find();
//
//        if(!empty($result)){
//            return $result['url'];
//        }



        $image = new Image();

        if(!file_exists(realpath('.'.$info['path']))){
            return false;
        }

        $image->open( realpath('.'.$info['path']));

        if(!is_dir(($thumbnail_path))){
            if(!mkdir(($thumbnail_path))){
                return false;
            }
        }

        $result = $image->thumb($size, $size,Image::IMAGE_THUMB_FILLED)->save($relative_path, null, 100);

        if(!file_exists(realpath($relative_path))){
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