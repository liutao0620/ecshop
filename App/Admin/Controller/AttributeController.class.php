<?php 
namespace Admin\Controller;
use Think\Controller;
class AttributeController extends MyController
{
    public function add()
    {
        if(IS_POST){
			$attrmodel=D('Attribute');			
			if($attrmodel->create()){
			    $type_id=I('post.type_id')+0;				
                if($attrmodel->add()){
                	$this->success('添加成功',U('lst',array('type_id'=>$type_id)));
                	exit;
                }else{
                	$this->error('添加失败');
                }
			}else{
				$this->error($attrmodel->getError());
			}
		}
        $typemodel=D('Type');
		$typedata = $typemodel->select();
        $this->assign('typedata',$typedata);
		$this->display();
    }

    public function lst()
    {
    	$type_id=I('get.type_id')+0;
    	if($type_id==0){
            $where = 1;
    	}else{
    		$where="a.type_id=$type_id";
    	}
    	$typemodel=D('Type');
		$typedata = $typemodel->select();
		$attrmodel=D('Attribute');
		$count =$attrmodel->join("a  left join it_type b on a.type_id=b.id")->where($where)->count();
		$Page =new \Think\Page($count,10);
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$show =$Page->show();
		$attrdata=$attrmodel->field("a.*,b.type_name")->join("a left join it_type b on a.type_id=b.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('show',$show);
		$this->assign('type_id',$type_id);
		$this->assign('attrdata',$attrdata);
        $this->assign('typedata',$typedata);
		$this->display();
    }
}
 ?>
