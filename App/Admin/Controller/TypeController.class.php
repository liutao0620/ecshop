<?php 
namespace Admin\Controller;
use Think\Controller;
class TypeController extends MyController
{
	public function add()
	{
		if(IS_POST){
			$typemodel=D('Type');
			//create调用定义好的validate完成自动验证
			//在TypeModel里面定义允许提交的字段后 才嫩加上I('post.',1)来验证
			if($typemodel->create(I('post.'),1)){
				/*//判断是否有id字段提交
				if(isset($typemodel->id)){
					unset($typename->id);
				}*/
				
                if($typemodel->add()){
                	$this->success('添加成功',U('lst'));
                	exit;
                }else{
                	$this->error('添加失败');
                }
			}else{
				$this->error($typemodel->getError());
			}
		}
		$this->display();
	}
    //商品类型列表页面
	public function lst()
	{
		$typemodel=D('Type');
		$typedata = $typemodel->select();
        $this->assign('typedata',$typedata);
		$this->display();
	}
}

 ?>