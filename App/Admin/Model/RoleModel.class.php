<?php 
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model
{
	protected $_validate=array(
             array('role_name','require','角色名称不能为空'),
		);
	protected function _after_insert($data,$options)
	{
        $role_id=$data['id'];
        $priv_ids=I('post.priv_id');
        foreach($priv_ids as $v){
            M('RolePrivilege')->add(array(
                'role_id'=>$role_id,
                'priv_id'=>$v,
            	));
        }
	}
    protected function _after_delete($data,$options)
    {
       // p($data);
       // p($options);exit;
       //删除it_role_privilege表里面的数据，条件就是$options['where']['id'];
       $role_id = $options['where']['id'];
       M("RolePrivilege")->where("role_id=$role_id")->delete();
    }
}

 ?>