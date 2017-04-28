<?php
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model{
        //定义一个入库之前的操作的方法
        /*protected  function _before_insert(&$data,$options){
               $data['add_time']=time();
               //接收传递的货号
               $goods_sn = I('post.goods_sn');
               if(empty($goods_sn)){
                    //我们自己生成货号
                    //uniqid()该函数会生成一个唯一的字符串
                     $goods_sn = "sn_".uniqid(); 
                     $data['goods_sn']=$goods_sn;
               }
               //完成文件的上传
               //取出配置文件里面定义的参数，
               $root_path = C('UPLOAD_ROOT_PATH');
               $maxfilesize = (int)C('UPLOAD_MAX_FILESIZE');
               $allow_ext = C('UPLOAD_ALLOW_EXT');
               //取出php.ini文件里面的选项值
               $maxfile =(int)ini_get('upload_max_filesize');
               $allow_max_filesize = min($maxfilesize,$maxfile);
               $upload = new \Think\Upload();// 实例化上传类    
               $upload->maxSize   =     $allow_max_filesize*1024*1024 ;// 设置附件上传大小    
               $upload->exts      =     $allow_ext;// 设置附件上传类型    
               $upload->rootPath  =      $root_path; // 文件上传保存的根路径
               $upload->savePath  =      'Goods/'; // 设置附件上传目录,相对于根路径的。
               $info   =   $upload->upload();
               if($info){
                    //上传成功
                   $goods_ori = $info['goods_img']['savepath'].$info['goods_img']['savename'];
                    //还需要生成多张缩略图  100*100  230*230
                    $image = new \Think\Image();
                    $image->open($root_path.$goods_ori);// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
                    $goods_thumb = $info['goods_img']['savepath'].'thumb'.$info['goods_img']['savename'];
                    $goods_img = $info['goods_img']['savepath'].'img'.$info['goods_img']['savename'];
                    //注意：在生成多张缩略图时，要先生成大图
                    $image->thumb(230, 230)->save($root_path.$goods_img);
                    $image->thumb(100, 100)->save($root_path.$goods_thumb);
                   $data['goods_ori']=$goods_ori;
                   $data['goods_thumb']=$goods_thumb;
                   $data['goods_img']=$goods_img;
               }else{
                    //上传失败，要输出错误提示
                    $this->error=$upload->getError();//把上传失败的错误提示赋给模型中的error变量。
                    return false;
               }
        }*/
        protected  function _before_insert(&$data,$options){
               $data['add_time']=time();
               //接收传递的货号
               $goods_sn = I('post.goods_sn');
               if(empty($goods_sn)){
                    //我们自己生成货号
                    //uniqid()该函数会生成一个唯一的字符串
                     $goods_sn = "sn_".uniqid(); 
                     $data['goods_sn']=$goods_sn;
               }
                //判断是否有文件上传，
                if($_FILES['goods_img']['error']!=4){
                        //调用上传的函数 
                        $res = oneFileupload('goods_img','Goods',$arr=array(array(100,100),array(230,230)));
                        if($res['status']==0){
                                //成功
                                $data['goods_ori']=$res['info'][0];
                                $data['goods_thumb']=$res['info'][1];
                                $data['goods_img']=$res['info'][2];
                        }else{
                               $this->error=$res['info'];//把上传失败的错误提示赋给模型中的error变量。
                               return false;
                        }
                }
              
               
        }
        protected function _after_insert($data,$options)
        {
          $attr =I('post.attr');
          $goods_id = $data['id'];
          foreach($attr as $k => $v){
            if(is_array($v)){
              foreach($v as $v1){
                M('GoodsAttr')->add(array(
                'goods_id'=>$goods_id,
                'goods_attr_id'=>$k,
                'attr_value'=>$v1
                ));
              }
              
            }else{
               M('GoodsAttr')->add(array(
                'goods_id'=>$goods_id,
                'goods_attr_id'=>$k,
                'attr_value'=>$v
                ));
            }
          }
        }

        public function getGoods($type,$num)
        {
           if($type=='best' || $type=='hot' || $type=='new'){
            return $this->field("id,goods_name,goods_thumb,shop_price")->where("is_".$type."=1 and is_sale=1 and is_delete=0")->order("id desc")->limit($sum)->select();
           }
           return;
        }    
}

?>