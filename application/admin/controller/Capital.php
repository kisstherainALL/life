<?php
namespace app\admin\controller;
use think\Db;
class Capital extends \think\Controller
{
    public function index()
    {
    
  

		  return $this->fetch('index');
    }

    public function capital_log($page=1)
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
            $where=array('c.CreateTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["c.CreateTime"]=array("elt",$end); 
            $where=array('c.CreateTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('c.CreateTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(c.Acode like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_MoneyLog')
          ->page($page,20)
          ->alias('c')
          ->field('c.ID,c.Title,c.TransAmount,c.CreateTime,c.Acode')
          ->where($where)
          ->where($where2)
          ->order('ID desc')
          ->select();
          // dump($res);
          //金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['TransAmount'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['TransAmount']=$sin;
          }
      $resNopage =  DB::table('ceb_MoneyLog')
                ->alias('c')
                ->field('c.ID,c.Title,c.TransAmount,c.CreateTime,c.Acode')
                ->where($where)
                ->where($where2)
                ->order('ID desc')
                ->select();  
      //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);
      // dump($res);service
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('data_sum',$num);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');
    }

    public function service($page=1){
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
            $where=array('c.CreateTime'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["c.CreateTime"]=array("elt",$end); 
            $where=array('c.CreateTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('c.CreateTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_MoneyLog')
          ->alias('c')
          ->field('c.Title,c.TransAmount,c.CreateTime,s.Name,c.Acode,o.Total')
          ->join('ceb_Member m','c.MemberID=m.ID','left')
          ->join('ceb_ShopMain s','s.MemberID=m.ID','left')
          ->join('ceb_Order o','o.OrderCode=c.Acode','left')
          ->where($where)
          ->where($where2)
          ->where("c.Type=6")
          ->order('CreateTime desc')
          ->select();
          // dump($res);
          // $result = a_array_unique($input);
          // dump($result);exit;
          //金额
         foreach ($res as $k => $v) {
            $tem=$res[$k]['TransAmount'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
              

            }
              // $one=bcmul($tem, 1, 2);
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              // dump($sin);exit;
              $res[$k]['TransAmount']=$sin;
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;            
            }
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              $res[$k]['Total']=$sin;
              $res[$k]['CreateTime']=substr($res[$k]['CreateTime'], 0,19);
          }

      //去除相同数据
      // $array2D = array('first'=>array('title'=>'1111','date'=>'2222'),'second'=>array('title'=>'1111','date'=>'2222'),'third'=>array('title'=>'2222','date'=>'3333'));  
      //   dump($array2D);  
      //   dump(unique_arr($res,true)); 
      // dump($res[0]['ROW_NUMBER']);
      foreach ($res as $k => $v) {
        $res[$k]['ROW_NUMBER']="0";
      }
      // dump($res);exit;
      $result_data = unique_arr($res,true);
      $sons = array_chunk($result_data, 20);
        // dump($sons);exit;
        if (empty($result_data)) {
          $p=$page;
          $res_data=$sons;
        }else{
          $p=$page-1;
          $res_data=$sons[$p];//装第一页（第N页）数据的新数组
        }
        // dump($res_data); 
      // exit;


      //需求数据统计处理  
      $data_sum=count($result_data);
      // $allTotal=DB::table('ceb_MoneyLog')
      //         ->alias('c')
      //         ->field('c.ID,c.Title,c.TransAmount,c.CreateTime,s.Name,c.Acode,o.Total')
      //         ->join('ceb_Member m','c.MemberID=m.ID','left')
      //         ->join('ceb_ShopMain s','s.MemberID=m.ID','left')
      //         ->join('ceb_Order o','o.OrderCode=c.Acode','left')
      //         ->where($where)
      //         ->where($where2)
      //         ->where("c.Type=6")
      //         ->sum('TransAmount'); 
      $Total_sum=0;        
      foreach ($result_data as $t) {
      $Total_sum+=$t["TransAmount"];
      }        
      // $allTotal=abs($allTotal);
      $Total_sum=sprintf("%1\$.2f",$Total_sum);                
      $tem_page=$data_sum/20;
      $lastpage=ceil($tem_page);    
      // dump($sin);exit;
      $this->assign('res_data',$res_data);
      $this->assign('page',$page);
      $this->assign('data_sum',$data_sum);
      $this->assign('lastpage',$lastpage);
      // $this->assign('allTotal',$allTotal);
      $this->assign('Total_sum',$Total_sum);
      return $this->fetch('');
    }

    

    //佣金
    public function commission($page=1){
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
              
              $where=array('c.CreateTime'=>array("egt",$start));
          }

          if($end!='')
          {
              
              $where=array('c.CreateTime'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('c.CreateTime'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(c.Acode like '%". $keyword."%')";
          }

        }
        $res=DB::table('ceb_MoneyLog')
          ->page($page,20)
          ->alias('c')
          ->field('c.ID,c.Title,c.TransAmount,c.CreateTime,c.Acode')
          ->where($where)
          ->where($where2)
          ->where("Type=3 and Aid=0")
          ->order('ID desc')
          ->select();
          //金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['TransAmount'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['TransAmount']=$sin;
          }
      $resNopage = DB::table('ceb_MoneyLog')
                ->alias('c')
                ->field('c.ID,c.Title,c.TransAmount,c.CreateTime,c.Acode')
                ->where($where)
                ->where($where2)
                ->where("Type=3 and Aid=0")
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
      return $this->fetch('');


    }

    //提现
    public function ti_xian($page=1)
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
        // dump($start);
        // dump($end);
        // dump($keyword);
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            
            $where=array('t.ApplyTime'=>array("egt",$start));
        }

