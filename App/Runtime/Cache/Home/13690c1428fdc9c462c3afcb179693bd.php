<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>购物车页面</title>
	<link rel="stylesheet" href="/Public/Home/style/base.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/global.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/header.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/cart.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/footer.css" type="text/css">
	<script type="text/javascript" src="/Public/Home/js/jquery-1.8.3.min.js"></script>
    <script>
     $(function(){
                //给‘+’绑定事件
                $(".add_num").click(function(){
                           //(1)计算新的购买数量，  新的购买数量=原来的购买数量+1
                           //获取当前的购买数量
                            var curr_buy_count  =  parseInt($(this).parent().find('.amount').val());
                            //计算新的购买数量
                            var new_buy_count = curr_buy_count+1;
                           //(2)计算新的小计价格    新的小计价格=原来的小计价格+商品的单价
                           //获取原来的小计价格
                           var curr_xiaoji_price  = parseFloat($(this).parent().parent().find("span:last").html());
                           //获取商品的单价
                           var danjia  = parseFloat($(this).parent().parent().find("span:first").html());
                           //新的小计价格
                           var new_xiaoji_price  =  Math.round((curr_xiaoji_price+danjia),2);
                           //(3)计算新的总金额    新的总金额=原来的总金额+单价
                           //获取原来的总金额
                           var curr_total_price = parseFloat($("#total").html());
                            //新的总金额
                            var new_total_price = Math.round((curr_total_price+danjia),2);
                           //获取当前商品的id
                           var goods_id = $(this).parent().find('.goods_id').val();
                           //获取商品属性的信息
                           var goods_attr_id = $(this).parent().find('.goods_attr_id').val();
                           var _this = $(this);
                           //ajax来完数据的更新
                           $.ajax({
                                type:'get',
                                url:"<?php echo U('Cart/cartUpdate')?>",
                                data:'goods_id='+goods_id+'&goods_attr_id='+goods_attr_id,
                                success:function(msg){
                                        if(msg==1){
                                                //购物车修改成功，显示新的购买数量，新的小计价格，小的总金额
                                                _this.parent().find('.amount').val(new_buy_count);
                                                _this.parent().parent().find("span:last").html(new_xiaoji_price);
                                                $("#total").html(new_total_price)
                                        }
                                }
                           });
                });
     });
    </script>
</head>
<body>
	<!-- 顶部导航 start -->
    <div class="topnav">
		<div class="topnav_bd w1210 bc">
			<div class="topnav_left">
				
			</div>
			<div class="topnav_right fr">
				<ul>
                <?php if(!empty($_SESSION['user_id'])){?>
					<li>您好，欢迎来到京西！[<?php echo $_SESSION['username']?>] [<a href="<?php echo U('User/logout')?>">退出</a>] </li>
                <?php }else {?>
                    <li>您好，欢迎来到京西！[<a href="<?php echo U('User/login')?>">登录</a>] [<a href="<?php echo U('User/register')?>">免费注册</a>] </li>
                <?php }?>
					<li class="line">|</li>
					<li>我的订单</li>
					<li class="line">|</li>
					<li>客户服务</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 顶部导航 end -->
	
	<div style="clear:both;"></div>
	
	<!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="index.html"><img src="/Public/Home/images/logo.png" alt="京西商城"></a></h2>
			<div class="flow fr">
				<ul>
					<li class="cur">1.我的购物车</li>
					<li>2.填写核对订单信息</li>
					<li>3.成功提交订单</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 页面头部 end -->
	
	<div style="clear:both;"></div>

	<!-- 主体部分 start -->
	<div class="mycart w990 mt10 bc">
		<h2><span>我的购物车</span></h2>
		<table>
			<thead>
				<tr>
					<th class="col1">商品名称</th>
					<th class="col2">商品信息</th>
					<th class="col3">单价</th>
					<th class="col4">数量</th>	
					<th class="col5">小计</th>
					<th class="col6">操作</th>
				</tr>
			</thead>
			<tbody>
            <?php foreach($cartdata as $v){?>
				<tr>
					<td class="col1"><a href="<?php echo U('Index/detail',array('id'=>$v['goods_id']))?>"><img src="/Public/Uploads/<?php echo $v['info']['goods_thumb']?>" alt="" /></a>  <strong><a href="<?php echo U('Index/detail',array('id'=>$v['goods_id']))?>"><?php echo $v['info']['goods_name']?></a></strong></td>
					<td class="col2"> <?php echo $v['attrs']?></td>
					<td class="col3">￥<span><?php echo $v['info']['shop_price']?></span></td>
					<td class="col4"> 
						<a href="javascript:;" class="reduce_num"></a>
                        <input type="hidden" name="goods_id" value="<?php echo $v['goods_id']?>" class="goods_id"/>
                        <input type="hidden" name="goods_attr_id" value="<?php echo $v['goods_attr_id']?>" class="goods_attr_id"/>
						<input type="text" name="amount" value="<?php echo $v['goods_count']?>" class="amount"/>
						<a href="javascript:;" class="add_num"></a>
					</td>
					<td class="col5">￥<span><?php echo $v['info']['shop_price']*$v['goods_count']?></span></td>
					<td class="col6"><a href="<?php echo U('cartDel',array('goods_id'=>$v['goods_id'],'goods_attr_id'=>$v['goods_attr_id']))?>">删除</a></td>
				</tr>
		    <?php }?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">购物金额总计： <strong>￥ <span id="total"><?php  echo $total['total_price']?></span></strong></td>
				</tr>
			</tfoot>
		</table>
		<div class="cart_btn w990 bc mt10">
			<a href="<?php echo U('Index/index')?>" class="continue">继续购物</a>
			<a href="<?php echo U('Cart/cartClear')?>" class="continue">清空购物车</a>
			<a href="<?php echo U('Order/checkout')?>" class="checkout">结 算</a>
		</div>
	</div>
	<!-- 主体部分 end -->

	<div style="clear:both;"></div>
	<!-- 底部版权 start -->
	<div class="footer w1210 bc mt15">
		<p class="links">
			<a href="">关于我们</a> |
			<a href="">联系我们</a> |
			<a href="">人才招聘</a> |
			<a href="">商家入驻</a> |
			<a href="">千寻网</a> |
			<a href="">奢侈品网</a> |
			<a href="">广告服务</a> |
			<a href="">移动终端</a> |
			<a href="">友情链接</a> |
			<a href="">销售联盟</a> |
			<a href="">京西论坛</a>
		</p>
		<p class="copyright">
			 © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号 
		</p>
		<p class="auth">
			<a href=""><img src="/Public/Home/images/xin.png" alt="" /></a>
			<a href=""><img src="/Public/Home/images/kexin.jpg" alt="" /></a>
			<a href=""><img src="/Public/Home/images/police.jpg" alt="" /></a>
			<a href=""><img src="/Public/Home/images/beian.gif" alt="" /></a>
		</p>
	</div>
	<!-- 底部版权 end -->
</body>
</html>