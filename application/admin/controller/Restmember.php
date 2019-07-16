<?php
namespace app\admin\controller;
use think\Db;
class Restmember extends \think\Controller
{
    public function index($page=1)
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
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('m.CreateTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where=array('m.CreateTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('m.CreateTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(m.EnrolName like '%". $keyword."%' or m.NickName like '%". $keyword."%' or m.TrueName like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_Member')
          ->page($page,20)
          ->alias('m')
          ->field('m.ID,m.EnrolName,m.NickName,m.TrueName,m.Sex,m.CityName,m.CreateTime')
          ->where("MemberType","1")
          ->where($where)
          ->where($where2)
          ->order('CreateTime desc')
          ->select();
          foreach ($res as $k => $v) {
            $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,16);
          }
      $tem=DB::table('ceb_Member')
          ->alias('m')
          ->field('m.ID,m.EnrolName,m.NickName,m.TrueName,m.Sex,m.CityName,m.CreateTime')
          ->where("MemberType","1")
          ->where($where)
          ->where($where2)
          ->order('CreateTime desc')
          ->select();
      $member_num=count($tem);
      $tem_page=$member_num/20;
      $lastpage=ceil($tem_page);
        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('member_num',$member_num);
        $this->assign('lastpage',$lastpage);
        return $this->fetch('');
    }

    public function examine($page=1)
    {
      $where='';
      if (input('keyword')||input('state')) {
       
        $keyword = input('keyword');
        $state = input('state');

        if($keyword!='')
        {
            $where="(m.EnrolName like '%". $keyword."%' or a.Name like '%". $keyword."%' or a.Contact like '%". $keyword."%')";
        }
        if($state!='')
        {
            $where="(a.type=0 and a.AuthentResult='$state')";
        }

      }
      $res=DB::table('ceb_ApproveInfo')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
          ->join('ceb_Member m','a.MemberID=m.ID')
          ->join('ceb_ShopMain s','s.MemberID=m.ID')
          ->where('a.type','0')
          ->where($where)
          ->order('AuthentResult asc , ID desc')
          ->select();
      $tem=DB::table('ceb_ApproveInfo')
          ->alias('a')
          ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
          ->join('ceb_Member m','a.MemberID=m.ID')
          ->join('ceb_ShopMain s','s.MemberID=m.ID')
          ->where('a.type','0')
          ->where($where)
          ->order('ID desc')
          ->select();
      $member_num=count($tem);
      
      foreach ($res as $k => $v) {
        $res[$k]['AuditTime']=substr($res[$k]['AuditTime'],0,19);
      }
      $tem_page=$member_num/20;
      $lastpage=ceil($tem_page);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('member_num',$member_num);
      $this->assign('lastpage',$lastpage);
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
               $this->success('修改成功',url('admin/Restmember/examine'));
              }else{
                 $this->error('修改失败');
              }

      }
    }


    //验证刷选
    public function user_state($page=1){
      $keyword = input('keyword');
      
      if (input('keyword')!='') {
        // dump(input('keyword'));
        // $hot = iconv("utf-8","gbk","$keyword");
        $hot = $keyword;
        $res=DB::table('ceb_ApproveInfo')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
            ->join('ceb_Member m','a.MemberID=m.ID')
            ->join('ceb_ShopMain s','s.MemberID=m.ID')
            ->where("a.type=0 and a.AuthentResult='$hot'")
            ->order('ID desc')
            ->select();
        $tem=DB::table('ceb_ApproveInfo')
            ->alias('a')
            ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
            ->join('ceb_Member m','a.MemberID=m.ID')
            ->join('ceb_ShopMain s','s.MemberID=m.ID')
            ->where("a.type=0 and a.AuthentResult='$hot'")
            ->order('ID desc')
            ->select();
        $member_num=count($tem);
      }else{
        $res=DB::table('ceb_ApproveInfo')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
            ->join('ceb_Member m','a.MemberID=m.ID')
            ->join('ceb_ShopMain s','s.MemberID=m.ID')
            ->where('a.type=0')
            ->order('ID desc')
            ->select();
        $tem=DB::table('ceb_ApproveInfo')
            ->alias('a')
            ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
            ->join('ceb_Member m','a.MemberID=m.ID')
            ->join('ceb_ShopMain s','s.MemberID=m.ID')
            ->where('a.type=0')
            ->order('ID desc')
            ->select();
        $member_num=count($tem);
      }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('member_num',$member_num);
      return $this->fetch('examine');

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
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('a.JieSuanTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.JieSuanTime"]=array("elt",$end); 
            $where=array('a.JieSuanTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.JieSuanTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_MoneyLog')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("Title='订单返现' and MemberType='1'")
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
               $this->success('修改成功',url('admin/Restmember/index'));
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
          ->page($page,20)
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
      $where='';
      $where1='';
      $where2='';
      $where3='';
      if (input('keyword')||input('start')||input('end')||input('payment_state')||input('pay_state')) {
        // dump(input(''));exit;
        $start = input('start');
        $end = input('end');
        $keyword = input('keyword');
        $payment_state = input('payment_state');
        $pay_state = input('pay_state');
        if ($pay_state=='fou') {
          $pay_state="0";
        }
        // dump($start);
        // dump($end);
        // dump($keyword);
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('a.JieSuanTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.JieSuanTime"]=array("elt",$end); 
            $where=array('a.JieSuanTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.JieSuanTime'=>array('between time',[$start,$end]));
        }

        if($payment_state!='')
        {
            $where1=array('a.Status'=>"$payment_state");
        }

        if($pay_state!='')
        {
            $where3=array('a.IsPay'=>"$pay_state");
        }

        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_JieSuanLog')
          ->page($page,20)
          ->alias('a')
          // ->field('a.*,m.EnrolName,m.MemberType,s.Name')
          ->field('a.ID,s.Name,a.JieMoneylogBeginTime,a.JieMoneylogEndTime,a.IsPay,a.JieSuanMoney,a.Status,a.JieSuanPeople,a.JieSuanTime')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("MemberType='1'")
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
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

          foreach ($res as $k => $v) {
            $id=$res[$k]['ID'];
            // dump($id);
            $data= DB::table('ceb_Order')
                 ->where("JieSuanID='$id' and Status='已完成'") 
            ->sum('tj_pingtaifuwufei');
            $res[$k]['pintai']=$data;
          }

          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['pintai'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['pintai']=$sin;
          } 
        //需求数据统计处理  
        $data_sum=DB::table('ceb_JieSuanLog')
                ->alias('a')
                ->field('a.ID')
                ->join('ceb_Member m','a.MemberID=m.ID','Left')
                ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
                ->where($where)
                ->where($where1)
                ->where($where2)
                ->where($where3)
                ->where("MemberType='1'")
                ->count();

        $alltotal=DB::table('ceb_JieSuanLog')
                ->alias('a')
                ->field('a.ID,a.JieSuanMoney')
                ->join('ceb_Member m','a.MemberID=m.ID','Left')
                ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
                ->where($where)
                ->where($where1)
                ->where($where2)
                ->where($where3)
                ->where("MemberType='1'")
                ->sum('JieSuanMoney'); 
        $allpintai=DB::table('ceb_Order')
                ->alias('o')
                ->field('o.ID as oid,a.ID,s.Name,a.JieMoneylogBeginTime,a.JieMoneylogEndTime,a.IsPay,a.JieSuanMoney,a.Status,a.JieSuanPeople,a.JieSuanTime,o.tj_pingtaifuwufei')
                ->join('ceb_JieSuanLog a','a.ID=o.JieSuanID','Left')
                ->join('ceb_Member m','a.MemberID=m.ID','Left')
                ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
                ->where($where)
                ->where($where1)
                ->where($where2)
                ->where($where3)
                ->where("MemberType='1'")
                ->sum('tj_pingtaifuwufei');                
        $tem_page=$data_sum/20;
        $lastpage=ceil($tem_page);
          // dump($data);   
        // dump($res);exit;
        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('data_sum',$data_sum);
        $this->assign('lastpage',$lastpage);
        $this->assign('alltotal',$alltotal);
        $this->assign('allpintai',$allpintai);
        return $this->fetch('');
    }

    //结算导出
    public function settlementExcel()
    { 
      vendor("phpexcel.PHPExcel"); 
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
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('a.JieSuanTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.JieSuanTime"]=array("elt",$end); 
            $where=array('a.JieSuanTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.JieSuanTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_JieSuanLog')
          ->alias('a')
          // ->field('a.*,m.EnrolName,m.MemberType,s.Name')
          ->field('a.ID,s.Name,a.JieMoneylogBeginTime,a.JieMoneylogEndTime,a.IsPay,a.JieSuanMoney,a.Status,a.JieSuanPeople,a.JieSuanTime')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("MemberType='1'")
          ->where($where)
          ->where($where2)
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
              if ($res[$k]['IsPay']==0) {
                $res[$k]['IsPay']="否";
              }else{
                $res[$k]['IsPay']="是";
              }
            }

          foreach ($res as $k => $v) {
            $res[$k]['JieMoneylogBeginTime']=substr($res[$k]['JieMoneylogBeginTime'],0,16);
            $res[$k]['JieMoneylogEndTime']=substr($res[$k]['JieMoneylogEndTime'],0,16);
            $res[$k]['JieSuanTime']=substr($res[$k]['JieSuanTime'],0,16);
          } 

          foreach ($res as $k => $v) {
            $id=$res[$k]['ID'];
            // dump($id);
            $data= DB::table('ceb_Order')
                 ->where("JieSuanID='$id' and Status='已完成'") 
            ->sum('tj_pingtaifuwufei');
            $res[$k]['pintai']=$data;
          } 
          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['pintai'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['pintai']=$sin;
          }   
          $PHPExcel = new \PHPExcel();//实例化  
          $PHPSheet = $PHPExcel->getActiveSheet();
          $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
          $i=2;  
          foreach($res as $key=>$val){  
                 // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
                 $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","结算开始时间")->setCellValue("D1","结算结算时间")->setCellValue("E1","是否已支付")->setCellValue("F1","结算金额")->setCellValue("G1","平台服务费")->setCellValue("H1","处理状态")->setCellValue("I1","结算人")->setCellValue("J1","结算申请时间");//表格数据 
                 $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['JieMoneylogBeginTime'])->setCellValue('D'.$i,$val['JieMoneylogEndTime'])->setCellValue('E'.$i,$val['IsPay'])->setCellValue('F'.$i,$val['JieSuanMoney'])->setCellValue('G'.$i,$val['pintai'])->setCellValue('H'.$i,$val['Status'])->setCellValue('I'.$i,$val['JieSuanPeople'])->setCellValue('J'.$i,$val['JieSuanTime']);//表格数据   
                 $i++;  
             }  
          // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
          // header('Content-Disposition: attachment;filename="普通订单汇总报表.xlsx"');//下载下来的表格名  
          // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
          // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

          header('Content-Type: application/vnd.ms-excel');
          header("Content-Disposition: attachment;filename=\"餐厅会员结算表.xls\"");
          header('Cache-Control: max-age=0'); 
          $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
          $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 
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
            ->field('o.ID,o.OrderCode,o.Total,o.tj_yunyingjine,o.tj_pingtaifuwufei,o.tj_fangxian,o.tj_yongjin,o.tj_feilv,o.Status,j.JieSuanMoney,j.Status as jStatus,o.TimeFd4,s.DecFd2,j.IsPay,s.Name,j.JieSuanTime')
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

      // dump($res);
      $this->assign('res2',$res);
      $this->assign('data',$data);
      return $this->fetch('');   
    }

    //结算管理详情下载
    public function settlement_infoExcel(){
      $id=input('id');
      vendor("phpexcel.PHPExcel");
      $data= DB::table('ceb_Order') 
            ->alias('o')
            ->field('o.ID,o.OrderCode,o.Total,o.tj_yunyingjine,o.tj_pingtaifuwufei,o.tj_fangxian,o.tj_yongjin,o.tj_feilv,o.Status,j.JieSuanMoney,j.Status as jStatus,o.TimeFd4,s.DecFd2,j.IsPay,s.Name,j.JieSuanTime')
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

            foreach ($data as $k => $v) {
              $data[$k]['JieSuanTime']=substr($data[$k]['JieSuanTime'],0,19);
            }
          $PHPExcel = new \PHPExcel();//实例化  
          $PHPSheet = $PHPExcel->getActiveSheet();
          $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
          $i=2;  
          foreach($data as $key=>$val){  
                 // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
                 $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","结算时间")->setCellValue("D1","订单编号")->setCellValue("E1","订单金额")->setCellValue("F1","运营金额")->setCellValue("G1","平台服务费")->setCellValue("H1","返现支出")->setCellValue("I1","佣金支出")->setCellValue("J1","费率")->setCellValue("K1","结算金额")->setCellValue("L1","结算状态")->setCellValue("M1","订单完成时间");//表格数据 
                 $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['JieSuanTime'])->setCellValue('D'.$i,$val['OrderCode'])->setCellValue('E'.$i,$val['Total'])->setCellValue('F'.$i,$val['tj_yunyingjine'])->setCellValue('G'.$i,$val['tj_pingtaifuwufei'])->setCellValue('H'.$i,$val['tj_fangxian'])->setCellValue('I'.$i,$val['tj_yongjin'])->setCellValue('J'.$i,$val['tj_feilv'])->setCellValue('K'.$i,$val['jiesuan'])->setCellValue('L'.$i,$val['zhuantai'])->setCellValue('M'.$i,$val['TimeFd4']);//表格数据   
                 $i++;  
             }  
          // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
          // header('Content-Disposition: attachment;filename="普通订单汇总报表.xlsx"');//下载下来的表格名  
          // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
          // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

          header('Content-Type: application/vnd.ms-excel');
          header("Content-Disposition: attachment;filename=\"餐厅会员结算详情.xls\"");
          header('Cache-Control: max-age=0'); 
          $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
          $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

    }

    //结算管理修改
    public function settlement_edit()
    {
      if (input('')) {
        // dump(input(''));exit;
        $wh['ID']=input('id');
        $id=$wh['ID'];
        $ispay=input('ispay');
        $sec=input('sec');
        $beizhu=input('beizhu');
        $data= DB::table('ceb_Order') 
            ->alias('o')
            ->field('o.ID')
            ->join('ceb_JieSuanLog j','o.JieSuanID=j.ID','Left')
            ->join('ceb_ShopMain s','o.ShopID=s.ID','Left')
            ->where("JieSuanID='$id' and o.Status='已完成'")
            ->order('ID desc')
            ->select();

        if (input('men')==''&&input('ispay')=='1') {
          $name=session('gg_uname');
          // dump($name);
          $res=Db::table('ceb_JieSuanLog')
              ->where($wh)
              ->update(["IsPay" => "$ispay","JieSuanPeople" => "$name","Status" => "$sec","NvrFd1" => "$beizhu"]);
              //已付款改供应商后台状态
              if ($sec=="审核通过") {
                foreach ($data as $k => $v) {
                  $id=$data[$k]['ID'];
                  $tem_stu=Db::table('ceb_Order')
                    ->where('ID',"$id")
                    ->update(["isJieSuan" => "2"]);
                }
              }
              if ($ispay=='1') {
                foreach ($data as $k => $v) {
                  $id=$data[$k]['ID'];
                  $tem_stu=Db::table('ceb_Order')
                    ->where('ID',"$id")
                    ->update(["isJieSuan" => "3"]);
                }
              }
              if ($res) {
               $this->success('修改成功',url('admin/Restmember/settlement'));
              }else{
                 $this->error('修改失败');
              }
        }else{

        }

         $res=Db::table('ceb_JieSuanLog')
              ->where($wh)
              ->update(["IsPay" => "$ispay","Status" => "$sec","NvrFd1" => "$beizhu"]);
              //已付款改供应商后台状态
              if ($sec=="审核通过") {
                foreach ($data as $k => $v) {
                  $id=$data[$k]['ID'];
                  $tem_stu=Db::table('ceb_Order')
                    ->where('ID',"$id")
                    ->update(["isJieSuan" => "2"]);
                }
              }
              if ($ispay=='1') {
                foreach ($data as $k => $v) {
                  $id=$data[$k]['ID'];
                  $tem_stu=Db::table('ceb_Order')
                    ->where('ID',"$id")
                    ->update(["isJieSuan" => "3"]);
                }
              }
              if ($res) {
               $this->success('修改成功',url('admin/Restmember/settlement'));
              }else{
                 $this->error('修改失败');
              }
        
      }
    }

    public function commission($page=1)
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
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('a.JieSuanTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.JieSuanTime"]=array("elt",$end); 
            $where=array('a.JieSuanTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.JieSuanTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_MoneyLog')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where("Title='分销者佣金' and MemberType='1'")
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



  

}
