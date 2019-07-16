<?php
namespace app\admin\controller;
use think\Db;
class User extends \think\Controller
{
    public function index()
    {
    
  

      return $this->fetch('index');
    }

    public function welcome()
    {
      return $this->fetch('welcome');
    }

    public function user_list($page=1)
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
            // $where["c.CreateTime"]=array("egt",$start);
            $where=array('CreateTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["c.CreateTime"]=array("elt",$end); 
            $where=array('CreateTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('CreateTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(EnrolName like '%". $keyword."%' or NickName like '%". $keyword."%' or TrueName like '%". $keyword."%')";
        }

      }
      
        $res=Db::table('ceb_Member')
            ->page($page,20)
            ->where('MemberType=2')
            ->where($where)
            ->where($where2)
            ->order('CreateTime desc')
            ->select();
        $resNopage=Db::table('ceb_Member')
            ->where('MemberType=2')
            ->where($where)
            ->where($where2)
            ->select();
        foreach ($res as $k => $v) {
              $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,19);
        }
        //分页自定义
        $num=count($resNopage); 
        $tem_page=$num/20;
        $lastpage=ceil($tem_page);
        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('data_sum',$num);
        $this->assign('lastpage',$lastpage);
        return $this->fetch('');
      
    }
    //供应商审核
    public function user_examine($page=1)
    {
      $where='';
      if (input('keyword')) {
        // dump(input(''));exit;
       
        $keyword = input('keyword');
        
        if($keyword!='')
        {
            $where="(a.type=1 and a.Contact like '%". $keyword."%' or a.Name like '%". $keyword."%' or m.EnrolName like '%". $keyword."%')";
        }

      }
           
      $res=DB::table('ceb_ApproveInfo')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
          ->join('ceb_Member m','a.MemberID=m.ID')
          ->join('ceb_ShopMain s','s.MemberID=m.ID')
          ->where('a.type=1')
          ->where($where)
          ->order('ID desc')
          ->select();
      $resNopage=DB::table('ceb_ApproveInfo')
          ->alias('a')
          ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
          ->join('ceb_Member m','a.MemberID=m.ID')
          ->join('ceb_ShopMain s','s.MemberID=m.ID')
          ->where('a.type=1')
          ->where($where)
          ->order('ID desc')
          ->select();   
      foreach ($res as $k => $v) {
        $res[$k]['AuditTime']=substr($res[$k]['AuditTime'],0,19);
      }
      //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('data_sum',$num);
      $this->assign('lastpage',$lastpage);
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
        $riz=Db::table("ceb_Member")
            ->where($wh)
            ->find();
        if ($password=='') {
           $res=Db::table('ceb_Member')
              ->where($wh)
              ->update(["NickName" => "$nickname","TrueName" => "$truename","Sex" => "$sec"]);
              if ($res) {
                welog($text='修改资料(供应商)'.' —— '.'会员名:'.$riz['EnrolName']);
               $this->success('修改成功',url('admin/User/user_list'));
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
    //供应商修改
    public function examine_edit($page=1){
      if (input('')) {
        // dump(input(''));
        $wh['ID']=input('id');
        $result=input('result');
        $resultdemo=input('resultdemo');
        $riz=Db::table("ceb_Member")
            ->where($wh)
            ->find();
        $res=Db::table('ceb_ApproveInfo')
              ->where($wh)
              ->update(["AuthentResult" => "$result","AuthentResultDemo" => "$resultdemo"]);
              if ($res) {
                welog($text='修改审核(供应商)'.' —— '.'会员名:'.$riz['EnrolName']);
                $this->success('修改成功',url('admin/User/user_examine'));
              }else{
                 $this->error('修改失败');
              }

      }
    }
    //重置密码
    public function pass_edit(){
      if (input('id')) {
        $wh['ID']=input('id');
        $riz=Db::table("ceb_Member")
            ->where($wh)
            ->find();
        // dump($riz);exit;
        $one=md5(123456);
            $password = strtoupper($one);          
        $res=Db::table('ceb_Member')
              ->where($wh)
              ->update(["Password" => "$password"]);
              if ($res) {
                welog($text='重置密码(供应商)'.' —— '.'会员名:'.$riz['EnrolName']);
                $this->error('修改成功(新密码：123456)',url('admin/User/user_list'));
              }else{
                 $this->error('修改失败');
              }
      }
    }
    //供应商审核状态
    public function user_state($page=1)
    {
      $where='';
      if (input('keyword')) {
        // dump(input(''));exit;
       
        $keyword = input('keyword');
        
        if($keyword!='')
        {
            $where="(a.AuthentResult like '%". $keyword."%')";
        }

      }
      
        $res=DB::table('ceb_ApproveInfo')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
            ->join('ceb_Member m','a.MemberID=m.ID')
            ->join('ceb_ShopMain s','s.MemberID=m.ID')
            ->where('a.type=1')
            ->where($where)
            ->order('ID desc')
            ->select();
        $resNopage=DB::table('ceb_ApproveInfo')
                  ->alias('a')
                  ->field('a.ID,a.Name,a.DocNo,a.Contact,a.Phone,a.AuthentResult,a.AuditTime,m.EnrolName,s.DecFd2')
                  ->join('ceb_Member m','a.MemberID=m.ID')
                  ->join('ceb_ShopMain s','s.MemberID=m.ID')
                  ->where('a.type=1')
                  ->where($where)
                  ->order('ID desc')
                  ->select();       
      //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('data_sum',$num);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('user_examine');

    }
    //供应商会员详情
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
    //供应商审核
    public function user_examine_info(){
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
      
        $res['CreateTime']=substr($res['CreateTime'],0,19);
        $res['AuditTime']=substr($res['AuditTime'],0,19);
     
      $this->assign('res2',$res);
      return $this->fetch('');
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
        $wh=array('MemberType' => 2,'CreateTime'=>array('gt',$start_time));
        $res =Db::table('ceb_Member')->page($page,10)->where($wh)->select();
        $tem=Db::table('ceb_Member')->where($wh)->select();    
        $member_num=count($tem);
        // dump($res);
        $this->assign('page',$page);
        $this->assign('res2',$res);
        $this->assign('member_num',$member_num);
        return $this->fetch('user_list');

      }elseif ($start_time!='' && $end_time!='') {
        $res=Db::table('ceb_Member')
            ->page($page,20)
            ->where(" MemberType=2  and  CreateTime>='$start_time' and CreateTime<='$end_time'")
            ->select();
        $tem=Db::table('ceb_Member')->where(" MemberType=2  and  CreateTime>='$start_time' and CreateTime<='$end_time'")->select();    
        $member_num=count($tem);    
        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('member_num',$member_num);
        return $this->fetch('user_list');

      }elseif ($start_time=='' && $end_time!='') {
        $wh=array('MemberType' => 2,'CreateTime'=>array('lt',$end_time));
        $res =Db::table('ceb_Member')->page($page,10)->where($wh)->select();
        $tem=Db::table('ceb_Member')->where($wh)->select();    
        $member_num=count($tem);
        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('member_num',$member_num);
        return $this->fetch('user_list');

      }else{
        $res =Db::table('ceb_Member')->page($page,20)->where('MemberType','2')->select();
        $tem=Db::table('ceb_Member')->where('MemberType','2')->select();    
        $member_num=count($tem);
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
        $this->assign('member_num',$member_num);
        return $this->fetch('user_list');
      }
     
     
      
   
     
     // return $this->fetch('user_list');
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
          ->where("Title='订单返现' and MemberType='2'")
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
          // ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->field('a.ID,s.Name,a.JieMoneylogBeginTime,a.JieMoneylogEndTime,a.IsPay,a.JieSuanMoney,a.Status,a.JieSuanPeople,a.JieSuanTime')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->where("MemberType='2'")
          ->order('ID desc')
          ->select();
          foreach ($res as $k => $v) {
            $res[$k]['JieSuanTime']=substr($res[$k]['JieSuanTime'],0,19);
            $res[$k]['JieMoneylogBeginTime']=substr($res[$k]['JieMoneylogBeginTime'],0,19);
            $res[$k]['JieMoneylogEndTime']=substr($res[$k]['JieMoneylogEndTime'],0,19);
          }
          //获取结算平台服务费总额
          foreach ($res as $k => $v) {
            $id=$res[$k]['ID'];
            // dump($id);
            $data= DB::table('ceb_Order')
                 ->where("JieSuanID='$id' and Status='已完成'") 
                 ->sum('tj_pingtaifuwufei');
            $res[$k]['pintai']=$data;
          }

          //结算金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['JieSuanMoney'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['JieSuanMoney']=$sin;
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
                ->where("MemberType='2'")
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
                ->where("MemberType='2'")
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
                ->where("MemberType='2'")
                ->sum('tj_pingtaifuwufei');                
        $tem_page=$data_sum/20;
        $lastpage=ceil($tem_page);
        // dump($allpintai);exit;
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
          // ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->field('a.ID,s.Name,a.JieMoneylogBeginTime,a.JieMoneylogEndTime,a.IsPay,a.JieSuanMoney,a.Status,a.JieSuanPeople,a.JieSuanTime')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where($where)
          ->where($where2)
          ->where("MemberType='2'")
          ->order('ID desc')
          ->select();
          
          //是否支付
          foreach ($res as $k => $v) {
            if ($res[$k]['IsPay']=="0") {
              $res[$k]['IsPay']="否";
            }else if ($res[$k]['IsPay']=="1") {
              $res[$k]['IsPay']="是";
            }
          }
          //获取结算平台服务费总额
          foreach ($res as $k => $v) {
            $id=$res[$k]['ID'];
            // dump($id);
            $data= DB::table('ceb_Order')
                 ->where("JieSuanID='$id' and Status='已完成'") 
            ->sum('tj_pingtaifuwufei');
            $res[$k]['pintai']=$data;
          }

          //结算金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['JieSuanMoney'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['JieSuanMoney']=$sin;
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
          //时间处理
          foreach ($res as $k => $v) {
            $res[$k]['JieSuanTime']=substr($res[$k]['JieSuanTime'],0,19);
            $res[$k]['JieMoneylogBeginTime']=substr($res[$k]['JieMoneylogBeginTime'],0,19);
            $res[$k]['JieMoneylogEndTime']=substr($res[$k]['JieMoneylogEndTime'],0,19);
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
          header("Content-Disposition: attachment;filename=\"普通订单汇总报表.xls\"");
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
           
            if (substr($res['JieSuanMoney'],0,1)==".") {
                $res['JieSuanMoney']="0".$res['JieSuanMoney'];
              }
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

      // dump($data);exit;
      $this->assign('res2',$res);
      $this->assign('data',$data);
      return $this->fetch('');   
    }

    //结算管理详情导出
    public function settlement_infoExcel()
    {
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
          header("Content-Disposition: attachment;filename=\"普通订单汇总报表.xls\"");
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
        $where['a.ID']=input('id');
        $ispay=input('ispay');
        $sec=input('sec');
        $beizhu=input('beizhu');
        $riz=DB::table('ceb_JieSuanLog')
          ->alias('a')
          // ->field('a.ID,a.CreateTime,a.Title,a.TransAmount,a.ACode,m.EnrolName,m.MemberType,s.Name')
          ->field('a.*,s.Name,m.EnrolName')
          ->join('ceb_Member m','a.MemberID=m.ID','Left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','Left')
          ->where($where)
          ->order('ID desc')
          ->find();
        $data= DB::table('ceb_Order') 
            ->alias('o')
            ->field('o.ID')
            ->join('ceb_JieSuanLog j','o.JieSuanID=j.ID','Left')
            ->join('ceb_ShopMain s','o.ShopID=s.ID','Left')
            ->where("JieSuanID='$id' and o.Status='已完成'")
            ->order('ID desc')
            ->select();
        // dump($data);      
        // dump($riz);exit;
        
        if (input('men')=='') {
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
                welog($text='供应商结算 —— '.$riz['Name'].'——'.$ispay.$sec);
               $this->success('修改成功',url('admin/User/settlement'));
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
                welog($text='供应商结算 —— '.$riz['Name'].'——'.$ispay.$sec);
                $this->success('修改成功',url('admin/User/settlement'));
              }else{
                 $this->error('修改失败');
              }
        
      }
    }
    //佣金
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
          ->where("Title='分销所得佣金' and MemberType='2'")
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
            welog($text='批量删除会员(供应商)');
            $this->success('删除会员成功(供应商)',url('admin/User/user_list'));
        }else{
                $this->error('删除会员失败(供应商)');
        }
      }
             
    }
    //返现批量删除
    public function fanxiandelete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $res=Db::table('ceb_MoneyLog')->where($wh)->delete();
        if($res){
            welog($text='批量删除返现(供应商)');
            $this->success('删除返现成功(供应商)',url('admin/User/fanxian'));
        }else{
            $this->error('删除返现失败(供应商)');
        }
      }
             
    }

    //佣金批量删除
    public function commissiondelete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $res=Db::table('ceb_MoneyLog')->where($wh)->delete();
        if($res){
            welog($text='批量删除佣金(供应商)');
            $this->success('批量删除佣金成功(供应商)',url('admin/User/fanxian'));
        }else{
            $this->error('批量删除返现失败(供应商)');
        }
      }
             
    }

    //结算批量删除
    public function settlementdelete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $res=Db::table('ceb_JieSuanLog')->where($wh)->delete();
        if($res){
            welog($text='批量删除结算(供应商)');
            $this->success('批量删除结算成功(供应商)',url('admin/User/fanxian'));
        }else{
            $this->error('批量删除结算失败(供应商)');
        }
      }
             
    }

    //审核批量删除
    public function exdelete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);
        $res=Db::table('ceb_ApproveInfo')->where($wh)->delete();
        if($res){
            welog($text='批量删除(供应商审核列表数据)');
            $this->success('删除成功(供应商审核列表数据)',url('admin/User/user_examine'));
        }else{
                $this->error('删除失败(供应商审核列表数据)');
        }
      }
             
    }
  

}
