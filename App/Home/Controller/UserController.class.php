<?php 
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller
{
	public function register()
	{
		if(IS_POST){
			$usermodel = D('User');
			if($usermodel->create()){
				$key=substr(uniqid(),-6);
				$usermodel->validate=$key;
				$usermodel->password=md5(I('post.password'));
				$usermodel->reg_time=time();
                if($usermodel->add()){
                    $title='注册完成激活账户';
                    $fromuser='京西商场';
                    $address=I('post.email');
                    $username=I('post.username');
                    $url="http://www.ecshop.com".U('User/active',array('email'=>$address,'key'=>$key));
                    $content="尊敬的{$username}：<br/><a href='$url'>单击激活</a>";
                	if(sendEmail($title,$content,$fromuser,$address)){
                		$this->success('注册成功，赶紧到邮箱激活',U('index/index'));
                	}else{
                		$this->error('发送邮件失败，请重新注册');
                	}
                    //$this->success('注册完成',U('Index/index'));exit;
                }else{
                	$this->error('注册失败');
                }
			}else{
				$this->error($usermodel->getError());
			}
		}

		$this->display();
	}
    
    public function login()
    {
	if(IS_POST){
        	$usermodel = D('User');
        	if($usermodel->validate($usermodel->_login_validate)->create()){
                if($usermodel->login()){
                    $cartmodel=D('Cart');
                    $cartmodel->cookie2db();
                    $return_url=$_SESSION['return_url'];
                    if(empty($return_url)){
                        $url='Index/index';
                    }else{
                        $url=$return_url;
                    }
                	$this->success('登录成功',U($url));
                	exit;
                }
                $this->error($usermodel->getError());
        	}
        	$this->error($usermodel->getError());        	
        }
		$this->display();
	}
    
    public function active()
    {
        //接收传递的内容
        $email = $_GET['email'];
        $key = $_GET['key'];
        //根据email查找出用户，如果用户不存在，则链接有误
        $usermodel = D('User');
        $info = $usermodel->where("email='$email'")->find();
        if(!$info){
                $this->error('链接有误');
        }
        //验证链接的有效性，从表里面取出的 validate和传递的key进行比较，
        if($info['validate']!=$key){
                 $this->error('链接有误');
        }
        //验证链接的有效期，取出注册时的时间戳，和当前的时间戳进行比较，
        if(time()-$info['reg_time']>24*3600){
                 $this->error('链接失效');
        }
        //查看用户的active字段，如果已经激活，则无需激活
        if($info['active']==1){
                $this->error('已经激活，则无需在激活');
        }
        //用户激活，则就是修改active字段为1，
        $usermodel->where("email='$email'")->setField("active",1);
        $this->success('激活成功',U('User/login'));
    }
    public function authcode()
	{
		$config =    array(    
							'fontSize'    =>    20,    // 验证码字体大小    
							'length'      =>    4,     // 验证码位数    
							'useNoise'    =>    false, // 关闭验证码杂点
							'imageW'      =>    140,
							'imageH'      =>    40,
		                  );
		$Verify =     new \Think\Verify($config);
		$Verify->entry();
	}
	public function findpasswordOne()
	{
		$this->display();
	}
	public function findpasswordTwo()
	{
		$username=I('post.username');
		$usermodel=D('User');
		$info=$usermodel->where("username='$username'")->find();
		$_SESSION['find_user_id'] = $info['id'];
		if(!info){
			$this->error('用户名不存在');
		}
		$this->assign('info',$info);
		$this->display();
	}
	public function findpassword()
	{
		$id=$_SESSION['find_user_id'];
		$answer=I('post.answer');
		$usermodel=D('User');
		$info=$usermodel->field('answer,email,validate')->find($id);
		$findPasswordTime=time();            	
        $usermodel->where("id=$id")->setField('findpassword_time',$findPasswordTime);
		if($info['answer']==$answer){
            $title='京西商场找回密码';
            $fromuser='京西商场服务部';
            $address=$info['email'];
            $key=$info['validate'];
            $url="http://www.ecshop.com".U('User/getPassword',array('email'=>$address,'key'=>$key));
            $content="尊敬的".$info['username'].":<br/><a href='$url'>找回密码</a>";
            if(sendEmail($title,$content,$fromuser,$address)){
            	$this->success('修改密码邮件发送成功',U('User/login'));
            }else{
            	$this->error('修改密码邮件发送失败，请重新找回密码');
            }
		}else{
			$this->error('答案错误，请重新输入');
		}
	}

	public function getPassword()
	{   
		$usermodel = D('User');
		if(IS_POST){
			$password = I('post.password');
			$rpassword = I('post.rsword');
			if(empty($password)){
				$this->error('密码不能为空');
			}
			if($password !=$password){
				$this->error('两次密码不一致');
			}
			$id = $_SESSION['find_user_id'];
            $usermodel->where("id=$id")->setField('password',md5($password));
            $this->success('重设密码成功',U('User/login'));exit;
		}
		$email = $_GET['email'];
        $key = $_GET['key'];
        //根据email查找出用户，如果用户不存在，则链接有误        
        $info = $usermodel->where("email='$email'")->find();
        if(!$info){
                $this->error('链接有误');
        }
        //验证链接的有效性，从表里面取出的 validate和传递的key进行比较，
        if($info['validate']!=$key){
                 $this->error('链接有误');
        }
        if(time()-$info['findpassword_time']>0.5*3600){
                 $this->error('链接失效');
        }
        $_SESSION['find_user_id'] = $info['id'];
        $this->display();
	}
	public function logout()
	{
        $_SESSION['username']=null;
        $_SESSION['user_id']=null;
        $this->success('退出成功',U('Index/index'));
    }

}

 ?>