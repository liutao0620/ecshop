<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP 管理中心 - 商品列表 </title>
<meta name="robots" c>
<meta http-equiv="Content-Type" c />
<link href="/Public/Admin/styles/general.css" rel="stylesheet" type="text/css" />
<link href="/Public/Admin/styles/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/Js/jquery.js"></script>
<script>
//参数o就是指img对象
//参数id就是当前记录的id
//参数field就是要修改数据库里面的哪个 字段。
function ajaxToggle(o,id,field){
        //思路：获取对象的src属性
       var srcs = $(o).attr('src');
        //判断srcs里面是否有'yes.gif',如果有则把数据库里面的值改为0
        //母字符串.indexOf(子字符串) 判断子字符串是否在母字符串里面，如果存在就返回所在的位置，如果不存在则返回过-1
       var value;//数据库中被改成的值
       if(srcs.indexOf('yes.gif')!==-1){
                //有
                value=0;
       }else{
                value = 1;
       }
       $.ajax({
            type:'get',
            url:"<?php echo U('ajaxToggle')?>",
            cache:false,
            data:'id='+id+'&value='+value+'&field='+field,
            success:function(msg){
            //msg返回的执行结果，受影响的行数，
                if(msg>0){
                        //已经修改成功
                        if(value==0){
                                srcs = srcs.replace('yes.gif','no.gif');
                        }else{
                                srcs = srcs.replace('no.gif','yes.gif');
                        }
                        $(o).attr('src',srcs);
                }
            }
       });

}
</script>
</head>
<body>

<h1>
<span class="action-span"><a href="goodsadd.html">添加新商品</a></span>
<span class="action-span1"><a href="#">ECSHOP 管理中心</a> </span><span id="search_id" class="action-span1"> - 商品列表 </span>
<div style="clear:both"></div>
</h1>

<div class="form-div">

  <form action="" name="searchForm">
    <img src="/Public/Admin/images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
      
    <select name="cat_id"><option value="0">所有分类</option><option value="1" >手机类型</option><option value="4" >&nbsp;&nbsp;&nbsp;&nbsp;3G手机</option><option value="5" >&nbsp;&nbsp;&nbsp;&nbsp;双模手机</option><option value="2" >&nbsp;&nbsp;&nbsp;&nbsp;CDMA手机</option><option value="3" >&nbsp;&nbsp;&nbsp;&nbsp;GSM手机</option><option value="12" >充值卡</option><option value="14" >&nbsp;&nbsp;&nbsp;&nbsp;移动手机充值卡</option><option value="15" >&nbsp;&nbsp;&nbsp;&nbsp;联通手机充值卡</option><option value="13" >&nbsp;&nbsp;&nbsp;&nbsp;小灵通/固话充值卡</option><option value="6" >手机配件</option><option value="11" >&nbsp;&nbsp;&nbsp;&nbsp;读卡器和内存卡</option><option value="7" >&nbsp;&nbsp;&nbsp;&nbsp;充电器</option><option value="8" >&nbsp;&nbsp;&nbsp;&nbsp;耳机</option><option value="9" >&nbsp;&nbsp;&nbsp;&nbsp;电池</option></select>

   
    <select name="brand_id"><option value="0">所有品牌</option><option value="1">诺基亚</option><option value="10">金立</option><option value="9">联想</option><option value="8">LG</option><option value="7">索爱</option><option value="6">三星</option><option value="5">夏新</option><option value="4">飞利浦</option><option value="3">多普达</option><option value="2">摩托罗拉</option><option value="11">  恒基伟业</option></select>

    
    <select name="intro_type"><option value="0">全部</option><option value="is_best">精品</option><option value="is_new">新品</option><option value="is_hot">热销</option><option value="is_promote">特价</option><option value="all_type">全部推荐</option></select>
         
     
      <select name="suppliers_id"><option value="0">全部</option><option value="1">北京供货商</option><option value="2">上海供货商</option></select>

            
      <select name="is_on_sale"><option value=''>全部</option><option value="1">上架</option><option value="0">下架</option></select>
      
    关键字 <input type="text" name="keyword" size="15" />
    <input type="submit" value=" 搜索 " class="button" />
  </form>
