<?php
namespace Home\Controller;
use Think\Controller;
class CartController extends Controller{
    //添加一个方法用于添加商品到购物车
    public function addCart(){
                //接收提交的数据
                $goods_id  = I('post.id');
                $attr = I('post.attr');//数组
                $goods_attr_id='';
                //判断当前商品是否有属性
                if(!empty($attr)){
                        $goods_attr_id = implode(',',$attr);
                }
                $goods_count = I('post.goods_count');
                $cartmodel = D('Cart');
                //调用模型中添加商品到购物车的方法
                $cartmodel->addCart($goods_id,$goods_attr_id,$goods_count);
                //添加数据到购物车后，要跳转到购物车列表页面，
                $this->success('添加购物车成功',U('Cart/cartList'));

    }
    //购物车列表页面
    public function cartList(){
           $cartmodel = D('Cart');
           $cartdata = $cartmodel->cartList();//返回购物车里面的数据
           //p($cartdata);exit;
           $this->assign('cartdata',$cartdata);
           //取出购物车里面的商品个数，和总的价格
           $total = $cartmodel->getTotal();
           //p($total);exit;
           $this->assign('total',$total);
           $this->display();
    }
    public function cartDel(){
            $goods_id = $_GET['goods_id'];
            $goods_attr_id = $_GET['goods_attr_id'];
            $cartmodel = D('Cart');
            $cartdata = $cartmodel->cartDel( $goods_id,$goods_attr_id);
            $this->redirect('Cart/cartList');
    }
    public function cartUpdate(){
            //接收传递的数据
            $goods_id = $_GET['goods_id']+0;
            $goods_attr_id = $_GET['goods_attr_id'];
            $cartmodel = D('Cart');
            $goods_count = 1;
            $cartdata = $cartmodel->cartUpdate( $goods_id,$goods_attr_id,$goods_count);
            echo 1;
    }
    public function cartClear(){
            $cartmodel = D('Cart');
            $cartmodel->cartClear();
            $this->redirect('Cart/cartList');
    }
}
?>