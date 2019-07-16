<?php
namespace app\admin\controller;
use think\Db;
class Menu extends \think\Controller
{
    public function index()
    {
      dump("天下第一帅");
      

		  // return $this->fetch('index');
    }

    
    

    
    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

  

}
