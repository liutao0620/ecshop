<?php 
namespace Admin\Model;
use Think\Model;
class PrivilegeModel extends Model
{
	protected $_validate=array(
        array('priv_name','require','权限名称不能为空'),
        array('parent_id','require','上级权限不合法'),        
		);
	 public function getTree($id=0)
   {
      $arr=$this->select();
      return $this->_getTree($arr,$id=0);
   }

   public function _getTree($arr,$id=0,$lev=0)
   {
   	  static $list=array();
   	  foreach($arr as $v){
   	  	if($v['parent_id']==$id){
   	  		$v['lev'] = $lev;
   	  		$list[]=$v;
   	  		$this->_getTree($arr,$v['id'],$lev+1);
   	  	}
   	  }
   	  return $list;
   }
}

 ?>