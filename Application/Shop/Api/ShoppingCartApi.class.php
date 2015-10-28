<?php
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\ShoppingCartModel;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/20
 * Time: 17:38
 */
class ShoppingCartApi extends  Api{
    const ADD="Shop/ShoppingCart/add";

    const QUERY="Shop/ShoppingCart/query";
	
	const QUERY_NO_PAGING="Shop/ShoppingCart/queryNoPaging";

    const SAVE_BY_ID="Shop/ShoppingCart/saveById";

    const SAVE="Shop/ShoppingCart/save";

    const GET_INFO="Shop/ShoppingCart/getInfo";

    const DELETE="Shop/ShoppingCart/delete";

    protected  function _init(){
        $this->model=new ShoppingCartModel();
    }
}
