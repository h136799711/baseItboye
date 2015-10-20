<?php
namespace Test\Controller;

use Think\Controller;
use Api\Service\OAuth2ClientService;


/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/11
 * Time: 17:44
 */
class TestController extends  Controller
{
    public function __construct(){
        parent::__construct();

        header("X-AUTHOR:ITBOYE.COM");
        var_dump(C('SHOW_PAGE_TRACE'));
        $client_id = C('CLIENT_ID');
        $client_secret = C('CLIENT_SECRET');
        $config = array(
            'client_id'=>$client_id,
            'client_secret'=>$client_secret,
            'site_url'=>C("SITE_URL"),
        );

        $client = new OAuth2ClientService($config);
        $access_token = $client->getAccessToken();
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']);
        }
        $this->assign("error",$access_token);
    }

    public function index()
    {
        //$id=cookie('name');
        /*$a=array(
            array('id'=>'hehe','name'=>'wacao'),
            array('id'=>'wowo','name'=>'yeye'),
        );
        $arr_str = serialize($a);
        cookie("a",$arr_str,24*3600);
        $b=cookie('a');
        $arr = unserialize($b);
        dump($arr);*/

        /**$dd="1321323231321321";
        dump(substr($dd,strlen($dd)-6));*/

        /*$i=0;
        for(;1==1;){

            $i++;
            if($i>100){
                return;
            }
        }*/



        //echo session($_POST['mail']);
        //$this->display();
        /*$map=array(
            'code'=>array('like','%000000'),
        );
        $result1=apiCall(ParcelNumberApi::QUERY_NO_PAGING,array($map));
        $map=array(
           //'level'=>3,
        );*/
       // $result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
        /*$j=0;
        for($i=0;$i<count($result1['info']);$i++){

           // array(array('neq',6),array('gt',3),'and');
            $map=array(
                'code'=>array(array('like',substr($result1['info'][$i]['code'],0,6).'%'),array('neq',$result1['info'][$i]['code'])),
            );
            $result2=apiCall(ParcelNumberApi::QUERY_NO_PAGING,array($map));
            $result1['info'][$i]['list']=$result2['info'];

            foreach($result2['info'] as $l){

                $list[$j]['code']=$l['code'];
                $list[$j]['parent']=$result1['info'][$i]['id'];
                $list[$j]['scope']=$l['scope'];
                $list[$j]['name']=$l['type'];
                $list[$j]['level']=4;
                $list[$j]['taxRate']=$l['taxrate'];
                $j++;
            }

        }*/
       // dump($result1);
        //dump($list);
        /*foreach($list as $l){
            apiCall(CategoryApi::ADD,array($l));
        }*/


        /*$map=array(
            'id'=>array('gt',413),
        );
        $result1=apiCall(ParcelNumberApi::QUERY_NO_PAGING,array($map));
        for($i=0;$i<count($result1['info']);$i++){
            $list[$i]['code']=$result1['info'][$i]['code'];

            $list[$i]['scope']=$result1['info'][$i]['scope'];
            $list[$i]['name']=$result1['info'][$i]['type'];

            $list[$i]['taxRate']=$result1['info'][$i]['taxrate'];

            $list[$i]['level']=2;
            if($result1['info'][$i]['id']==414){
                $list[$i]['parent']=16;
            }elseif($result1['info'][$i]['id']==415){
                $list[$i]['parent']=20;
            }elseif($result1['info'][$i]['id']==416){
                $list[$i]['parent']=26;
            }elseif($result1['info'][$i]['id']==417){
                $list[$i]['parent']=34;
            }

        }
        //dump($result1);
       // dump($list);
        foreach($list as $l){
            apiCall(CategoryApi::ADD,array($l));
        }*/




    }

    //�ϴ�����
    public function upload()
    {
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload();// ʵ���ϴ���
        $upload->maxSize = 3145728;// ���ø����ϴ���С
        $upload->exts = array('xls', 'xlsx');// ���ø����ϴ���
        $upload->savePath = '/'; // ���ø����ϴ�Ŀ¼
        // �ϴ��ļ�
        $info = $upload->uploadOne($_FILES['excelData']);
        $filename = './Uploads' . $info['savepath'] . $info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if (!$info) {// �ϴ�������ʾ������Ϣ
            $this->error($upload->getError());
        } else {// �ϴ��ɹ�
            $this->goods_import($filename, $exts);
        }
    }

    //�������ҳ��
    public function import()
    {
        $this->display('goods_import');
    }

    //������ݷ���
    protected function goods_import($filename, $exts = 'xls')
    {
        //����PHPExcel��⣬��ΪPHPExcelû��������ռ䣬ֻ��inport����
        import("Org.Util.PHPExcel");
        //����PHPExcel����ע�⣬��������\
        $PHPExcel = new \PHPExcel();
        //���excel�ļ���׺��Ϊ.xls�����������
        if ($exts == 'xls') {
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }


        //�����ļ�
        $PHPExcel = $PHPReader->load($filename);
        //��ȡ���еĵ�һ�������?���Ҫ��ȡ�ڶ�������0��Ϊ1����������
        $currentSheet = $PHPExcel->getSheet(10);
        //��ȡ������
        $allColumn = $currentSheet->getHighestColumn();
        //��ȡ������
        $allRow = $currentSheet->getHighestRow();
        // dump("???");
        $i = 0;
        //ѭ����ȡ���е���ݣ�$currentRow��ʾ��ǰ�У������п�ʼ��ȡ��ݣ�����ֵ��0��ʼ
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            //�����п�ʼ��A��ʾ��һ��
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                //������
                $address = $currentColumn . $currentRow;
                //��ȡ������ݣ����浽����$arr��

                //��ȡ������ݣ����浽����$arr��
                if ($currentColumn == 'A') {
                    $data[$i]['code'] = $currentSheet->getCell($address)->getValue();
                } else if ($currentColumn == 'B') {
                    $data[$i]['type'] = (string)$currentSheet->getCell($address)->getValue();
                } else if ($currentColumn == 'C') {
                    $data[$i]['scope'] = (string)$currentSheet->getCell($address)->getValue();
                } else if ($currentColumn == 'D') {
                    $data[$i]['taxRate'] = $currentSheet->getCell($address)->getValue();
                }
            }
            $i++;

        }


        //dump($data);
        /*set_time_limit(1800);
        foreach ($data as $d) {
            apiCall(ParcelNumberApi::ADD, array($d));
        }
        dump("����ɹ�");*/
        //  $this->save_import($data);
    }


    //���浼�����
    public function save_import($data)
    {
        /*
         * ������ֵ��� 7673
         * */
//  	$a=189;
//		$dataList=array();
//      foreach ($data as $k=>$v){
//      	$a=$a+1;
//      	if($v['L']!=null){
//				if($v['V']==null){
////				    $dataList[] = array('problem_id'=>($v['A'])+2,'evaluation_id'=>($v['B']+1),'title'=>$v['D'],'hidden_value'=>"",'create_time'=>time());
////					 $dataList[] = array('problem_id'=>($v['A'])+2,'evaluation_id'=>($v['B']+1),'title'=>$v['E'],'hidden_value'=>"",'create_time'=>time());
//					  $dataList[] = array('problem_id'=>$a,'evaluation_id'=>($v['A']+1),'title'=>$v['L'],'hidden_value'=>"",'explain'=>"",'create_time'=>time(),'sort'=>10);
//				}else{
////					 $dataList[] = array('problem_id'=>($v['A'])+2,'evaluation_id'=>($v['B']+1),'title'=>$v['D'],'hidden_value'=>$v['N'],'create_time'=>time());
//					 $dataList[] = array('problem_id'=>$a,'evaluation_id'=>($v['A']+1),'title'=>$v['L'],'hidden_value'=>$v['V'],'explain'=>"",'create_time'=>time(),'sort'=>10);
//				}
//			}
////
//
//      }
////		dump($dataList);
//
//		$result = M('eval_answer')->addAll($dataList);
//		M('eval_answer')->where('evaluation_id=13')->save($dataList);
//      if($result){
//          $this->success('��Ʒ����ɹ�', 'Index/index');
//      }else{
//          $this->error('��Ʒ����ʧ��');
//      }

        /*
         * �������
         * */
//		$dataList=array();
//      foreach ($data as $k=>$v){
////			if($v['B']==null){
//			 $dataList[] = array('name'=>$v['B'],'sort'=>0);
////			}else{
////			 $dataList[] = array('evaluation_id'=>($v['A'])+1,'title'=>$v['B'],'create_time'=>time(),'desc'=>'','sort'=>$v['C']);
////			}
//      }
//		$result = M('datatree')->addAll($dataList);
//		dump($dataList);
//      if($result){
//          $this->success('��Ʒ����ɹ�', 'Index/index');
//      }else{
//          $this->error('��Ʒ����ʧ��');
//      }

        /*
         * �������
         * */
//      $add_time = date('Y-m-d H:i:s', time());
        $a = 2;
        foreach ($data as $k => $v) {
            $a = $a + 1;
            $date['id'] = $a;
            $date['type'] = $v['C'];
            $date['title'] = $v['B'];
            $date['desc'] = '';
            $date['user_id'] = 34;
            $date['create_time'] = time();
            $date['update_time'] = time();
            $date['roles'] = "";
            $date['parentid'] = $v['E'] + 2;
//			dump($date);dump($a);
            $result = M('evaluation')->save($date);
        }
//      if($result){
//          $this->success('��Ʒ����ɹ�', 'Index/index');
//      }else{
//          $this->error('��Ʒ����ʧ��');
//      }

    }

    //������ݷ���
    public function goods_export()
    {
        $goods_list = M('user')->select();
        //print_r($goods_list);exit;
        $data = array();
        foreach ($goods_list as $k => $goods_info) {
            $data[$k][name] = $goods_info['name'];
            $data[$k][type] = $goods_info['type'];
            $data[$k][isNull] = $goods_info['isNull'];
            $data[$k][fault] = $goods_info['default'];
            $data[$k][remore] = $goods_info['remore'];
            $data[$k][aa] = $goods_info['aa'];
        }
        foreach ($data as $field => $v) {
            if ($field == 'name') {
                $headArr[] = '���';
            }

            if ($field == 'type') {
                $headArr[] = '����';
            }

            if ($field == 'isNull') {
                $headArr[] = '�Ƿ�Ϊ��';
            }
            if ($field == 'default') {
                $headArr[] = 'Ĭ��';
            }
            if ($field == 'remore') {
                $headArr[] = '��ע';
            }
            if ($field == 'aa') {
                $headArr[] = 'aa';
            }
        }

        $filename = "goods_list";

        $this->getExcel($filename, $headArr, $data);
    }


    private function getExcel($fileName, $headArr, $data)
    {
        //����PHPExcel��⣬��ΪPHPExcelû��������ռ䣬ֻ��inport����
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y_m_d", time());
        $fileName .= "_{$date}.xls";

        //����PHPExcel����ע�⣬��������\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //���ñ�ͷ
        $key = ord("A");
        //print_r($headArr);exit;
        foreach ($headArr as $v) {
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        //print_r($data);exit;
        foreach ($data as $key => $rows) { //��д��
            $span = ord("A");
            foreach ($rows as $keyName => $value) {// ��д��
                $j = chr($span);
                $objActSheet->setCellValue($j . $column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //�������
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //���û��ָ���һ����,����Excel�����ǵ�һ����
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //�ļ�ͨ�����������
        exit;
    }

    public function sele()
    {
        $result = M('evaluation')->where('parentid' == 0)->select();
        $result1 = M('evaluation')->where('parentid' == 3)->select();
        $result2 = M('evaluation')->select();
        $this->assign('list', $result);
        $this->assign('list1', $result1);
        $this->assign('list2', $result2);
        $this->display();
    }

    public function test(){
        $this->display();
    }
}