<?php 
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller
{
	public function login()
	{
        if(IS_POST){
        	$adminmodel = D('Admin');
        	if($adminmodel->validate($adminmodel->_login_validate)->create()){
                if($adminmodel->login()){
                	$this->success('登录成功',U('Index/index'));
                	exit;
                }

        	}
        		$this->error($adminmodel->getError());
        	
        }
		$this->display();
	}
	public function authcode()
	{
		$config =    array(    
							'fontSize'    =>    20,    // 验证码字体大小    
							'length'      =>    4,     // 验证码位数    
							'useNoise'    =>    false, // 关闭验证码杂点
							'imageW'      =>    140,
							'imageH'      =>    40,
		                  );
		$Verify =     new \Think\Verify($config);
		$Verify->entry();
	}
}

 ?>