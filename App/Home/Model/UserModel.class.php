<?php 
namespace Home\Model;
use Think\Model;
class UserModel extends Model
{
	protected $_validate=array(
        array('username','require','用户名不能为空'),
        array('username','','用户名已存在',1,'unique'),
        array('username','/^[\w]{3,20}$/','用户名不合法',1,'regex'),
        array('password','require','密码不能为空'),
        array('password','6,20','密码长度不合法',1,'length'),
        array('rpassword','password','两次密码不一致',1,'confirm'),
        array('email','require','邮箱不能为空'),
        array('email','','邮箱已存在',1,'unique'),
        array('email','email','邮箱格式不合法'),
        array('question','require','问题不能为空'),
        array('answer','require','答案不能为空'),
		);
	public function login(){
                //接收传递用户名和密码
                $username = I('post.username');
                $password  = I('post.password');
                //取出数据
                $info = $this->where("username='$username'")->find();
                if($info){
                        //判断用户是否激活
                        if($info['active']==0){
                                $this->error='用户没有激活，则无法登录';
                                return false;
                        }
                        //说明有该用户
                        if($info['password']==md5($password)){
                                    //登录成功
                                    $_SESSION['username']=$username;
                                    $_SESSION['user_id']=$info['id'];
                                    return true;
                        }
                }
                $this->error='用户名或密码错误';
                return false;
        }
    public $_login_validate = array(
        array('admin_name','require','管理员名称不能为空'), 
        array('password','require','密码不能为空'),
        array('checkcode','require','验证码不能为空'),

        array('checkcode','check_verify','验证码输入错误',1,'callback'), 
		);
    protected function check_verify($code, $id = '')
	{    
		$verify = new \Think\Verify();    
		return $verify->check($code, $id);
    }
}
 ?>