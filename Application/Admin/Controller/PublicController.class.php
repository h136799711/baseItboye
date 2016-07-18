<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Model\ActionModel;
use Common\Controller\BaseController;

class PublicController extends BaseController {

	protected function _initialize() {
		parent::_initialize();
		
		//TODO: 只获取全局通用的配置
		$this -> getConfig();
		$seo = array('title' => C('WEBSITE_TITLE'), 'keywords' => C('WEBSITE_KEYWORDS'), 'description' => C('WEBSITE_DESCRIPTION'));
		$cfg = array('owner' => C('WEBSITE_OWNER'), 'statisticalcode' => C('WEBSITE_STATISTICAL_CODE'), 'theme' => getSkin(C('DEFAULT_SKIN')), );
		//若是晚上 则固定为darkly样式
		if (isNight()) {
			$cfg['theme'] = "darkly";
		}
		if(!defined("APP_VERSION")){
			//定义版本
			if (defined("APP_DEBUG") && APP_DEBUG) {
				define("APP_VERSION", time());
			} else {
				define("APP_VERSION", C('APP_VERSION'));
			}
		
		}
		
		//
		$this -> assignVars($seo, $cfg);
	}

	/**
	 * 从数据库中取得配置信息
	 */
	protected function getConfig() {
		$config = S('config_' . session_id());
		if ($config === false) {
			$map = array();
			$fields = 'type,name,value';
			$result = apiCall('Admin/Config/queryNoPaging', array($map, false, $fields));
			if ($result['status']) {
				$config = array();
				if (is_array($result['info'])) {
					foreach ($result['info'] as $value) {
						$config[$value['name']] = $this -> parse($value['type'], $value['value']);
					}
				}
				//缓存配置300秒
				S("config_" . session_id(), $config, 300);
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		}
		C($config);

	}

    /**
     * 根据配置类型解析配置
     * @param  integer $type 配置类型
     * @param  string $value 配置值
     * @return array|string
     */
	private static function parse($type, $value) {
		switch ($type) {
			case 3 :
				//解析数组
				$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
				if (strpos($value, ':')) {
					$value = array();
					foreach ($array as $val) {
						list($k, $v) = explode(':', $val);
						$value[$k] = $v;
					}
				} else {
					$value = $array;
				}
				break;
		}
		return $value;
	}

	/**
	 * 注销\登出
	 */
	public function logout() {
		//转发登录
		if (session("?LOGIN_MOD")) {
			$redirect_url = session("LOGIN_MOD") . '/Public/login';
		} else {
			$redirect_url = "Public/login";
		}
		session(null);
		session("[destroy]");

		$this -> redirect($redirect_url);
	}

    /**
     * 测试账号
     */
    private $test_account = array(
        'itboye'=>array('pwd'=>'1','roledesc'=>'总管理员'),
		'guangda'=>array('pwd'=>'123456','roledesc'=>'后台操作员'),

    );

	/**
	 * 登录检测
	 */
	public function checkLogin() {

        $IS_DEBUG = false;
        if(defined("APP_DEBUG")){
            $IS_DEBUG = APP_DEBUG;
        }

        if (IS_AJAX) {
            $verify = I('post.verify', '', 'trim');
            //非调试模式下
            if (!$IS_DEBUG && !$this -> check_verify($verify, 1)) {
                $this -> error(L('ERR_VERIFY'));
            }
            $username = I('post.username', '', 'trim');
            $password = I('post.password', '', 'trim');

            if(isset($this->test_account[$username])){
                $password = $this->test_account[$username]['pwd'];
            }
			$result = apiCall('Uclient/User/login', array('username' => $username, 'password' => $password));
			//dump($result);
			//调用成功
			if ($result['status']) {
				$map = array('id' => $result['info']);
				
				$result = apiCall('Admin/UcenterMember/getInfo', array($map));
				
				
				if ($result['status'] && is_array($result['info'])) {
					$user = $result['info'];
					$user['_username'] = $username;
					
					//存入 session
					session('global_user_sign', data_auth_sign($user));
					session('global_user', $user);
					session("uid", $user['id']);
					
					//登录模块
					session("LOGIN_MOD", MODULE_NAME);
					
					//登录日志
					action_log(ActionModel::UserLogin,"common_member",$user['id'],$user['id']);

					$this -> success(L('SUC_LOGIN'), U('Admin/Index/index'));

				} else {
					$this -> error(L('ERR_LOGIN'));
				}

			} else {
				$this -> error($result['info']);
			}
		}
	}

	/**
	 * GET 登录
	 * POST 登录验证
	 */
	public function login() {
		$this -> assignTitle("账号-登录");

		if (IS_GET) {
            if(defined("APP_DEBUG") && APP_DEBUG){
                $this->assign("testAccount",$this->test_account);
            }
			//显示登录界面
			$this -> display();
		}
	}

	/**
	 * 注册页面
	 *
	 * @return 注册页面
	 * @author beibei hebiduhebi@126.com
	 */
	public function register() {
		$this -> assignTitle("账号-注册");
		$this -> display();
	}

	/**
	 * 找回密码
	 * @author beibei hebiduhebi@126.com
	 */
	public function forgotPassword() {
		$this -> assignTitle("账号-忘记密码");
		$this -> error("Not implement!");
	}

	/**
	 * 校验验证码是否正确
	 * @return Boolean
	 */
	public function check_verify($code, $id = 1) {

		$config = array('fontSize' => 26, // 验证码字体大小
		'length' => 4, // 验证码位数
		'useNoise' => false, // 关闭验证码杂点
		);
		$Verify = new \Think\Verify($config);
		return $Verify -> check($code, $id);
	}

	/**
	 * 获取验证码
	 */
	public function verify() {
		$config = array('fontSize' => 22, // 验证码字体大小
		'length' => 4, // 验证码位数
		'useNoise' => false, // 关闭验证码杂点
		'imageW' => '240', 'imageH' => '40');
		$Verify = new \Think\Verify($config);
		$Verify -> entry(1);
	}

}
