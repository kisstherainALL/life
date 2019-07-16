<?php
namespace app\admin\controller;
use think\Db;
class Remember extends \think\Controller
{
    public function index($page=1)
    {
      if (input('keyword')!='') {
        // dump(input('keyword'));
        $keyword = input('keyword');
        // $hot = iconv("utf-8","gbk","$keyword");
        $hot = $keyword;
        $res=Db::table('ceb_Member')
            ->page($page,20)
            ->alias('m')
            ->field('m.ID,m.EnrolName,m.NickName,m.TrueName,m.Sex,m.CityName,m.CreateTime')
            ->where("MemberType=0 and EnrolName='$hot' or NickName='$hot' or TrueName='$hot'")
            ->order('CreateTime desc')
            ->select();
        
        
      }else{
        $res=DB::table('ceb_Member')
          ->page($page,20)
          ->alias('m')
          ->field('m.ID,m.EnrolName,m.NickName,m.TrueName,m.Sex,m.CityName,m.CreateTime')
          ->where("MemberType=0")
          ->order('CreateTime desc')
          ->select();
      }  
      
      foreach ($res as $k => $v) {
        $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,19);
      }
  
        $this->assign('res2',$res);
        $this->assign('page',$page);
		    return $this->fetch('');
    }

    public function fanxian($page=1)
    { 
      $where='';
      $where2='';
      if (input('keyword')||input('start')||input('end')) {
        // dump(input(''));exit;
        $start = input('start');
        $end = input('end');
        $keyword = input('keyword');
        // dump($start);
        // dump($end);
        // dump($keyword);
        
        if($start!='')
        {
            $where["a.CreateTime"]=array("egt",$start);
        }

        if($end!='')
        {
            $where["a.CreateTime"]=array("elt",$end);           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.CreateTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(a.ACode like '%". $keyword."%')";
        }

      }
      
      $res=DB::table('ceb_MoneyLog')
          ->page($page,10)
          ->alias('a')
          ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("Title='订单返现'")
          ->where($where)
          ->where($where2)
          ->order('CreateTime desc')
          ->select();
      
          //金额数据处理
          foreach ($res as $k => $v) {
            $tem=$res[$k]['TransAmount'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['TransAmount']=$sin;
              $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,19);
          }
          // dump($res);exit;
  
        $this->assign('res2',$res);
        $this->assign('page',$page);
        return $this->fetch('');
    }

    public function user_info(){
      $id=input('id');
      // dump($id);
      $res=Db::table('ceb_Member')
          ->alias('a')
          ->field('a.ID,a.EnrolName,a.Headimgurl,a.NickName,a.TrueName,a.Sex,a.CurMoney1,a.CurMoney2,a.CurMoney3,a.CreateTime,a.NvrFd2,a.NvrFd3,a.NvrFd4')
          ->where("ID='$id'")
          ->order('ID desc')
          ->find();
          //返现
          
            $tem=$res['CurMoney1'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res['CurMoney1']=$sin;
          
          //佣金
          
            $tem=$res['CurMoney2'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res['CurMoney2']=$sin;
          
          //退款
          
            $tem=$res['CurMoney3'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res['CurMoney3']=$sin;
          
      // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');
    }

    //修改
    public function edit($page=1){
      if (input('')) {
        // dump(input(''));
        $wh['ID']=input('id');
        $password=input('password');
        $nickname=input('nickname');
        $truename=input('truename');
        $one=Db::table("ceb_Member")
            ->where($wh)
            ->find();
            // dump($one);exit;
        $sec=input('sec');
        if ($password=='') {
           $res=Db::table('ceb_Member')
              ->where($wh)
              ->update(["NickName" => "$nickname","TrueName" => "$truename","Sex" => "$sec"]);
              if ($res) {
                welog($text='修改(普通会员) —— ID:'.$one['ID']);
                $this->success('修改成功',url('admin/Remember/index'));
              }else{
                $this->error('修改失败');
              }
        }else{
            $one=md5(input('password'));
            $password = strtoupper($one);
            // dump($password);exit;
            $res=Db::table('ceb_Member')
              ->where($wh)
              ->update(["NickName" => "$nickname","TrueName" => "$truename","Sex" => "$sec","Password" => "$password"]);
              if ($res) {
                welog($text='修改(普通会员)———ID:'.$one['ID']);
                $this->success('修改成功',url('admin/Remember/index'));
              }else{
                $this->error('修改失败');
              }
        }

      }

    }
    //批量删除
    public function delete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $res=Db::table('ceb_Member')->where($wh)->delete();
        if($res){
            welog($text='批量删除普通会员');
            $this->success('删除普通会员成功',url('admin/Remember/index'));
        }else{
                $this->error('删除普通会员失败 ');
        }
      }
             
    }

    //返现批量删除
    public function fxdelete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $res=Db::table('ceb_MoneyLog')->where($wh)->delete();
        if($res){
            welog($text='批量删除普通会员返现');
            $this->success('删除普通会员返现成功',url('admin/Remember/fanxian'));
        }else{
                $this->error('删除普通会员返现失败 ');
        }
      }
             
    }

    public function user_time($page=1){
      $keyword = input('keyword');
      // dump($keyword);
      // dump(input('start'));
      // dump(input('end'));exit;
      $start_time=input('start');
      $end_time=input('end');
      // $wh['MemberType']=2;
      if ($start_time!='' && $end_time=='') {
        $wh=array('MemberType' => 0,'CreateTime'=>array('gt',$start_time));
        $res =Db::table('ceb_Member')->page($page,10)->where($wh)->select();
        // dump($res);
        $this->assign('page',$page);
        $this->assign('res2',$res);
        return $this->fetch('index');

      }elseif ($start_time!='' && $end_time!='') {
        $res=Db::table('ceb_Member')
            ->page($page,10)
            ->where(" MemberType=0  and  CreateTime>='$start_time' and CreateTime<='$end_time'")
            ->select();
        $this->assign('res2',$res);
        $this->assign('page',$page);
        return $this->fetch('index');

      }elseif ($start_time=='' && $end_time!='') {
        $wh=array('MemberType' => 0,'CreateTime'=>array('lt',$end_time));
        $res =Db::table('ceb_Member')->page($page,10)->where($wh)->select();
        // dump($res);
        $this->assign('res2',$res);
        $this->assign('page',$page);
        return $this->fetch('index');

      }else{
        $res =Db::table('ceb_Member')->page($page,10)->where('MemberType=0')->order('CreateTime desc')->select();
        // $res=Db::query("select
        //   ID,
        //   NickName, 
        //   EnrolName,
        //   TrueName,
        //   Sex,
        //   CityName,
        //   CreateTime 
        //   from ceb_Member where MemberType=2");
        // $res2=\app\common\model\Func::array_iconv($res);
        // dump($res2);
        $this->assign('res2',$res);
        $this->assign('page',$page);
        return $this->fetch('index');
      }
     
     
      
   
     
     // return $this->fetch('user_list');
    }
    //返现时间查询
    public function fanxian_time($page=1){
      $keyword = input('keyword');
      // dump($keyword);
      // dump(input('start'));
      // dump(input('end'));exit;
      $start_time=input('start');
      $end_time=input('end');
      // $wh['MemberType']=2;
      if ($start_time!='' && $end_time=='') {
        $wh=array('Title' => '订单返现','a.CreateTime'=>array('gt',$start_time));
        $res=Db::table('ceb_MoneyLog')
            ->page($page,10)
            ->alias('a')
            ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
            ->join('ceb_Member m','a.MemberID=m.ID','Left')
            ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
            ->where($wh)
            ->order('CreateTime desc')
            ->select();
        // $res =Db::table('ceb_Member')->page($page,10)->where($wh)->select();
        // dump($res);
       

      }elseif ($start_time!='' && $end_time!='') {
    

        $res=Db::table('ceb_MoneyLog')
            ->page($page,10)
            ->alias('a')
            ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
            ->join('ceb_Member m','a.MemberID=m.ID','Left')
            ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
            ->where(" Title='订单返现' and  a.CreateTime>='$start_time' and a.CreateTime<='$end_time'")
            ->order('CreateTime desc')
            ->select();     
        

      }elseif ($start_time=='' && $end_time!='') {
        $wh=array('Title' =>'订单返现','a.CreateTime'=>array('lt',$end_time));
        // $res =Db::table('ceb_Member')->page($page,10)->where($wh)->select();
        $res=Db::table('ceb_MoneyLog')
            ->page($page,10)
            ->alias('a')
            ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
            ->join('ceb_Member m','a.MemberID=m.ID','Left')
            ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
            ->where($wh)
            ->order('CreateTime desc')
            ->select();
        

      }else{
        $res=Db::table('ceb_MoneyLog')
            ->page($page,10)
            ->alias('a')
            ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
            ->join('ceb_Member m','a.MemberID=m.ID','Left')
            ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
            ->where("Title='订单返现'")
            ->order('CreateTime desc')
            ->select();
        // $res=Db::query("select
        //   ID,
        //   NickName, 
        //   EnrolName,
        //   TrueName,
        //   Sex,
        //   CityName,
        //   CreateTime 
        //   from ceb_Member where MemberType=2");
        // $res2=\app\common\model\Func::array_iconv($res);
        // dump($res2);
       
      }
     
      //金额数据处理
      foreach ($res as $k => $v) {
        $tem=$res[$k]['TransAmount'];
        if (substr($tem,0,1)==".") {
          $tem="0".$tem;
        }
          $sin=sprintf("%1\$.2f",$tem);
          $res[$k]['TransAmount']=$sin;
      }
             
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('fanxian');
   
     
     // return $this->fetch('user_list');
    }

    public function food_cate(){
       $res=Db::query("select * from ceb_Dictionary where Left(Code,4) = '0150' order by ID  desc");
      
      // $wh['Code']=array('like',array("0150".'%'));
      // $wh[substr($res['Code'], 0, 3)]='015';
      // $res=DB::table('ceb_Dictionary')
      //     // ->alias('d')
      //     // ->field('d.Code')
      //     // ->join('ceb_ShopMain s','a.ShopID=s.ID')
      //     ->where($wh)
      //     ->order('ID desc')
      //     ->select();
          // select * from ceb_Coupon where 1=1 and Left(Coupon_MemberID,2) = '17'
          // dump($res);exit;
          // substr("ABCDEFG", 0, 3);
      $this->assign('res2',$res);
      return $this->fetch('');
    }

    //菜品列表
    public function food_list($page=1){
      $res=DB::table('ceb_Product')
          ->page($page,10)
          ->alias('p')
          ->field('p.ID,P.Title,Price,P.SaleNum,s.Name,p.Status,p.TimeFd1')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where("Type=0")
          ->order('ID desc')
          ->select();
          // dump($res);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');


    }

    public function goods_info(){
      // dump(input(''));
      $wh['a.ID']=input('id');
      // $cate=Db::query("SELECT * FROM [dbo].[ceb_Dictionary] where Code like '003%' ");
      // dump($cate);
      // foreach ($cate as $k => $v) {
      //   $cata
      // }
      $res=DB::table('ceb_Product')
          ->alias('a')
          ->field('a.*,s.Name')
          ->join('ceb_ShopMain s','a.ShopID=s.ID')
          ->where($wh)
          ->find();
      // dump($res);exit;
      $first_cate=substr($res['ProductClass'],0,6);
      $sec_cate=substr($res['ProductClass'],0,9);
      // $one="SELECT * FROM [dbo].[ceb_Dictionary] where Code = '$first_cate'";
      $one_cate=DB::table('ceb_Dictionary')->where("Code = '$first_cate'")->find();
      if ($first_cate!=$sec_cate) {
          $second_cate=DB::table('ceb_Dictionary')->where("Code = '$sec_cate'")->find();
      }else{
        $second_cate='';
      }
      
      // dump($one_cate);
      // dump($second_cate);exit;
  
     $this->assign('res2',$res);
     $this->assign('one_cate',$one_cate);
     $this->assign('second_cate',$second_cate);
     return $this->fetch('');
    }

    public function getData(){
        $res=Db::query("SELECT * FROM [dbo].[ceb_Dictionary] where Code like '003%' ");
        
        $arr=array(); // 一级和二级的数组
        // 获取一级分类
        for ($i=0; $i <count($res) ; $i++)
        { 
            $temp1=$res[$i]['Code'];
            if(strlen($temp1)==6)
              {
                  $arr[$temp1]=array();
              }
        }
        // 获取而二级分类
        for ($i=0; $i <count($res) ; $i++)
        { 
            $temp2=$res[$i]['Code'];
            if(strlen($temp2)==9 and substr($temp2, 0,3)=="003")
              {
                 if (isset($arr[substr($temp2, 0,6)])) {array_push($arr[substr($temp2, 0,6)], $temp2);}
              }
        }
       dump($arr);
       

        die();
      $res=DB::table('ceb_Product')
          ->alias('a')
          ->field('a.*,s.Name')
          ->join('ceb_ShopMain s','a.ShopID=s.ID')
          ->where($wh)
          ->find();
      // dump($res);exit;
     $this->assign('res2',$res);
     die($res['ProductClass']);

      $data2=array("2-1","2-2","3-2");
      $data['test']=$data2;
      $data['test2']=$data2;
      echo json_encode($data);
    }

  

}
