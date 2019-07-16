<?php
namespace app\admin\controller;
use think\Db;
class Privilege extends \think\Controller
{
    public function index()
    {
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
        // dump(input(''));exit;
        $keyword = input('keyword');
        // dump($start);
        // dump($end);
        // dump($keyword);
        $this->assign('keyword',$keyword);
        
        if($keyword!='')
        {
            $where2="(Name like '%". $keyword."%')";
        }

        }

      $where['ID']=SESSION('gg_uid');
      $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($where)->find();
      $res=Db::table("ceb_WePrivilege")->where($where2)->order('C_time desc')->select();
      if ($user_pri['WePrivilegeID']!='19') {
        foreach ($res as $k => $v) {
          if ($res[$k]['ID']=='19') {
            unset($res[$k]);
          }
        }
      }
      // dump($res);exit;
      $num=count($res);
      $this->assign('num',$num);
      $this->assign('res',$res);
      return $this->fetch('index');
    }

    public function add()
    { 
      // dump(input(''));
      
      if (input('')) {
         // dump(input(''));
        $data = input('');
        // if (!input('name')) {
        //    $this->error('请填写权限名称');
        // }
        // if (!isset(input('privilege')) {
        //    $this->error('请选择权限');
        // }
        
        $name = $data['name'];

        $privilege = implode($data['privilege'], ',');
        // dump($privilege);exit;
        $c_time = $u_time = date("Y-m-d H:i:s",time());
        $data = ["Name" => "$name","Privilege" => "$privilege", "C_time" => "$c_time", "U_time" => "$u_time"];
        $res=Db::table("ceb_WePrivilege")->insert($data);
        if($res){
          welog($text='添加权限'.' —— '.$name);
          $this->success('添加权限成功',url('admin/Privilege/index'));
        }else{
          $this->error('添加权限失败 ');
        }
      }else{

        $where['ID']=SESSION('gg_uid');
        $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($where)->find();
        // dump($user_pri);exit;
        if ($user_pri['WePrivilegeID']=='19') {
          $res=Db::table("ceb_WeMenu")->where('Pid=0 and is_show=0')->select();
          foreach ($res as $k => $v) {
          $wh['Pid'] = $res[$k]['ID'];
          $wh['is_show']="0";
          if ($wh['Pid']!='') {
            $menu=Db::table("ceb_WeMenu")->where($wh)->select();
          }
          $res[$k]['son']=$menu;
          // dump($menu);
          }
        }else if($user_pri['WePrivilegeID']!='19'){
          $wh['ID']=SESSION('gg_uid');
          $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($wh)->find();
          $where['ID']=$user_pri['WePrivilegeID'];
          $pri=Db::table("ceb_WePrivilege")->where($where)->select();
          $all=$pri[0]['Privilege'];
          $Menu_wh['ID'] = array('in',$all);
          $Menu_wh['is_show']="0";

          $res=array();//定义一个空数组储存整理后的新数组
          //添加一级分类到数组
          $tem=Db::table("ceb_WeMenu")->where($Menu_wh)->select();
          for ($i=0; $i < count($tem) ; $i++) { 
             if($tem[$i]["pid"]==0)
             {
                array_push($res, $tem[$i]);
             }
          }
          //添加二级分类到数组
          for ($x=0; $x < count($res) ; $x++) 
          { 
             for ($y=0; $y < count($tem) ; $y++)
             { 
                if($tem[$y]["pid"]==$res[$x]["ID"])
                {
                  $res[$x]["son"][]=$tem[$y];
                }
             }
          }
        }

        
        $this->assign('res',$res);
        return $this->fetch('');

      }
      
    }

    public function edit()
    {
      $id = input('id');
      
      if (input('name')) {
        // dump(input(''));
        $data = input('');
        $wh['ID']=$data['id'];
        $name = $data['name'];
        $privilege = implode($data['privilege'], ',');
        $c_time = $u_time = date("Y-m-d H:i:s",time());

        $one=Db::table("ceb_WePrivilege")
            ->where($wh)
            ->find();
        $res=Db::table('ceb_WePrivilege')
              ->where($wh)
              ->update(["Name" => "$name","Privilege" => "$privilege","U_time" => "$c_time"]);
              if ($res) {
                welog($text='修改权限'.' —— '.$one['Name']);
                $this->success('修改成功',url('admin/Privilege/index'));
              }else{
                $this->error('修改失败');
              }

      }else{

        $where['ID']=SESSION('gg_uid');
        $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($where)->find();
        if ($user_pri['WePrivilegeID']=='19') {
          $data=Db::table("ceb_WePrivilege")->where("ID='$id'")->find();//判定选中
          $res=Db::table("ceb_WeMenu")->where('Pid=0 and is_show=0')->select();
          foreach ($res as $k => $v) {
          $wh['Pid'] = $res[$k]['ID'];
          $wh['is_show']="0";
          if ($wh['Pid']!='') {
            $menu=Db::table("ceb_WeMenu")->where($wh)->select();
          }
          $res[$k]['son']=$menu;
          // dump($menu);
          }
        }else if($user_pri['WePrivilegeID']!='19') {
          $data=Db::table("ceb_WePrivilege")->where("ID='$id'")->find();//判定选中
          $wh['ID']=SESSION('gg_uid');
          $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($wh)->find();
          $where['ID']=$user_pri['WePrivilegeID'];
          $pri=Db::table("ceb_WePrivilege")->where($where)->select();
          $all=$pri[0]['Privilege'];
          $Menu_wh['ID'] = array('in',$all);
          $Menu_wh['is_show']="0";

          $res=array();//定义一个空数组储存整理后的新数组
          //添加一级分类到数组
          $tem=Db::table("ceb_WeMenu")->where($Menu_wh)->select();
          for ($i=0; $i < count($tem) ; $i++) { 
             if($tem[$i]["pid"]==0)
             {
                array_push($res, $tem[$i]);
             }
          }
          //添加二级分类到数组
          for ($x=0; $x < count($res) ; $x++) 
          { 
             for ($y=0; $y < count($tem) ; $y++)
             { 
                if($tem[$y]["pid"]==$res[$x]["ID"])
                {
                  $res[$x]["son"][]=$tem[$y];
                }
             }
          }
        }
        
        // dump($res);exit;
        $this->assign('data',$data);
        $this->assign('res',$res);
        return $this->fetch(''); 
      }

    }

    public function delete()
    {
      if (input('')) {
        $id = input('');
        // $privilege=implode(',', $privilege);
        // dump($name);
        // dump($privilege);
        // dump(input(''));exit;
        // dump($id);exit;
          $wh['ID']=input('id');
          if ($wh['ID']=='19') {
            $this->error('该权限为“超级管理员”，无法对其进行删除操作');
          }
          $one=Db::table("ceb_WePrivilege")
            ->where($wh)
            ->find();

          $res=Db::table('ceb_WePrivilege')->where($wh)->delete();
          if($res){
            welog($text='修改权限'.'——'.$one['Name']);
            $this->success('删除权限成功',url('admin/Privilege/index'));
          }else{
            $this->error('删除权限失败 ');
          }
      }  
    }
    
    public function delete_all()
    {
      if (input('')) {
        $id = input('');
        
        // dump($id);exit;
        $one=implode($id['id'], ',');
        $c_arr = explode(',',$one);
        $a=19;
        // dump($c_arr);
        // dump($one);exit;
        $wh['ID'] = array('in',$one);
        if (in_array($a,$c_arr)) {
          $this->error('不能删除“超级管理员权限组”');
        }
        $res=Db::table('ceb_WePrivilege')->where($wh)->delete();
        if($res){
            welog($text='批量删除权限成功');
            $this->success('批量删除权限成功',url('admin/Privilege/index'));
        }else{
                $this->error('批量删除权限失败 ');
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
