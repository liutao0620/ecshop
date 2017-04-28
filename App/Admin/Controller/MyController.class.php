<?php 
namespace Admin\Controller;
use Think\Controller;
class MyController extends Controller
{
	public function _initialize()
	{
		$url = MODULE_NAME.'/'. CONTROLLER_NAME.'/'. ACTION_NAME;
		$admin_id=$_SESSION['admin_id'];

		if($admin_id>0){
			if($admin_id==1){
				return true;
			}
			if(CONTROLLER_NAME=='Index'){
				return true;
			}
			$sql = "select concat(module_name,'/',controller_name,'/',action_name) url from it_admin_role a left join it_role_privilege b on a.role_id=b.role_id left join it_privilege c on b.priv_id=c.id where a.admin_id=$admin_id having url='$url'";
			    $info = M()->query($sql);
			    if($info){
			    	return true;
			    }else{
			    	$this->success('您没有权限！',U('index/index'));exit;
			    }
		}else{
			$this->success('必须要登录',U('Login/login'));
			exit;
		}
	}
}

 ?>