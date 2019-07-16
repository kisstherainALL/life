<?php
namespace app\admin\controller;
use think\Db;
class Index extends \think\Controller
{
    public function index()
    {
     
      $wh['ID']=SESSION('gg_uid');
      $user_pri=Db::table("ceb_User")->field('WePrivilegeID')->where($wh)->find();
      $where['ID']=$user_pri['WePrivilegeID'];
      $pri=Db::table("ceb_WePrivilege")->where($where)->select();
      $all=$pri[0]['Privilege'];
      $Menu_wh['ID'] = array('in',$all);
      $Menu_wh['is_show']="0";

      $tempArr=array();//定义一个空数组储存整理后的新数组
      //添加一级分类到数组
      $res=Db::table("ceb_WeMenu")->where($Menu_wh)->select();
      for ($i=0; $i < count($res) ; $i++) { 
         if($res[$i]["pid"]==0)
         {
            array_push($tempArr, $res[$i]);
         }
      }
     //添加二级分类到数组
     for ($x=0; $x < count($tempArr) ; $x++) 
     { 
         for ($y=0; $y < count($res) ; $y++)
         { 
            if($res[$y]["pid"]==$tempArr[$x]["ID"])
            {
              $tempArr[$x]["son"][]=$res[$y];
            }
         }
     }

     $controller = request()->controller();
     $action = request()->action();
     // dump($controller);
     // dump($action);exit;
      $this->assign('controller',$controller);
      $this->assign('pri',$pri);
      $this->assign('tempArr',$tempArr);
		  return $this->fetch('index');
    }

    public function welcome()
    {
      $res=DB::table('ceb_WeLog')
          ->order('c_time desc')
          ->find();
      
      $res['c_time']=substr($res['c_time'],0,19);
      
      $this->assign('res',$res);
      return $this->fetch('welcome');
    }

    public function user_list(){
      $res=\think\Db::table("ceb_Member")->where('MemberType=2')->select();
      // $res2=\app\common\model\Func::array_iconv($res);
      $this->assign('res2',$res);
      return $this->fetch('');
    }

    public function user_examine(){
   
      // $res=Db::query('select * from ceb_ApproveInfo join ceb_Member on ceb_ApproveInfo.MemberID=ceb_Member.ID where ceb_ApproveInfo.type=1');
      $res=Db::query('select * from ceb_ApproveInfo join ceb_Member on ceb_ApproveInfo.MemberID=ceb_Member.ID join ceb_ShopMain on ceb_ShopMain.MemberID=ceb_Member.ID where ceb_ApproveInfo.type=1');     

      $res2=\app\common\model\Func::array_iconv($res);
      $this->assign('res2',$res2);
      return $this->fetch('');
    }

    public function goods_list(){
   
      $res=Db::table("ceb_Product")->where('Type=1')->select();
      $res2=\app\common\model\Func::array_iconv($res);
      $this->assign('res2',$res2);
     return $this->fetch('');
    }

    public function goods_examine(){
   
     
     return $this->fetch('');
    }

    public function user_info(){
      dump(123123);
   
     
     // return $this->fetch('');
    }
    
    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

    public function get_category($data,$pid = 0,$level = 1)
    {
        if(!isset($data['old'])){
            $da['old'] = $data;//用来循环的数据
            $da['new'] = array();//记录循环好的新数据
            $data = $da;
            unset($da);
        }
        foreach ($data['old'] as $k => $v) {
            if($v['pid'] == $pid){
                $v['level'] = $level;
                // var_dump($v);exit;
                $data['new'][$v['ID']] = $v;
                unset($data['old'][$k]);//把当前选中分类清除 因为我自己不可能是自己的分类
                $son = $this->get_category($data,$v['ID'],$level+1);
                if($son){
                    $data['new'] = $son;
                }
            }
        }
        return $data['new'];
    }

  

}
