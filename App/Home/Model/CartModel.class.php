<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model{
        //添加商品到购物车的方法
        //参数1：商品的id
        //参数2 : 商品的属性
        //参数3：购买数量
        public function addCart($goods_id,$goods_attr_id,$goods_count){
                    //取出登录用户的id
                    $user_id = $_SESSION['user_id']+0;
                    if($user_id>0){
                            //已经登录，把数据就存储到数据库里面
                            //在存储之前要判断购物车表里面是否有该商品，如果有，则修改购买数量，如果没有则添加。
                            $info = $this->where("user_id=$user_id and goods_id=$goods_id and goods_attr_id='$goods_attr_id'")->find();
                            if($info){
                                        //该商品已经存在  ，则修改购买数量
                                        $this->where("user_id=$user_id and goods_id=$goods_id and goods_attr_id='$goods_attr_id'")->setInc('goods_count',$goods_count);
                            }else{
                                        //该商品不存在，则直接添加
                                        $this->add(array(
                                                'goods_id'=> $goods_id,
                                                'goods_attr_id'=>$goods_attr_id,
                                                'goods_count'=>$goods_count,
                                                'user_id'=>$user_id
                                        ));
                            }
                    }else{
                            //没有登录,则保存到cookie里面，
                            $cart = isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
                            //判断该商品是否已经存在于cookie中，如果存在，则增加购买数量，如果没有就直接添加
                            //构造键
                            $key = $goods_id.'-'.$goods_attr_id;
                            if(isset($cart[$key])){
                                        //说明已经存在，则修改购买 数量
                                        $cart[$key]+=$goods_count;
                            }else{
                                        //说明没有，就直接添加
                                        $cart[$key]=$goods_count;
                            }
                            //把修改的数组，再保存到cookie里面，
                            setcookie('cart',serialize($cart),time()+3600*24*7,'/');
                    }
        }

        //购物车列表的方法
        public function cartList(){
                //从cookie或数据库里面获取数据
                //判断用户是否登录，
                $user_id = $_SESSION['user_id']+0;
                if($user_id>0){
                            //已经登录，从数据库里面取出
                            //条件是什么？ 答：user_id  购物车表it_cart
                            $cartdata = $this->where("user_id=$user_id")->select();//二维数组

                }else{
                            //没有登录，从cookie里面取出
                            $cart= isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();//一维数组
                            //$cart=array('3-5,6'=>12,'2-6,7'=>34)
                            //为了统一遍历，把该一维数组转换成二维数组
                            $cartdata = array();
                            foreach($cart as $k=>$v){
                                        $a = explode('-',$k);
                                        $cartdata[]=array(
                                                'goods_id'=>$a[0],
                                                'goods_attr_id'=>$a[1],
                                                'goods_count'=>$v,
                                        );
                            }
                }
                //还需要对数据进行加工，获取缩略图，单价，属性信息。
                $cartList=array();
                foreach($cartdata as $v){
                            //在添加一个info元素，用于获取商品的基本信息
                            $v['info']=M('Goods')->field("id,goods_name,goods_thumb,shop_price")->where("id=".$v['goods_id'])->find();
                            //attrs用于获取商品的属性信息的。
                            $v['attrs']=$this->getAttrs($v['goods_id'],$v['goods_attr_id']);
                            $cartList[]=$v;
                }
                return $cartList;

        }
        //定义一个方法，取出属性数据
        public function getAttrs($goods_id,$goods_attr_id){
                $sql="select  group_concat(concat(b.attr_name,':',a.attr_value) separator '<br/>')  as attrs
                from it_goods_attr  a left join it_attribute   b  on  a.goods_attr_id=b.id where a.goods_id=$goods_id and a.id in($goods_attr_id)";
               $info =  M()->query($sql);//返回的二维数组，
               //p($info);exit;
               return $info[0]['attrs'];
        }
        //用于取出购物车中商品的数量和价格
        public function getTotal(){
                    //思路：
                    $cartdata = $this->cartList();//返回购物车里面的数据
                    $total_number = 0;//设置购买数量
                    $total_price = 0;//设置总计价格
                    if($cartdata){
                            //说明购物车里面有商品
                            foreach($cartdata as $v){
                                    $total_number+=$v['goods_count'];
                                    $total_price+=$v['goods_count']*$v['info']['shop_price'];
                            }
                    }
                    return array('total_number'=>$total_number,'total_price'=>$total_price);

        }
        //删除购物车的方法
        public function cartDel($goods_id,$goods_attr_id){
                //判断用户是否登录
                $user_id = $_SESSION['user_id']+0;
                if($user_id>0){
                        //已经登录,操作数据库
                        $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->delete();
                }else{
                        //没有登录，操作cookie
                        $cart= isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();//一维数组
                         //$cart=array('3-5,6'=>12,'2-6,7'=>34)
                        //构造键
                        $key=$goods_id.'-'.$goods_attr_id;
                        unset($cart[$key]);
                       //把修改的数组，再保存到cookie里面，
                       setcookie('cart',serialize($cart),time()+3600*24*7,'/');  
                       //表示在当前www.bjshop.com/下面的文件都可以读取cookie的数据
                }
        }
        //cookie数据移动到数据库里面。
        public function cookie2db(){
                    //取出cookie数据
                    $cart= isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();//一维数组  
                    $user_id = $_SESSION['user_id'];
                    if($cart){
                             //cookie里面有数据，
                             //判断数据库里面，是否有该商品，
                             ////$cart=array('3-5,6'=>12,'2-6,7'=>34)
                             foreach($cart as $k=>$v){
                                        $a  = explode('-',$k);
                                        $goods_id = $a[0];
                                        $goods_attr_id = $a[1];
                                        $goods_count = $v;
                                        $info  = $this->where("user_id=$user_id and goods_id=$goods_id and goods_attr_id='$goods_attr_id'")->find();
                                        if($info){
                                                //说明该商品已经存在，则直接修改购买数量
                                                $this->where("user_id=$user_id and goods_id=$goods_id and goods_attr_id='$goods_attr_id'")->setInc('goods_count',$goods_count);

                                        }else{
                                                //说明该商品不存在，则直接添加数据库。
                                                $this->add(array(
                                                        'goods_id'=>$goods_id,
                                                        'goods_attr_id'=>$goods_attr_id,
                                                        'goods_count'=>$goods_count,
                                                        'user_id'=>$user_id
                                                ));
                                        }    
                             
                             }
                            //把cookie数据给清空
                            setcookie('cart','',time()-1,'/');
                    }
        }
        //修改购物车的方法
        public function cartUpdate($goods_id,$goods_attr_id,$goods_count){
                    //判断用户是否登录，如果登录则直接修改数据库，如果没有登录，则修改cookie
                    $user_id = $_SESSION['user_id']+0;
                    if($user_id>0){
                            //已经登录，我们要修改数据库
                            $this->where("user_id=$user_id and goods_id=$goods_id and goods_attr_id='$goods_attr_id'")->setInc('goods_count',$goods_count);
                    }else{
                            //没有登录，则修改cookie
                            $cart= isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();//一维数组  
                            //构造键
                            $key = $goods_id.'-'.$goods_attr_id;
                            //修改购买数量   $cart=array(2=>3,5=>8)   $key=2     $cart[$key]=$cart[$key]+5;
                            $cart[$key]=$cart[$key]+$goods_count;
                            //把修改后的结果存储到cookie里面
                             setcookie('cart',serialize($cart),time()+3600*24*7,'/');
                    }
        }
        //清空购物车的方法
        public function cartClear(){
                //判断用户是否登录，，如果已经登录，则直接修改数据库，如果没有登录，则直接修改cookie
                $user_id = $_SESSION['user_id']+0;
                if($user_id>0){
                          //已经登录，操作数据库
                          $this->where("user_id=$user_id")->delete();
                }else{
                         //没有登录，操作cookie
                         setcookie('cart','',time()-1,'/');
                }
        }
}
?>