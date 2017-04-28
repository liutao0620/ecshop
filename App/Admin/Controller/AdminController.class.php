<?php 
namespace Admin\Controller;
use Think\Controller;
class AdminController extends MyController
{
	public function add()
	{
		if(IS_POST){
        	$adminmodel = D('Admin');
        	if($adminmodel->create()){
        		$salt=substr(uniqid(),-6);
        		$pwd=I('post.password');
        		$adminmodel->password=md5(md5($pwd).$salt);
        		$adminmodel->salt=$salt;
                if($adminmodel->add()){
                	$this->success('添加成功',U('lst'));
                	exit;
                }else{
                	$this->error('添加失败');
                }

        	}
        	$this->error($adminmodel->getError());
        	
        }
		$rolemodel=D('Role');
		$roledata = $rolemodel->select();
		$this->assign('roledata',$roledata);
		$this->display();
	}
	public function lst()
	{
		$adminmodel = D('Admin');
		$admindata=$adminmodel->field("a.*,c.role_name")->join("a left join it_admin_role b on a.id=b.admin_id left join it_role c on b.role_id=c.id")->where("a.id!=1")->select();
		$this->assign('admindata',$admindata);
		$this->display();
	}
	public function update()
	{
		$adminmodel = D('Admin');
		if(IS_POST){
			if($adminmodel->create()){
				$pwd=I('post.password');
				if(!empty($pwd)){
                    $salt=substr(uniqid(),-6);
	        		$pwd=I('post.password');
	        		$adminmodel->password=md5(md5($pwd).$salt);
	        		$adminmodel->salt=$salt; 
				}else{
					unset($adminmodel->password);
				}
				if($adminmodel->save()!==false){
                   $this->success('修改成功',U('lst'));exit;
				}else{
                   $this->error('修改失败');
				}
			}else{
               $this->error($adminmodel->getError());
			}
		}
		$id=I('get.id')+0;
		if($id == 1){
			$this->error('参数错误');
		}
		
		$info = $adminmodel->field("a.*,b.role_id")->join("a left join it_admin_role b on a.id=b.admin_id")->where("id=$id")->find();
		$this->assign('info',$info);

        $rolemodel=D('Role');
		$roledata = $rolemodel->select();
		$this->assign('roledata',$roledata);
		$this->display();

	}
	public function delete()
	{
		$id=I('get.id')+0;
		if($id == 1){
			$this->error('参数错误');
		}
		$adminmodel = D('Admin');
		$info = $adminmodel->delete($id);
		if($info !== false){
			$this->success('删除成功',U('lst'));
		}else{
			$this->error('删除失败');
		}

	}
}

?>