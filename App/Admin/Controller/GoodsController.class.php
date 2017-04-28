<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends MyController
{
        //添加商品
        public function add(){
            if(IS_POST){
                    $goodsmodel  = D('Goods');
                    if($goodsmodel->create()){
                         if($goodsmodel->add()){
                                $this->success('添加成功');exit;
                         }
                    }
                    //获取模型里面的错误提示
                    //模型中的getError()方法是输出模型中error属性的内容。
                    $error = $goodsmodel->getError();
                    if(empty($error)){
                            $error = '添加失败';
                    }
                    //入库失败时，提示添加失败
                    //上传失败时，提示上传失败的错误提示。
                    $this->error($error);
            }
                //取出栏目的数据
              $catemodel = D('Category');
              $catedata = $catemodel->getTree();
              $this->assign('catedata',$catedata);
              //取出商品类型数据
              $typemodel = D('Type');
              $typedata = $typemodel->select();
              $this->assign('typedata',$typedata);
              $this->display();
        }
        public function showattr(){
                $type_id = $_GET['type_id'];
                //根据type_id取出 属性数据
                $attrmodel  = D('Attribute');
                $attrdata=$attrmodel->where("type_id=$type_id")->select();
                $this->assign('attrdata',$attrdata);
                $this->display();
        }
        public function lst(){
            $goodsmodel = D('Goods');
            $goodsdata =  $goodsmodel->field("id,goods_name,goods_sn,shop_price,is_best,is_sale,is_new,is_hot,goods_number")->select();
            $this->assign('goodsdata',$goodsdata);
            $this->display();
        }
        //完成ajax切换的一个方法
        public function ajaxToggle(){
                //接收传递的参数
                $id = $_GET['id'];
                $value = $_GET['value'];
                $field=$_GET['field'];
                $goodsmodel = D('Goods');
                //返回受影响的行数。
                echo  $goodsmodel->where("id=$id")->setField('is_'.$field,$value);
        }
        public function product()
        {
            $goods_id=$_GET['id']+0;
            if(IS_POST){
                $goods_id = I('post.goods_id');
                $attr = I('post.attr');
                $goods_number = I('post.goods_number');
                $kc=0;
                foreach($goods_number as $k => $v){
                    $a = array();
                    foreach($attr as $k1 => $v1){
                        $a[] = $v1[$k];
                    }
                    M('Product')->add(array(
                        'goods_id'=>$goods_id,
                        'goods_attr_id'=>implode(',',$a),
                        'goods_number'=>$v,
                        ));
                        $kc+=$v;
                }
                M('Goods')->where("id=$goods_id")->setField('goods_number',$kc);
            }
            $sql="select a.*,b.attr_name from it_goods_attr a left join it_attribute b on a.goods_attr_id=b.id where a.goods_id=$goods_id and a.goods_attr_id in (select goods_attr_id from it_goods_attr where goods_id=$goods_id group by goods_attr_id having count(*)>1)";
            $data = M()->query($sql);
            $list=array();
            foreach($data as $v){
                $list[$v['goods_attr_id']][]=$v;
            }
            $this->assign('list',$list);
            $this->display();
        }
}

?>