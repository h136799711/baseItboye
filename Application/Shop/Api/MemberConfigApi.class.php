<?php
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\MemberConfigModel;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/11
 * Time: 11:43
 */
class MemberConfigApi extends Api{

    const COUNT="Shop/MemberConfig/count";

    const ADD="Shop/MemberConfig/add";
	
	const QUERY="Shop/MemberConfig/query";

    const SAVE_BY_ID="Shop/MemberConfig/saveByID";
	
	const SAVE="Shop/MemberConfig/save";

    const QUERY_NO_PAGING="Shop/MemberConfig/queryNoPaging";

    const GET_INFO = "Shop/MemberConfig/getInfo";

    const SET_INC="Shop/MemberConfig/setInc";

    protected function _init(){
        $this->model=new MemberConfigModel();
    }

    /**
     * 根据id保存数据
     */
    public function saveByID($ID, $entity) {
        unset($entity['uid']);

        return $this -> save(array('uid' => $ID), $entity);
    }

}