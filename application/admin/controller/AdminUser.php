<?php
namespace app\admin\controller;
use think\Db;
class AdminUser extends \think\Controller
{
    public function index($page=1)
    {
      $where['ID']=SESSION('gg_uid');
      $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($where)->find();
      $res=Db::table('ceb_User')
              ->page($page,10)
              ->alias('u')
              ->field('u.ID,u.UserName,U.Remark,s.Name,s.ID AS SID')
              ->join('ceb_WePrivilege s','u.WePrivilegeID = s.ID','left')
              ->select();
      if ($user_pri['WePrivilegeID']!='19') {
        foreach ($res as $k => $v) {
          if ($res[$k]['SID']=="19") {
            unset($res[$k]);
          }
        }
      }        
      // dump($res);exit;
      $this->assign('res',$res);
      return $this->fetch('index');
    }

    public function add()
    {
      if (input('')) {
        $data = input('');
        $username = $data['username'];
        $password = $data['password'];
        $repassword = $data['repassword'];
        $name = $data['name'];
        $privilege = $data['privilege'];
        $beizhu = $data['beizhu'];
        // $privilege = implode($data['privilege'], ',');
        $LastTime = $EnrolTime = date("Y-m-d H:i:s",time());
        // $data = ['foo' => 'bar', 'bar' => 'foo'];
        if ($username=='') {
          $this->error('请填写用户名');
        }
        if ($password!=$repassword) {
          $this->error('确认密码不一致');
        }else{
          $password = md5($password);
          if ($password) {
            $password = strtoupper($password);
          }
        }
        if ($username=='') {
          $this->error('选择权限');
        }
        if ($name=='') {
          $this->error('请填写真实姓名');
        }
        $data = ["UserName" => "$username", "Password" => "$password", "Name" => "$name", "LastTime" => "$LastTime", "EnrolTime" => "$EnrolTime","Remark" => "$beizhu","WePrivilegeID" => "$privilege"];
        // dump($password);exit;
        $res=Db::table("ceb_User")->insert($data);
        // Db::table('think_user')->insert($data);
        if($res){
            welog($text='添加用户'.' — 账号：'.$username.' — 真实姓名：'.$name);
            $this->success('添加用户成功',url('admin/AdminUser/index'));
        }else{
                $this->error('添加用户失败 ');
        }
      
      }
      $where['ID']=SESSION('gg_uid');
      $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($where)->find();
      $res=Db::table('ceb_WePrivilege')
          ->select();
      if ($user_pri['WePrivilegeID']!='19') {
        foreach ($res as $k => $v) {
          if ($res[$k]['ID']=='19') {
            unset($res[$k]);
          }
        }
      }    
      // dump($res);exit;
      $this->assign('res',$res);
      return $this->fetch('');
    }
   
    public function edit(){
      if (input('username')) {
        // dump(input(''));
        $wh['ID']=input('id');
        $username=input('username');
        $password=input('password');
        $repassword=input('repassword');
        $name=input('name');
        $privilege=input('privilege');
        $beizhu=input('beizhu');
        $one=Db::table("ceb_User")
            ->where($wh)
            ->find();

        if ($password=='') {
          $res=Db::table('ceb_User')
              ->where($wh)
              ->update(["UserName" => "$username","Name" => "$name","Remark" => "$beizhu","WePrivilegeID" => "$privilege"]);
              if ($res) {
                welog($text='修改用户'.' — 账号：'.$one['UserName'].' — 真实姓名：'.$one['Name']);
               $this->error('修改成功',url('admin/AdminUser/index'));
              }else{
                 $this->error('修改失败');
              }

        }elseif ($password!='') {
          if ($password!=$repassword) {
            $this->error('确认密码不一致');
          }else{
            $one=md5(input('password'));
            $password = strtoupper($one);
            $tem=Db::table("ceb_User")
                ->where($wh)
                ->find();
            // dump($password);exit;
            $res=Db::table('ceb_User')
              ->where($wh)
              ->update(["UserName" => "$username","Password" => "$password","Name" => "$name","Remark" => "$beizhu","WePrivilegeID" => "$privilege"]);
              if ($res) {
                welog($text='修改用户'.' — 账号：'.$tem['UserName'].' — 真实姓名：'.$tem['Name']);
               $this->error('修改成功',url('admin/AdminUser/index'));
              }else{
                 $this->error('修改失败');
              }
          }
           
        }

      }else{
        $where=input('');
        $wh['u.ID']=input('id');
        $data=Db::table('ceb_User')
          ->alias('u')
          ->field('u.ID,u.UserName,u.Password,u.Remark,u.Name as uName,u.WePrivilegeID,s.Name')
          ->join('ceb_WePrivilege s','u.WePrivilegeID = s.ID','left')
          ->where($wh)
          ->find();

        $where['ID']=SESSION('gg_uid');
        $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($where)->find();
        $res=Db::table('ceb_WePrivilege')
          ->select();
        if ($user_pri['WePrivilegeID']!='19') {
          foreach ($res as $k => $v) {
            if ($res[$k]['ID']=='19') {
              unset($res[$k]);
            }
          }
        }
          $this->assign('data',$data);
          $this->assign('res',$res);
          return $this->fetch('');
      }
        
          
        

      // exit;
      // dump($data);exit;
      
        
    }

    public function delete(){
     if (input('id')) {
      $wh['ID']=input('id');
      $one=Db::table("ceb_User")
            ->where($wh)
            ->find();
       $res=Db::table('ceb_User')->where($wh)->delete();
       if ($res) {
          welog($text='删除用户'.' — 账号：'.$one['UserName'].' — 真实姓名：'.$one['Name']);
          $this->error('删除成功',url('admin/AdminUser/index'));
        }else{
          $this->error('删除失败');
        }
     }
             
    }

    public function delete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $all=Db::table('ceb_User')->where($wh)->select();
        foreach ($all as $k => $v) {
          $user[]=$all[$k]['UserName'];
          $trun[]=$all[$k]['Name'];
        }
        $u_name=implode($user, ',');
        $u_trun=implode($trun, ',');
        $res=Db::table('ceb_User')->where($wh)->delete();
        if($res){
            welog($text='批量删除用户'.'ID:'.$one.' — 用户名:'.$u_name.' — 真实姓名:'.$u_trun);
            $this->success('删除用户成功',url('admin/AdminUser/index'));
        }else{
                $this->error('删除用户失败 ');
        }
      }
             
    }

    
    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

  

}
