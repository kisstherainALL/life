<?php
namespace app\admin\controller;
use think\Db;
class Login extends \think\Controller
{
    public function index()
    {
        if (input('')) {
            dump('123');
        }
  

		  return $this->fetch('');
    }

    public function login()
    {
        $username=input('username');
        $key=md5(input('password'));
        $verify=input('verify');
        $password=strtoupper($key);
        $wh['username'] = $username;
        // dump($username);
        // dump($password);
        // dump($verify);
        // dump(input(''));exit;
        if (empty($username)) {
            $this->error('用户名不得为空');//rzkjadmin
        }

        if (empty(input('password'))) {
            $this->error('密码不得为空');
        }

        if(!captcha_check($verify)){
         $this->error('验证码错误');
        }
        $one=Db::table('ceb_User')
            ->alias('u')
            ->where($wh)
            ->field('u.*,p.Name as pname')
            ->join('ceb_WePrivilege p','u.WePrivilegeID=p.ID')
            ->find();
            // dump($one);exit;
        if ($one && $one['Password'] == $password) {
            session('gg_uid',$one['ID']);
            session('gg_uname',$username);
            session('gg_privlege',$one['pname']);
            // dump($_SESSION);exit;
           
            welog($text="登录瑞紫运营中心");            
            $this->success('登录成功',url('admin/Index/index'));
        }else{
            session(null);
            $this->error('用户名或密码错误');
        }

        log("登录瑞紫运营中心");
    }

    public function logout(){        
        session(null);
        $this->success('安全退出成功',url('admin/Login/index'));
    }

    public function code(){
       


        // $Verify = new \Think\Verify();
        // $Verify->useImgBg = true;
        // $Verify->length = 4;
        // $Verify->imageW = 169;
        // $Verify->imageH = 42;
        // $Verify->entry();


    }

}
