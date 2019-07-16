<?php
namespace app\admin\controller;
use think\Db;
class RestaurantMember extends \think\Controller
{
    public function index($page=1)
    {
      $res=DB::table('ceb_Member')
          ->page($page,10)
          ->alias('m')
          ->field('m.ID,m.EnrolName,m.NickName,m.TrueName,m.Sex,m.CityName,m.CreateTime')
          ->where("MemberType=1")
          ->order('CreateTime desc')
          ->select();
          foreach ($res as $k => $v) {
            $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,16);
          }
          // dump($res);
  
        $this->assign('res2',$res);
        $this->assign('page',$page);
		    return $this->fetch('');
    }

    public function examine($page=1)
    {
      $res=DB::table('ceb_ApproveInfo')
            ->page($page,10)
            ->alias('a')
            ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
            ->join('ceb_Member m','a.MemberID=m.ID')
            ->join('ceb_ShopMain s','s.MemberID=m.ID')
            ->where('a.type=0')
            ->order('ID desc')
            ->select();
            // dump($res);exit;
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');      
    }

    //会员修改
    public function examine_edit($page=1){
      if (input('')) {
        // dump(input(''));
        $wh['ID']=input('id');
        $result=input('result');
        $resultdemo=input('resultdemo');
        $res=Db::table('ceb_ApproveInfo')
              ->where($wh)
              ->update(["AuthentResult" => "$result","AuthentResultDemo" => "$resultdemo"]);
              if ($res) {
               $this->success('修改成功',url('admin/RestaurantMember/examine'));
              }else{
                 $this->error('修改失败');
              }

      }
    }
    //会员验证详情
    public function examine_info(){
      $id=input('id');
      // dump($id);
      $res=Db::table('ceb_ApproveInfo')
          ->alias('a')
          ->field('a.ID,m.EnrolName,a.Name,a.ExtField1,a.ExtField2,a.ExtField3,a.Contact,a.Phone,s.DecFd2,m.NvrFd2,m.NvrFd3,m.NvrFd4,a.CreateTime,a.AuditTime,a.AuthentResult,a.AuthentResultDemo')
          ->join('ceb_Member m','a.MemberID = m.ID')
          ->join('ceb_ShopMain s','s.MemberID = m.ID')
          ->where("a.ID='$id'")
          ->order('ID desc')
          ->find();
      // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');
    }
    //返现
    public function fanxian($page=1)
    {
      // $res=DB::table('ceb_Member')
      //     // ->page($page,10)
      //     ->alias('m')
      //     ->field('m.ID,m.CreateTime')
      //     ->join('ceb_MoneyLog a','a.MemberID=m.ID')
      //     ->join('ceb_ShopMain s','s.MemberID=m.ID')
      //     // ->where("MemberType=0")
      //     ->order('CreateTime desc')
      //     ->select();
      $res=DB::table('ceb_MoneyLog')
          ->page($page,10)
          ->alias('a')
          ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("Title='订单返现' and MemberType='1'")
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
          }
          // dump($res);exit;
  
        $this->assign('res2',$res);
        $this->assign('page',$page);
        return $this->fetch('');
    }

    // public function user_info(){
    //   $id=input('id');
    //   dump($id);
    //   $res=Db::table('ceb_Member')
    //       ->alias('a')
    //       ->field('a.ID,a.EnrolName,a.Headimgurl,a.NickName,a.TrueName,a.Sex,a.CurMoney1,a.CurMoney2,a.CurMoney3,a.CreateTime,a.NvrFd2,a.NvrFd3,a.NvrFd4')
    //       ->where("ID='$id'")
    //       ->order('ID desc')
    //       ->find();
    //   // dump($res);
    //   $this->assign('res2',$res);
    //   return $this->fetch('');
    // }

     public function member_info(){
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
        $sec=input('sec');
        if ($password=='') {
           $res=Db::table('ceb_Member')
              ->where($wh)
              ->update(["NickName" => "$nickname","TrueName" => "$truename","Sex" => "$sec"]);
              if ($res) {
               $this->success('修改成功',url('admin/RestaurantMember/index'));
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
               $this->success('修改成功',url('admin/User/user_list'));
              }else{
                 $this->error('修改失败');
              }
        }

      }
    }

    public function welcome()
    {
      return $this->fetch('welcome');
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

    //结算
    public function settlement($page=1)
    {
      $res=DB::table('ceb_JieSuanLog')
          ->page($page,10)
          ->alias('a')
          // ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->field('a.ID,s.Name,a.JieMoneylogBeginTime,a.JieMoneylogEndTime,a.IsPay,a.JieSuanMoney,a.Status,a.JieSuanPeople,a.JieSuanTime')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("MemberType='1'")
          ->order('ID desc')
          ->select();
          //结算金额数据处理
          foreach ($res as $k => $v) {
            $tem=$res[$k]['JieSuanMoney'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['JieSuanMoney']=$sin;
          }

          foreach ($res as $k => $v) {
            $res[$k]['JieMoneylogBeginTime']=substr($res[$k]['JieMoneylogBeginTime'],0,16);
            $res[$k]['JieMoneylogEndTime']=substr($res[$k]['JieMoneylogEndTime'],0,16);
            $res[$k]['JieSuanTime']=substr($res[$k]['JieSuanTime'],0,16);
          }    
        // dump($res);exit;
        $this->assign('res2',$res);
        $this->assign('page',$page);
        return $this->fetch('');
    }
    //结算管理详情
    public function settlement_info()
    {
      // dump(input(''));
      $wh['a.ID']=input('id');
      $id=input('id');
      // dump($wh);
      $res=DB::table('ceb_JieSuanLog')
          ->alias('a')
          // ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->field('a.*,s.Name,m.EnrolName')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where($wh)
          ->order('ID desc')
          ->find();
          
            $res['JieMoneylogBeginTime']=substr($res['JieMoneylogBeginTime'],0,16);
            $res['JieMoneylogEndTime']=substr($res['JieMoneylogEndTime'],0,16);
            $res['JieSuanTime']=substr($res['JieSuanTime'],0,16);
            
            if ($res['IsPay']=='0') {
              $res['IsPay']="未付";
            }elseif ($res['IsPay']=='1') {
              $res['IsPay']="已付";
            }

      $data= DB::table('ceb_Order') 
            ->alias('o')
            ->field('o.ID,o.OrderCode,o.Total,o.tj_yunyingjine,o.tj_pingtaifuwufei,o.tj_fangxian,o.tj_yongjin,o.tj_feilv,o.Status,j.JieSuanMoney,j.Status as jStatus,o.TimeFd4,s.DecFd2,j.IsPay')
            ->join('ceb_JieSuanLog j','o.JieSuanID=j.ID','Left')
            ->join('ceb_ShopMain s','o.ShopID=s.ID','Left')
            ->where("JieSuanID='$id' and o.Status='已完成'")
            ->order('ID desc')
            ->select();  
            foreach ($data as $k => $v) {
              $pay=$data[$k]['IsPay'];
              $need=$data[$k]['jStatus'];
              $yun=$data[$k]['tj_yunyingjine']-0;
              $zong=$data[$k]['Total']-0;
              $felv=$data[$k]['tj_feilv']-0;
              $DecFd2=$data[$k]['DecFd2']-0;
              $per=$DecFd2/100;
              $data[$k]['jiesuan']=(($zong-($zong*$per))-$zong*$felv/100);
              if ($need=="审核不通过") {
                $data[$k]['zhuantai']="申请中";
              }elseif ($need=="审核通过" && $pay=='0') {
                $data[$k]['zhuantai']="待付款";
              }elseif ($need=="审核通过" && $pay=='1') {
                $data[$k]['zhuantai']="已付";
              }elseif ($need=="未处理") {
                $data[$k]['zhuantai']="申请中";
              }
              $data[$k]['TimeFd4']=substr($data[$k]['TimeFd4'],0,16);
            }
            //数据处理
            foreach ($data as $k => $v) {
              $tem=$data[$k]['tj_yunyingjine'];
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
              }
                $sin=sprintf("%1\$.2f",$tem);
                $data[$k]['tj_yunyingjine']=$sin;
            }
            //数据处理
            foreach ($data as $k => $v) {
              $tem=$data[$k]['tj_pingtaifuwufei'];
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
              }
                $sin=sprintf("%1\$.2f",$tem);
                $data[$k]['tj_pingtaifuwufei']=$sin;
            }
            //数据处理
            foreach ($data as $k => $v) {
              $tem=$data[$k]['tj_fangxian'];
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
              }
                $sin=sprintf("%1\$.2f",$tem);
                $data[$k]['tj_fangxian']=$sin;
            }
            //数据处理
            foreach ($data as $k => $v) {
              $tem=$data[$k]['tj_yongjin'];
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
              }
                $sin=sprintf("%1\$.2f",$tem);
                $data[$k]['tj_yongjin']=$sin;
            }
            //数据处理
            foreach ($data as $k => $v) {
              $tem=$data[$k]['tj_feilv'];
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
              }
                $sin=sprintf("%1\$.2f",$tem);
                $data[$k]['tj_feilv']=$sin;
            }

      // dump($data);
      $this->assign('res2',$res);
      $this->assign('data',$data);
      return $this->fetch('');   
    }
    //结算管理修改
    public function settlement_edit()
    {
      if (input('')) {
        // dump(input(''));exit;
        $wh['ID']=input('id');
        $ispay=input('ispay');
        $sec=input('sec');
        $beizhu=input('beizhu');

        if (input('men')==''&&input('ispay')=='1') {
          $name=session('gg_uname');
          // dump($name);
          $res=Db::table('ceb_JieSuanLog')
              ->where($wh)
              ->update(["IsPay" => "$ispay","JieSuanPeople" => "$name","Status" => "$sec","NvrFd1" => "$beizhu"]);
              if ($res) {
               $this->success('修改成功',url('admin/RestaurantMember/settlement'));
              }else{
                 $this->error('修改失败');
              }
        }else{

        }

         $res=Db::table('ceb_JieSuanLog')
              ->where($wh)
              ->update(["IsPay" => "$ispay","Status" => "$sec","NvrFd1" => "$beizhu"]);
              if ($res) {
               $this->success('修改成功',url('admin/RestaurantMember/settlement'));
              }else{
                 $this->error('修改失败');
              }
        
      }
    }

    public function commission($page=1)
    {
      $res=DB::table('ceb_MoneyLog')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("Title='分销者佣金' and MemberType='1'")
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

  

}
