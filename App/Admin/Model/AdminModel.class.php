<?php 
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model
{
	public $_login_validate = array(
        array('admin_name','require','管理员名称不能为空'), 
        array('password','require','密码不能为空'),
        array('checkcode','require','验证码不能为空'),

        array('checkcode','check_verify','验证码输入错误',1,'callback'), 
		);
	protected function check_verify($code, $id = '')
	{    
		$verify = new \Think\Verify();    
		return $verify->check($code, $id);
	}
	protected $_validate=array(
        array('admin_name','require','管理员名称不能为空'), 
        array('admin_name','','管理员名称已存在',1,'unique'),
        array('password','6,12','密码要在6到12位之间',1,'length',1),
        array('password','6,12','密码要在6到12位之间',2,'length',2),
        array('rpassword','password','两次密码不一致',2,'confirm'),
        array('role_id','number','要选择角色'),
		);
	public function login()
	{
		$admin_name=I('post.admin_name');
		$password=I('post.password');
		$info = $this->where("admin_name='$admin_name'")->find();
		if(!empty($info)){
			if($info['password']==md5(md5($password).$info['salt'])){
				$_SESSION['admin_name'] = $admin_name;
				$_SESSION['admin_id'] = $info['id'];
				return true;
			}
			
		}
        $this->error='用户名或密码错误';
		return false;
	}
	public function _after_insert($data,$options)
	{
		$role_id=I('post.role_id');
		$admin_id=$data['id'];
		M('AdminRole')->add(array(
            'role_id'=>$role_id,
            'admin_id'=>$admin_id,
			));
	}
	protected function _after_update($data,$options)
	{

		$admin_id=$options['where']['id'];
		$role_id=I('post.role_id');
        M('AdminRole')->where("admin_id=$admin_id")->delete();
        M('AdminRole')->add(array(
               'admin_id'=>$admin_id,
               'role_id'=>$role_id,
        	));
	}
	protected function _after_delete($data,$options)
    {
       $admin_id = $options['where']['id'];
       M("AdminRole")->where("admin_id=$admin_id")->delete();
    }
    //根据权限取出数据
    public function getButton()
    {
    	$admin_id=$_SESSION['admin_id'];
    	if($admin_id == 1){
    		//超级管理员
    		//取出顶级权限
    		$sql="select * from it_privilege where parent_id=0";
    		$data = M()->query($sql);
    		foreach($data as $v){
    			$v['child']=M()->query("select * from it_privilege where parent_id=".$v['id']);
    			$list[]=$v;
    		}
    	}else{
    		//普通管理员
    		$sql="select d.* from it_admin_role a left join it_role b on a.role_id= b.id left join it_role_privilege c on b.id=c.role_id left join it_privilege d on c.priv_id=d.id where d.parent_id=0 and a.admin_id=$admin_id";
    		$data = M()->query($sql);
    		foreach($data as $v){
    			$sql="select d.* from it_admin_role a left join it_role b on a.role_id= b.id left join it_role_privilege c on b.id=c.role_id left join it_privilege d on c.priv_id=d.id where d.parent_id=".$v['id']." and a.admin_id=$admin_id";
    			$v['child']=M()->query($sql);
    			$list[]=$v;
    		}
    	}
    	return $list;
    }

}

 ?>