</div>
<form method="post" action="" name="listForm" >

  <div class="list-div" id="listDiv">
<table cellpadding="3" cellspacing="1">
  <tr>
    <th>
      <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" />
      <a href="#">编号</a><img src="/Public/Admin/images/sort_desc.gif"/>    </th>

    <th><a href="#">商品名称</a></th>
    <th><a href="#">货号</a></th>
    <th><a href="#">价格</a></th>
    <th><a href="#">上架</a></th>
    <th><a href="#">精品</a></th>
    <th><a href="#">新品</a></th>

    <th><a href="#">热销</a></th>
    <th><a href="#">推荐排序</a></th>
        <th><a href="#">库存</a></th>
        <th>操作</th>
  </tr>
  <?php  foreach($goodsdata as $v){?>
      <tr>
    <td><input type="checkbox" name="checkboxes[]" value="32" /><?php echo $v['id']?></td>

    <td class="first-cell" style=""><span ><?php echo $v['goods_name']?></span></td>
    <td><span ><?php echo $v['goods_sn']?></span></td>
    <td align="right"><span ><?php echo $v['shop_price']?>
    </span></td>
    <td align="center"><img style="cursor:pointer"onclick="ajaxToggle(this,'<?php echo $v['id']?>','sale')" src="/Public/Admin/images/<?php echo $v['is_sale']==1?'yes':'no'?>.gif"  /></td>
    <td align="center"><img style="cursor:pointer"  onclick="ajaxToggle(this,'<?php echo $v['id']?>','best')" src="/Public/Admin/images/<?php echo $v['is_best']==1?'yes':'no'?>.gif"  /></td>
    <td align="center"><img style="cursor:pointer" onclick="ajaxToggle(this,'<?php echo $v['id']?>','new')" src="/Public/Admin/images/<?php echo $v['is_new']==1?'yes':'no'?>.gif"  /></td>
    <td align="center"><img style="cursor:pointer" onclick="ajaxToggle(this,'<?php echo $v['id']?>','hot')" src="/Public/Admin/images/<?php echo $v['is_hot']==1?'yes':'no'?>.gif"  /></td>

    <td align="center"><span >100</span></td>
        <td align="right"><span ><?php echo $v['goods_number'];?></span></td>
        <td align="center">
      <a href="#" target="_blank" title="查看"><img src="/Public/Admin/images/icon_view.gif" width="16" height="16" border="0" /></a>
      <a href="#" title="编辑"><img src="/Public/Admin/images/icon_edit.gif" width="16" height="16" border="0" /></a>
      <a href="#" title="复制"><img src="/Public/Admin/images/icon_copy.gif" width="16" height="16" border="0" /></a>
      <a href="#"  title="回收站"><img src="/Public/Admin/images/icon_trash.gif" width="16" height="16" border="0" /></a>
      <a href="<?php echo U('product',array('id'=>$v['id']))?>" title="货品列表"><img src="/Public/Admin/images/icon_docs.gif" width="16" height="16" border="0" /></a>          </td>
  </tr>
     <?php }?>  
      </table>

<table id="page-table" cellspacing="0">
  <tr>
    <td align="right" nowrap="true">
      
        
          上页&nbsp;&nbsp;1</b>&lt;&lt; [1]&nbsp;&nbsp;<a href=admin.php?c=goods&a=goodsList&page=2& title='第2页'>[2]</a>&nbsp;&nbsp;<a href=admin.php?c=goods&a=goodsList&page=3& title='第3页'>[3]</a>&nbsp;&nbsp;<a href=admin.php?c=goods&a=goodsList&page=4& title='第4页'>[4]</a>&nbsp;&nbsp;<a href=admin.php?c=goods&a=goodsList&page=5& title='第5页'>[5]</a>&nbsp;&nbsp;<a href=admin.php?c=goods&a=goodsList&page=8& title='第8页'>&gt;&gt;8</a>&nbsp;<a href=admin.php?c=goods&a=goodsList&page=2&  title='下一页'>[下一页]</a>
    
    </td>
  </tr>

</table>

</div>
</form>

<div id="footer">
共执行 7 个查询，用时 0.112141 秒，Gzip 已禁用，内存占用 3.085 MB<br />
版权所有 &copy; 2005-2010 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>