        if($end!='')
        {
            
            $where=array('t.ApplyTime'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('t.ApplyTime'=>array('between time',[$start,$end]));
        }

        if($payment_state!='')
        {
            $where1=array('t.AuditStatus'=>"$payment_state");
        }

        if($pay_state!='')
        {
            $where3=array('t.PayStatus'=>"$pay_state");
        }

        if($keyword!='')
        {
            $where2="(m.EnrolName like '%". $keyword."%' or m.TrueName like '%". $keyword."%')";
        }

      }
     $res=DB::table('ceb_TiXian')
        ->page($page,20)
        ->alias('t')
        ->field('t.ID,m.EnrolName,m.TrueName,t.ToName,t.ToAccount,t.ToBankName,t.ToBankDetailName,t.ApplyAmount,t.ChargeAmount,t.TrueAmount,t.ApplyTime,t.AuditStatus,t.PayStatus')
        ->join('ceb_Member m','t.MemberID=m.ID')
        ->where($where)
        ->where($where1)
        ->where($where2)
        ->where($where3)
        ->order('ID desc')
        ->select();
        //金额
        foreach ($res as $k => $v) {
          $tem=$res[$k]['ChargeAmount'];
          if (substr($tem,0,1)==".") {
            $tem="0".$tem;
          }
            $sin=sprintf("%1\$.2f",$tem);
            $res[$k]['ChargeAmount']=$sin;
        }
      //需求数据统计处理  
        $data_sum=DB::table('ceb_TiXian')
                ->alias('t')
                ->field('t.ID')
                ->join('ceb_Member m','t.MemberID=m.ID')
                ->where($where)
                ->where($where1)
                ->where($where2)
                ->where($where3)
                ->count();

        $alltotal=DB::table('ceb_TiXian')
                ->alias('t')
                ->field('t.ID,m.EnrolName,m.TrueName,t.ToName,t.ToAccount,t.ToBankName,t.ToBankDetailName,t.ApplyAmount,t.ChargeAmount,t.TrueAmount,t.ApplyTime,t.AuditStatus,t.PayStatus')
                ->join('ceb_Member m','t.MemberID=m.ID')
                ->where($where)
                ->where($where1)
                ->where($where2)
                ->where($where3)
                ->sum('ApplyAmount'); 
        $allpintai=DB::table('ceb_TiXian')
                ->alias('t')
                ->field('t.ID,m.EnrolName,m.TrueName,t.ToName,t.ToAccount,t.ToBankName,t.ToBankDetailName,t.ApplyAmount,t.ChargeAmount,t.TrueAmount,t.ApplyTime,t.AuditStatus,t.PayStatus')
                ->join('ceb_Member m','t.MemberID=m.ID')
                ->where($where)
                ->where($where1)
                ->where($where2)
                ->where($where3)
                ->sum('TrueAmount');                
        $tem_page=$data_sum/20;
        $lastpage=ceil($tem_page);  
      // dump($res);exit;
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('data_sum',$data_sum);
      $this->assign('lastpage',$lastpage);
      $this->assign('alltotal',$alltotal);
      $this->assign('allpintai',$allpintai);
      return $this->fetch('');


    }
    //提现详情
    public function ti_xian_info($page=1){
      $id=input('id');
      // dump(input(''));
      // dump($id);
      $wh['t.ID']=$id;
      $res=DB::table('ceb_TiXian')
          // ->page($page,10)
          ->alias('t')
          // ->field('t.ID,m.EnrolName,m.TrueName,t.ToName,t.ToAccount,t.ToBankName,t.ToBankDetailName,t.ApplyAmount,t.ChargeAmount,t.TrueAmount,t.ApplyTime,t.AuditStatus,t.PayStatus')
          ->field('t.*,m.EnrolName,m.TrueName')
          ->join('ceb_Member m','t.MemberID=m.ID')
          // ->limit(100)
          ->where($wh)
          ->order('ID desc')
          ->find();
          //金额
          
            $tem=$res['ChargeAmount'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res['ChargeAmount']=$sin;

              $res['ApplyTime']=substr($res['ApplyTime'],0,19);
              $res['AuditTime']=substr($res['AuditTime'],0,19);
              $res['SuccessTiXianTime']=substr($res['SuccessTiXianTime'],0,19);
          
      // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');

    }
    //提现管理修改
    public function ti_xian_edit(){
        // dump(input(''));
        $wh['ID'] = input('id');
        $auditStatus = input('AuditStatus');//审核不通过
        $applyTime = input('shen_time');//审核时间
        $payStatus = input('PayStatus');//未体现
        $beuzhu = input('beizhu');//审核不通过原因
        $Demo = input('fail');//提现失败原因
        $res=Db::table('ceb_TiXian')
            ->where($wh)
            ->update(["AuditStatus" => "$auditStatus",
                      "AplyTime" => "$applyTime",
                      "PayStatus" => "$payStatus",
                      "ContractNo" => "$beuzhu",
                      "Demo" => "$Demo"]);
        if ($res) {
            welog($text='资金管理 —— 提现管理修改');
           $this->success('修改成功',url('admin/Capital/ti_xian'));
          }else{
             $this->error('修改失败');
        }    

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
