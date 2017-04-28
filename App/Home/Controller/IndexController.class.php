<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index()
    {
    	$goodsmodel = D("Admin/Goods");
    	$newdata=$goodsmodel->getGoods('new',3);
    	$hotdata=$goodsmodel->getGoods('hot',3);
    	$bestdata=$goodsmodel->getGoods('best',3);
    	$this->assign(array(
            'newdata'=>$newdata,
            'hotdata'=>$hotdata,
            'bestdata'=>$bestdata,
    		));
    	$catemodel = M('Category');
    	$catedata = $catemodel->select();
    	$this->assign('catedata',$catedata);
        $this->display();
    }
    public function category()
    {
    	$cat_id=$_GET['id']+0;
    	if($cat_id==0){
    		header("location:/index.php");
    	}
    	$catemodel = D("Admin/Category");
    	$ids = $catemodel->getChildId($cat_id);
    	//if(empty($ids)){
    		$ids[]=$cat_id;
    	//}
    	$goodsmodel = M('Goods');
    	$ids = implode(',',$ids);
    	$goodsdata = $goodsmodel->field("id,goods_name,goods_thumb,shop_price")->where("cat_id in ($ids)")->select();
    	$this->assign('goodsdata',$goodsdata);
        $this->display();
    }
    public function detail()
    {
        $goods_id=$_GET['id']+0;
        if($goods_id <=0){
            header("location:/index.php");
        }
        $goodsmodel = M('Goods');
        $goodsinfo = $goodsmodel->field("id,goods_name,goods_img,goods_sn,shop_price,add_time,goods_ori")->find($goods_id);
        $attrdata=M('GoodsAttr')->field("a.*,b.attr_name,b.attr_type")->join("a left join it_attribute b on a.goods_attr_id=b.id")->where("a.goods_id=$goods_id")->select();
        $radiodata=array();
        $uniquedata=array();
        foreach($attrdata as $v){
            if($v['attr_type']==1){
                $radiodata[$v['goods_attr_id']][]=$v;
            }else{
                $uniquedata[]=$v;
            }
        }
        $this->assign(array(
            'goodsinfo'=>$goodsinfo,
            'radiodata'=>$radiodata,
            'uniquedata'=>$uniquedata,
            ));
        $this->display();
    }
}