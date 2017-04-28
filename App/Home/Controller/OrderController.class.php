<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller{
        public function checkout(){
                //(1)判断购物车里面是否有商品，
                $cartmodel = D('Cart');
                $info = $cartmodel->getTotal();
                if($info['total_number']==0){
                        //购物车里面没有商品
                        $this->error('没有商品，无法下订单');
                }
                //(2)判断用户是否登录，如果没有登录，则跳转到登录页面，登录完成后，再跳回来。
                $user_id = $_SESSION['user_id'];
                if(empty($user_id)){
                        //没有登录，
                        //把调回的地址，存储到session里面，
                        $_SESSION['return_url']='Order/checkout';
                        $this->redirect('User/login');
                }
                $cartdata = $cartmodel->cartList();
                $this->assign('cartdata',$cartdata);
                $this->display();
        }
        public function flow()
        {
            if(IS_POST){
                $fh  = fopen('./lock.lock','w');
                   flock($fh,LOCK_EX);//添加文件锁
                foreach($cartdata as  $v){
                            //如果没有属性，则要和 it_goods表里面的goods_number字段比较
                            if(empty($v['goods_attr_id'])){
                                    //取出库存  
                                    $kc = M('Goods')->where('id='.$v['goods_id'])->getField('goods_number');
                            }else{
                                    $attr = $v['goods_attr_id'];
                                    $where = "goods_id=".$v['goods_id']." and goods_attr_id='$attr'";
                                    $kc = M('Product')->where($where)->getField('goods_number');
                            }
                            //判断库存是否充足
                            if($v['goods_count']>=$kc){
                                        $this->error('库存不足');
                            }
                    }
                $order_sn = strtoupper('sn_'.substr(uniqid(),-6).time());
                $user_id = $_SESSION['user_id'];
                $cartmodel = D('Cart');
                $info = $cartmodel->getTotal();
                $order_amount = $info['total_price'];
                $consignee = I('post.consignee');
                $address = I('post.address');
                $mobile = I('post.mobile');
                $pay_name = I('post.pay_name');
                $shipping_name = I('shipping_name');
                $add_time = time();
                $order_id = M('Order')->add(array(
                        'order_sn'=>$order_sn,
                        'user_id'=>$user_id,
                        'order_amount'=>$order_amount,
                        'consignee'=>$consignee,
                        'address'=>$address,
                        'mobile'=>$mobile,
                        'pay_name'=>$pay_name,
                        'shipping_name'=>$shipping_name,
                        'add_time'=>$add_time,
                ));
                $cartdata = $cartmodel->cartList();
                foreach($cartdata as $v){
                        M('OrderGoods')->add(array(
                                'order_id'=>$order_id,
                                'goods_name'=>$v['info']['goods_name'],
                                'shop_price'=>$v['info']['shop_price'],
                                'goods_attr_id'=>$v['goods_attr_id'],
                                'goods_count'=>$v['goods_count'],
                        ));
                }
                //下单成功，减掉库存
                        foreach($cartdata as $v){
                                    //判断该商品是否有属性，如果有属性，则减掉it_product表里面的库存，还要减掉it_goods里面的库存，如果该商品没有属性，则直接减掉it_goods表里面的库存。、
                                    if(!empty($v['goods_attr_id'])){
                                                //减掉it_product表里面的库存
                                                $attr = $v['goods_attr_id'];
                                                $where = "goods_id=".$v['goods_id']." and goods_attr_id='$attr'";
                                                M('Product')->where($where)->setDec('goods_number',$v['goods_count']);
                                    }
                                    //减掉it_goods表里面的库存
                                    M('Goods')->where("id=".$v['goods_id'])->setDec('goods_number',$v['goods_count']);
                        }
                        flock($fh,LOCK_UN);
                $cartmodel->cartClear();
                $this->assign('order_sn',$order_sn);

                if($pay_name == 2){
                    $v_mid = '1009001';//商户的编号
                    $key='#(%#WU)(UFGDKJGNDFG';
                    $v_oid = $order_sn;//订单编号
                    $v_amount = $order_amount;//订单金额
                    $v_moneytype = 'CNY';
                    $v_url = 'http://www.ecshop.com'.U('Order/access');
                    //支付动作完成后返回到该url，支付结果以POST方式发送                    
                    //v_amount v_moneytype v_oid v_mid v_url key六个参数的value
                    $v_md5info = strtoupper(md5($v_amount.$v_moneytype.$v_oid.$v_mid.$v_url.$key));
                    $this->assign(array(
                                        'v_mid'=>$v_mid,
                                        'v_oid'=>$v_oid,
                                        'v_amount'=>$v_amount,
                                        'v_moneytype'=>$v_moneytype,
                                        'v_url'=>$v_url,
                                        'v_md5info'=>$v_md5info
                                ));
                    $this->display('online');
                }elseif($pay_name == 3){
                    require_once("./alipay/alipay.config.php");
                    require_once("./alipay/lib/alipay_submit.class.php");

                    /**************************请求参数**************************/

                            //支付类型
                            $payment_type = "1";
                            //必填，不能修改
                            //服务器异步通知页面路径
                            $notify_url = "http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/notify_url.php";
                            //需http://格式的完整路径，不能加?id=123这类自定义参数

                            //页面跳转同步通知页面路径
                            $return_url = "http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/return_url.php";
                            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

                            //商户订单号
                            $out_trade_no = $_POST['WIDout_trade_no'];
                            //商户网站订单系统中唯一订单号，必填

                            //订单名称
                            $subject = $_POST['WIDsubject'];
                            //必填

                            //付款金额
                            $total_fee = $_POST['WIDtotal_fee'];
                            //必填

                            //订单描述

                            $body = $_POST['WIDbody'];
                            //商品展示地址
                            $show_url = $_POST['WIDshow_url'];
                            //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

                            //防钓鱼时间戳
                            $anti_phishing_key = "";
                            //若要使用请调用类文件submit中的query_timestamp函数

                            //客户端的IP地址
                            $exter_invoke_ip = "";
                            //非局域网的外网IP地址，如：221.0.0.1


                    /************************************************************/

                    //构造要请求的参数数组，无需改动
                    $parameter = array(
                            "service" => "create_direct_pay_by_user",
                            "partner" => trim($alipay_config['partner']),
                            "seller_email" => trim($alipay_config['seller_email']),
                            "payment_type"  => $payment_type,
                            "notify_url"    => $notify_url,
                            "return_url"    => $return_url,
                            "out_trade_no"  => $out_trade_no,
                            "subject"   => $subject,
                            "total_fee" => $total_fee,
                            "body"  => $body,
                            "show_url"  => $show_url,
                            "anti_phishing_key" => $anti_phishing_key,
                            "exter_invoke_ip"   => $exter_invoke_ip,
                            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
                    );

                    //建立请求
                    $alipaySubmit = new \AlipaySubmit($alipay_config);
                    $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
                    $this->assign('html_text',$html_text);
                    $this->display('alipay');

                }else{
                    $this->display();
                }
                

            }
        }
        public function access()
        {
            $v_oid = $_POST['v_oid'];//订单号
            $v_pstatus=$_POST['v_pstatus'];
            $v_md5str = $_POST['v_md5str'];
            $v_amount=$_POST['v_amount'];
            $v_moneytype = $_POST['v_moneytype'];
            //要验证数据的合法性，
            //我们自己生成一个校验码（接收的post数据+密钥）
            //v_oid，v_pstatus，v_amount，v_moneytype，key
            $key='#(%#WU)(UFGDKJGNDFG';
            $md5info  = strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));
            if($md5info==$v_md5str){
                     if($v_pstatus==20){
                            //支付成功
                            //修改订单中的支付状态。
                            M('Order')->where("order_sn='$v_oid'")->setField('pay_status',1);
                            $this->success('支付完成',U('Index/index'));
                     }else{
                            //支付失败
                     } 
            }else{
                    //数据异常
            }
        }
}
?>