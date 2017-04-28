<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model
{
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

   public function getChildId($id)
   {
        $arr = $this->select();
        return $this->_getChildId($arr,$id);
    }
   public function _getChildId($arr,$id)
   {
        static $ids=array();
        foreach($arr as $v){
                if($v['parent_id']==$id){
                        $ids[]=$v['id'];
                        $this->_getChildId($arr,$v['id']);
                }
        }
        return $ids;
   }
}
?>