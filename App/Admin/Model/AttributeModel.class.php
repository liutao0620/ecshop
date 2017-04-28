<?php 
namespace Admin\Model;
use Think\Model;
class AttributeModel extends Model
{
	protected $_validate=array(
        array('attr_name','require','属性名称不能为空'),
        array('type_id','isGt0','商品类型不合法',1,'callback'),
        array('attr_type',array(0,1),'属性类型不合法',1,'in',),
        array('attr_input_type',array(0,1),'属性录入方式不合法',1,'in',),
		);
	protected function isGt0()
	{
		$type_id=I('post.type_id')+0;
        if($type_id>0){
        	return true;
        }else{
        	return false;
        }
	}
}

 ?>