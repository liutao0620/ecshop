<?php 
namespace Admin\Controller;
use Think\Controller;
class CategoryController extends MyController
{
	public function add()
	{
		$catemodel = D('Category');
        if(IS_POST){
                if($catemodel->create()){
                        if($catemodel->add()){
                                //添加成功
                                $this->success('添加成功',U('lst'));exit;
                        }else{
                                $this->error('添加失败');
                        }
                }else{
                        $this->error($catemodel->getError());
                }
        }
        $catedata = $catemodel->getTree();
        $this->assign('catedata',$catedata);
        $this->display();
	}

	public function lst()
	{
		$catemodel=D('Category');
		$catedata=$catemodel->getTree();
		$this->assign('catedata',$catedata);
        $this->display();
	}

	public function del()
	{
		$cat_id=I('get.cat_id')+0;
		$catemodel=D('Category');
		$info = $catemodel->where("parent_id=$cat_id")->select();
		if($info){
                        //说明有子栏目，
                        $this->error('该栏目下面有子栏目，不能被删除');
                }
                //要开始删除栏目。
                //$catemodel->delete返回的值是受影响的行数
        if($catemodel->delete($cat_id)!==false){
                    $this->success('删除成功',U('lst'));
        }else{
                $this->error('删除失败');
        }
	}

	public function update()
	{
      //取出当前栏目数据
      $catemodel  = D('Category');
      if(IS_POST){
            if($catemodel->create()){
                //$catemodel->save()返回受影响的行数
                //还要判断提交的父栏目的id是否在自己的子孙栏目里面。
                $parent_id  = I('post.parent_id');
                $id = I('post.id');
                //查找自己的子孙了栏目的id
                $ids = $catemodel->getChildId($id);
              //把自己的id 也添加到该数组里面来
                $ids[]=$id;
                if(in_array($parent_id,$ids)){
                        $this->error('不能把自己的子孙栏目当成自己的父栏目');
                }
                if($catemodel->save()!==false){
                        $this->success('修改成功',U('lst'));exit;
                }else{
                        $this->error('修改失败');
                }
            }else{
                    $this->error($catemodel->getError());
            }
      }
              //接收传递的栏目id
              $cat_id = $_GET['cat_id']+0;
              $info =  $catemodel->find($cat_id);
              $this->assign('info',$info);
           
              //取出当前栏目的子孙栏目
              $ids = $catemodel->getChildId($cat_id);
              //把自己的id 也添加到该数组里面来
              $ids[]=$cat_id;
              $this->assign('ids',$ids);
              //取出所有的栏目数据
              $catemodel = D('Category');
              $catedata = $catemodel->getTree();
              $this->assign('catedata',$catedata);
              $this->display();
        }
}

 ?>