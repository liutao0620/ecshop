<?php 
namespace Admin\Controller;
use Think\Controller;
class RoleController extends MyController
{
	public function add()
	{
		$rolemodel=D('Role');
        if(IS_POST){
            if($rolemodel->create()){
            	if($rolemodel->add()){
                    $this->success('添加成功',U('lst'));
                    exit;
            	}else{
            		$this->error('添加失败');
            	}
            }else{
            	$this->error($rolemodel->getError());
            }
		}

		$privmodel=D('Privilege');
		$privdata=$privmodel->getTree();
		$this->assign('privdata',$privdata);
		$this->display();
	}
    public function lst()
    {
        $rolemodel=D('Role');
        $roledata=$rolemodel->field("a.*,group_concat(c.priv_name) as privnames")->join("a left join it_role_privilege b on a.id=b.role_id left join it_privilege c on b.priv_id=c.id")->group("a.id")->select();        
        $this->assign('roledata',$roledata);
        $this->display();
    }
    public function delete(){
                //接收传递的角色id
                $role_id  = $_GET['id']+0;
                //判断该角色是否有管理员
                //思路：查询it_admin_role表，条件就是role_id
               $info = M('AdminRole')->where("role_id=$role_id")->find();
               if($info){
                    $this->error('该角色有管理员不能被删除');
               }
               $rolemodel = D('Role');
               if($rolemodel->delete($role_id)!==false){
                            $this->success('删除角色成功',U('lst'));exit;
               }else{
                            $this->error('删除角色失败');
               }
         }
}

 ?>