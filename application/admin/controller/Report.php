<?php
namespace app\admin\controller;
use think\Db;
class Report extends \think\Controller
{
    public function index()
    {
      // $res=Db::table('ceb_Order')->field('ID,Receiver')->select();
      // dump($res3);
      

		  // return $this->fetch('index');
    }

    public function welcome()
    {
      // return $this->fetch('welcome');
    }
    //普通订单汇总报表
    public function pu_order($page=1)
    {
      $where='';
      $where1='';
      $where2='';
      $where3='';
      $where4='';
      if (input('keyword')||input('start')||input('end')||input('pay_state')||input('payment_state')||input('settlement_state')) {
        // dump(input(''));
        $start = input('start');
        $end = input('end');
        $keyword = input('keyword');
        $pay_state = input('pay_state');
        $payment_state = input('payment_state');
        $settlement_state = input('settlement_state');
        
        
        // dump($start);
        // dump($end);
        // dump($keyword);
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        $this->assign('pay_state',$pay_state);
        $this->assign('payment_state',$payment_state);
        $this->assign('settlement_state',$settlement_state);
        if ($settlement_state=='fou') {
            $settlement_state="0";
        }
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('a.TimeFd1'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where=array('a.TimeFd1'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
        }

        if($pay_state!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where1=array('a.PayStatus'=>"$pay_state");           
        }

        if($payment_state!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where3=array('a.Status'=>"$payment_state");           
        }

        if($settlement_state!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where4=array('a.IsJieSuan'=>"$settlement_state");           
        }

        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
        }

      }
       $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.Count,a.EnrolName,a.IsJieSuan,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('Type',0)
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->where($where4)
          ->order('TimeFd1 desc')
          ->select();

          // foreach ($res as $k => $v) {
          //   if ($res[$k]['IsJieSuan']==0) {
          //     $res[$k]['IsJieSuan']="未结";
          //   }else{
          //     $res[$k]['IsJieSuan']="已结";
          //   }
          // }
          // dump($res);
          //订单金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['Total']=$sin;
          }
	   //不含分页
	   $resNopage=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.Count,a.EnrolName,a.IsJieSuan,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('Type',0)
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->where($where4)
          ->order('TimeFd1 desc')
          ->select();
          foreach ($res as $k => $v) {
            if ($res[$k]['IsJieSuan']==0) {
              $res[$k]['IsJieSuan']="未结";
            }else{
              $res[$k]['IsJieSuan']="已结";
            }
          }
          //订单金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['Total']=$sin;
          }
	    $AllTotal=0;
  	  foreach ($resNopage as $t) {
  			$AllTotal+=$t["Total"];
  		}
      //自定义分页
      $sum=count($resNopage);
      $tem_page=$sum/20;
      $lastpage=ceil($tem_page);

	    $this->assign('resNoPagecount',count($resNopage));
	    $this->assign('AllTotal', $AllTotal);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');
    }
    //普通订单下载
    public function pu_orderExcel()  
    {
      
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where1='';
        $where2='';
        $where3='';
        $where4='';
        if (input('keyword')||input('start')||input('end')||input('pay_state')||input('payment_state')||input('settlement_state')) {
        // dump(input(''));exit;
        $start = input('start');
        $end = input('end');
        $keyword = input('keyword');
        $pay_state = input('pay_state');
        $payment_state = input('payment_state');
        $settlement_state = input('settlement_state');
        if ($settlement_state=='fou') {
            $settlement_state="0";
        }  
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end); 
              $where=array('a.TimeFd1'=>array("elt",$end));          
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($pay_state!='')
          {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where1=array('a.PayStatus'=>"$pay_state");           
          }

          if($payment_state!='')
          {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where3=array('a.Status'=>"$payment_state");           
          }

          if($settlement_state!='')
          {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where4=array('a.IsJieSuan'=>"$settlement_state");           
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
         $res=Db::table('ceb_Order')
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.Count,a.EnrolName,a.IsJieSuan,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where('Type',0)
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->where($where3)
            ->where($where4)
            ->order('TimeFd1 desc')
            ->select();
            foreach ($res as $k => $v) {
              if ($res[$k]['IsJieSuan']==0) {
                $res[$k]['IsJieSuan']="未结";
              }else{
                $res[$k]['IsJieSuan']="已结";
              }
            }
        // dump($res);exit;  
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","会员名称")->setCellValue("D1","订单号")->setCellValue("E1","下单日期")->setCellValue("F1","收货人")->setCellValue("G1","收货地址")->setCellValue("H1","联系电话")->setCellValue("I1","支付状态")->setCellValue("J1","订单状态")->setCellValue("K1","结算状态")->setCellValue("L1","商品数量")->setCellValue("M1","订单金额");//表格数据 
               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['EnrolName'])->setCellValue('D'.$i,$val['OrderCode'])->setCellValue('E'.$i,$val['TimeFd1'])->setCellValue('F'.$i,$val['Receiver'])->setCellValue('G'.$i,$val['Address'])->setCellValue('H'.$i,$val['Mobile'])->setCellValue('I'.$i,$val['PayStatus'])->setCellValue('J'.$i,$val['Status'])->setCellValue('K'.$i,$val['IsJieSuan'])->setCellValue('L'.$i,$val['Count'])->setCellValue('M'.$i,$val['Total']);//表格数据   
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



    //退换货统计报表
    public function return_order($page=1)
    { 
      $res=Db::table('ceb_ProReturns')
          ->page($page,20)
          ->alias('p')
          ->field('p.ID,p.OrderCode,p.Name,p.AppReason,p.Price,p.Count,p.Total,p.ReturnTotal,p.Status,p.IsBackMoney,m.EnrolName,s.Name as shopName')
          ->join('ceb_Member m','p.MemberID = m.ID','left')
          ->join('ceb_ShopMain s','p.ShopID = s.ID','left')
          // ->join('ceb_Order o','o.ShopID = s.ID')
          ->select();
          foreach ($res as $k => $v) {
            $wh['OrderCode']=$res[$k]['OrderCode'];
            $tem=Db::table('ceb_Order')->field('TimeFd1')->where($wh)->find();
            
            $res[$k]['time']=$tem['TimeFd1'];
          }
		  
    	// 不分页统计
    	$resNopage=Db::table('ceb_ProReturns')
          ->alias('p')
          ->field('p.ID,p.OrderCode,p.Name,p.AppReason,p.Price,p.Count,p.Total,p.ReturnTotal,p.Status,p.IsBackMoney,m.EnrolName,s.Name as shopName')
          ->join('ceb_Member m','p.MemberID = m.ID','left')
          ->join('ceb_ShopMain s','p.ShopID = s.ID','left')
          // ->join('ceb_Order o','o.ShopID = s.ID')
          ->select();
          foreach ($res as $k => $v) {
            $wh['OrderCode']=$res[$k]['OrderCode'];
            $tem=Db::table('ceb_Order')->field('TimeFd1')->where($wh)->find();
            $res[$k]['time']=$tem['TimeFd1'];
          }
          // dump($res);exit;
		  
      $AllTotal=0;
	    $AllCount=0;
  	  foreach ($resNopage as $t) {
  			$AllTotal+=$t["Total"];
  			$AllCount+=$t["Count"];
  		}

       //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);
  
  	  $this->assign('resNoPagecount',count($resNopage));
  	  $this->assign('AllTotal', $AllTotal); 
  	  $this->assign('AllCount', $AllCount); 
      $this->assign('res2',$res);
      $this->assign('page',$page);  
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');

    }

    public function return_orderExcel($page=1)  
    {
      if (input('$page')) {
        $page=input('$page');
      }
        vendor("phpexcel.PHPExcel");  
        $userName=Db::table('ceb_ProReturns')
          ->page($page,10)
          ->alias('p')
          ->field('p.ID,p.OrderCode,p.Name,p.AppReason,p.Price,p.Count,p.Total,p.ReturnTotal,p.Status,p.IsBackMoney,m.EnrolName,s.Name as shopName')
          ->join('ceb_Member m','p.MemberID = m.ID','left')
          ->join('ceb_ShopMain s','p.ShopID = s.ID','left')
          // ->join('ceb_Order o','o.ShopID = s.ID')
          ->select();
          foreach ($userName as $k => $v) {
            $wh['OrderCode']=$userName[$k]['OrderCode'];
            $tem=Db::table('ceb_Order')->field('TimeFd1')->where($wh)->find();
            
            $userName[$k]['time']=substr($tem['TimeFd1'],0,19);
           
          }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($userName as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","会员名称")->setCellValue("D1","关联订单号")->setCellValue("E1","下单时间")->setCellValue("F1","商品名称")->setCellValue("G1","退货数量")->setCellValue("H1","单价")->setCellValue("I1","退款金额")->setCellValue("J1","状态");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['shopName'])->setCellValue('C'.$i,$val['EnrolName'])->setCellValue('D'.$i,$val['OrderCode'])->setCellValue('E'.$i,$val['time'])->setCellValue('F'.$i,$val['Name'])->setCellValue('G'.$i,$val['Count'])->setCellValue('H'.$i,$val['Price'])->setCellValue('I'.$i,$val['Total'])->setCellValue('J'.$i,$val['Status']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="商品退货统计报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"商品退货统计报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //点餐订单汇总表
    public function dc_order($page=1)
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
            $where=array('a.TimeFd1'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end);
            $where=array('a.TimeFd1'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
        }

      }
      $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.EnrolName,a.Count,a.isJieSuan,s.Name,s.AreaName')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('Type',1)
          ->where($where)
          ->where($where2)
          ->order('ID desc')
          ->select();
          foreach ($res as $k => $v) {
            $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
          }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }
    //点餐订单汇总表下载
    public function dc_orderExcel()  
    {
      
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end); 
              $where=array('a.TimeFd1'=>array("elt",$end));          
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.EnrolName,a.Count,a.isJieSuan,s.Name,s.AreaName')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where('Type',1)
            ->where($where)
            ->where($where2)
            ->order('ID desc')
            ->select();
            foreach ($res as $k => $v) {
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
            }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","城市")->setCellValue("C1","所属餐厅/商铺")->setCellValue("D1","会员名称")->setCellValue("E1","订单号")->setCellValue("F1","下单日期")->setCellValue("G1","订单状态")->setCellValue("H1","结算状态")->setCellValue("I1","菜品数量")->setCellValue("J1","订单金额");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['AreaName'])->setCellValue('C'.$i,$val['Name'])->setCellValue('D'.$i,$val['EnrolName'])->setCellValue('E'.$i,$val['OrderCode'])->setCellValue('F'.$i,$val['TimeFd1'])->setCellValue('G'.$i,$val['Status'])->setCellValue('H'.$i,$val['isJieSuan'])->setCellValue('I'.$i,$val['Count'])->setCellValue('J'.$i,$val['Total']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="点餐订单汇总报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"点餐订单汇总报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 
    }


    //佣金汇总报表
    public function Commission_list($page=1)
    {
      $where='';
      $where2='';
      if (input('keyword')||input('start')||input('end')) {
        // dump(input(''));exit;
        $start = input('start');
        $end = input('end');
        $keyword = input('keyword');
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where=array('a.TimeFd1'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["a.TimeFd1"]=array("elt",$end); 
            $where=array('a.TimeFd1'=>array("elt",$end));          
        }

        if($start!='' && $end!='')
        {     
            $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or m.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%' or m.NickName like '%". $keyword."%')";
        }

      }
      $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_yongjin,s.Name,m.ParentFxMemberID,m.EnrolName,m.NickName')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->join('ceb_Member m','a.MemberID = m.ID')
          ->where('a.Status','已完成')
          ->where($where)
          ->where($where2)
          ->order('TimeFd1 desc')
          ->select();
          foreach ($res as $k => $v) {
            if ($res[$k]['ParentFxMemberID']!='') {
              $wh['MemberID']=$res[$k]['ParentFxMemberID'];
              $one=Db::table('ceb_ShopMain')->field('Name')->where($wh)->find();
              $res[$k]['ParentFxMemberID']=$one['Name'];
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
              $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,19);
              // dump($res[$k]['ParentFxMemberID']);
            }
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_yongjin'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              // $one=bcmul($tem, 1, 2);
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              // dump($sin);exit;
              $res[$k]['tj_yongjin']=$sin;
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              $res[$k]['Total']=$sin;
          }
      // dump($res);exit;    
      // 不分页统计
	  $resNopage=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_yongjin,s.Name,m.ParentFxMemberID,m.EnrolName')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->join('ceb_Member m','a.MemberID = m.ID')
          ->where('a.Status','已完成')
          ->where($where)
          ->where($where2)
          ->order('TimeFd1 desc')
          ->select();
        $Total=0;
		$tj_yongjin=0;
	  foreach ($resNopage as $t) {
			$Total+=$t["Total"];
			$tj_yongjin+=$t["tj_yongjin"];
		}

      //自定义分页
      $sum=count($resNopage);
      $tem_page=$sum/20;
      $lastpage=ceil($tem_page);

	  $this->assign('Total', $Total);   
      $this->assign('tj_yongjin', -$tj_yongjin);  	  
	  $this->assign('AllCount',count($resNopage)); 
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('sum',$sum);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');    
    }
    //佣金明细详情报表下载
    public function commission_listExcel()  
    {
      
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
            $where2="(a.OrderCode like '%". $keyword."%' or m.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%' or m.NickName like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_yongjin,s.Name,m.ParentFxMemberID,m.EnrolName,m.NickName')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->join('ceb_Member m','a.MemberID = m.ID')
            ->where('a.Status','已完成')
            ->where($where)
            ->where($where2)
            ->order('TimeFd1 desc')
            ->select();
            foreach ($res as $k => $v) {
              if ($res[$k]['ParentFxMemberID']!='') {
                $wh['MemberID']=$res[$k]['ParentFxMemberID'];
                $one=Db::table('ceb_ShopMain')->field('Name')->where($wh)->find();
                $res[$k]['ParentFxMemberID']=$one['Name'];
                $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
                $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,19);
                // dump($res[$k]['ParentFxMemberID']);
              }
            }
            foreach ($res as $k => $v) {
              $tem=$res[$k]['tj_yongjin'];
              // $tem=abs($tem);
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
                

              }
                // $one=bcmul($tem, 1, 2);
                $one=abs($tem); 
                $sin=sprintf("%1\$.2f",$one);
                // dump($sin);exit;
                $res[$k]['tj_yongjin']=$sin;
            }  
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","收入店铺名称")->setCellValue("C1","支出店铺名称")->setCellValue("D1","会员名称")->setCellValue("E1","昵称")->setCellValue("F1","关联订单号")->setCellValue("G1","下单时间")->setCellValue("H1","订单金额")->setCellValue("I1","佣金金额")->setCellValue("J1","订单完成时间");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['ParentFxMemberID'])->setCellValue('C'.$i,$val['Name'])->setCellValue('D'.$i,$val['EnrolName'])->setCellValue('E'.$i,$val['NickName'])->setCellValue('F'.$i,$val['OrderCode'])->setCellValue('G'.$i,$val['TimeFd1'])->setCellValue('H'.$i,$val['Total'])->setCellValue('I'.$i,$val['tj_yongjin'])->setCellValue('J'.$i,$val['TimeFd4']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="佣金明细报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"佣金明细报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }


    //返现汇总报表
    public function Back_list($page=1)
    {
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%' or m.NickName like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
            ->page($page,20)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_fangxian,s.Name,m.EnrolName,m.NickName')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->join('ceb_Member m','a.MemberID = m.ID')
            ->where('a.Status','已完成')
            ->where($where)
            ->where($where2)
            ->order('TimeFd1 desc')
            ->select();

            foreach ($res as $k => $v) {
              $tem=$res[$k]['Total'];
              // $tem=abs($tem);
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
                

              }
                $one=abs($tem); 
                $sin=sprintf("%1\$.2f",$one);
                $res[$k]['Total']=$sin;
            }    

            foreach ($res as $k => $v) {
              $tem=$res[$k]['tj_fangxian'];
              // $tem=abs($tem);
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
                

              }
                $one=abs($tem); 
                $sin=sprintf("%1\$.2f",$one);
                $res[$k]['tj_fangxian']=$sin;
                $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
                $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,19);
            }
			
	// 不分页统计
	 $resNopage=Db::table('ceb_Order')
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_fangxian,s.Name,m.EnrolName')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->join('ceb_Member m','a.MemberID = m.ID')
            ->where('a.Status','已完成')
            ->where($where)
            ->where($where2)
            ->order('TimeFd1 desc')
            ->select();

            foreach ($res as $k => $v) {
              $tem=$res[$k]['Total'];
              // $tem=abs($tem);
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
                

              }
                $one=abs($tem); 
                $sin=sprintf("%1\$.2f",$one);
                $res[$k]['Total']=$sin;
            }    

            foreach ($res as $k => $v) {
              $tem=$res[$k]['tj_fangxian'];
              // $tem=abs($tem);
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
                

              }
                $one=abs($tem); 
                $sin=sprintf("%1\$.2f",$one);
                $res[$k]['tj_fangxian']=$sin;
                $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
                $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,19);
            }
       $Total=0;
	   $tj_fangxian=0;
	  foreach ($resNopage as $t) {
			$Total+=$t["Total"];
			$tj_fangxian+=$t["tj_fangxian"];
		}

      //自定义分页
      $sum=count($resNopage);
      $tem_page=$sum/20;
      $lastpage=ceil($tem_page); 
        
	  $this->assign('Total', $Total);
	  $this->assign('tj_fangxian', -$tj_fangxian);
	  $this->assign('AllCount',count($resNopage)); 
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('sum',$sum);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');  
    }
    //返现明细报表下载
    public function back_listExcel($page=1)  
    {
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%' or m.NickName like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_fangxian,s.Name,m.EnrolName,m.NickName')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->join('ceb_Member m','a.MemberID = m.ID')
          ->where('a.Status','已完成')
          ->where($where)
          ->where($where2)
          ->order('TimeFd1 desc')
          ->select();
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;              
            }
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              $res[$k]['Total']=$sin;
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_fangxian'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;             
            }
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              $res[$k]['tj_fangxian']=$sin;
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
              $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,19);
          }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","支出店铺名称")->setCellValue("C1","会员名称")->setCellValue("D1","关联订单号")->setCellValue("E1","下单时间")->setCellValue("F1","订单金额")->setCellValue("G1","返现金额")->setCellValue("H1","订单完成时间");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['EnrolName'])->setCellValue('D'.$i,$val['OrderCode'])->setCellValue('E'.$i,$val['TimeFd1'])->setCellValue('F'.$i,$val['Total'])->setCellValue('G'.$i,$val['tj_fangxian'])->setCellValue('H'.$i,$val['TimeFd4']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="返现明细报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"返现明细报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //订单资金分配
    public function order_dollar($page=1)
    {
        $where='';
        $where1='';
        $where2='';
        if (input('keyword')||input('start')||input('end')||input('payment_state')) {
          // dump(input(''));
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $payment_state = input('payment_state');
          if ($payment_state=="fou") {
              $payment_state="0";
          }

          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          $this->assign('payment_state',$payment_state);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
               $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($payment_state!='')
          {     
              $where1=array('a.isJieSuan'=>"$payment_state");
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
       $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_pingtaifuwufei,a.tj_fangxian,a.tj_yongjin,a.tj_feilv,a.tj_jiesuantotal,a.isJieSuan,a.tj_jiesuantotal,a.Status,a.TimeFd1,a.TimeFd4,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('a.Status','已完成')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->order('TimeFd4 desc')
          ->select();
          // dump($res);
          //返现金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_fangxian'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_fangxian']=$sin;
          }
           //运营金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_yunyingjine'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_yunyingjine']=$sin;
          }
          //结算金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_jiesuantotal']=$sin;
          }
          //佣金金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_yongjin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_yongjin']=$sin;
          }
          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_pingtaifuwufei']=$sin;
          }
          //费率
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_feilv'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_feilv']=$sin;
          }
          //支付状态
          foreach ($res as $k => $v) {
            $tem=$res[$k]['isJieSuan'];
            if ($tem=='0') {
              $res[$k]['isJieSuan']="未结";
            }elseif ($tem=='1') {
              $res[$k]['isJieSuan']="申请中";
            }elseif ($tem=='2') {
              $res[$k]['isJieSuan']="待付款";
            }elseif ($tem=='3') {
              $res[$k]['isJieSuan']="已结";
            }
            $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
            $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,16);
              
          }
      // 不分页统计
	  $resNopage=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_pingtaifuwufei,a.tj_fangxian,a.tj_yongjin,a.tj_feilv,a.tj_jiesuantotal,a.isJieSuan,a.tj_jiesuantotal,a.Status,a.TimeFd1,a.TimeFd4,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('a.Status','已完成')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->order('TimeFd4 desc')
          ->select();
          //返现金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_fangxian'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_fangxian']=$sin;
          }
           //运营金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_yunyingjine'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_yunyingjine']=$sin;
          }
          //结算金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_jiesuantotal']=$sin;
          }
          //佣金金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_yongjin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_yongjin']=$sin;
          }
          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_pingtaifuwufei']=$sin;
          }
          //费率
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_feilv'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_feilv']=$sin;
          }
          //支付状态
          foreach ($res as $k => $v) {
            $tem=$res[$k]['isJieSuan'];
            if ($tem=='0') {
              $res[$k]['isJieSuan']="未结";
            }elseif ($tem=='1') {
              $res[$k]['isJieSuan']="申请中";
            }elseif ($tem=='2') {
              $res[$k]['isJieSuan']="待付款";
            }elseif ($tem=='3') {
              $res[$k]['isJieSuan']="已结";
            }
            $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
            $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,16);
              
          }
	    $Total=0;
		$tj_yunyingjine=0;
		$tj_pingtaifuwufei=0;
		$tj_fangxian=0;
		$tj_yongjin=0;
		$tj_feilv=0;
		$tj_jiesuantotal=0;
	  foreach ($resNopage as $t) {
			$Total+=$t["Total"];
			$tj_yunyingjine+=$t["tj_yunyingjine"];
			$tj_pingtaifuwufei+=$t["tj_pingtaifuwufei"];
			$tj_fangxian+=$t["tj_fangxian"];
			$tj_yongjin+=$t["tj_yongjin"];
			$tj_feilv+=$t["tj_feilv"];
			$tj_jiesuantotal+=$t["tj_jiesuantotal"];
		}
      //自定义分页
      $sum=count($resNopage);
      $tem_page=$sum/20;
      $lastpage=ceil($tem_page);
        
	  $this->assign('Total', $Total);
	  $this->assign('tj_yunyingjine', $tj_yunyingjine);
	  $this->assign('tj_pingtaifuwufei', $tj_pingtaifuwufei);
	  $this->assign('tj_fangxian', $tj_fangxian);
	  $this->assign('tj_yongjin', $tj_yongjin);
	  $this->assign('tj_feilv', $tj_feilv);
	  $this->assign('tj_jiesuantotal', $tj_jiesuantotal);
	  $this->assign('AllCount',count($resNopage)); 
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('lastpage',$lastpage);
      return $this->fetch(''); 
    }

    public function order_dollarExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel"); 

        $where='';
        $where1='';
        $where2='';
        if (input('keyword')||input('start')||input('end')||input('payment_state')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $payment_state = input('payment_state');
          if ($payment_state=="fou") {
              $payment_state="0";
          }
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($payment_state!='')
          {     
              $where1=array('a.isJieSuan'=>"$payment_state");
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }

        $userName=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_pingtaifuwufei,a.tj_fangxian,a.tj_yongjin,a.tj_feilv,a.tj_jiesuantotal,a.isJieSuan,a.tj_jiesuantotal,a.Status,a.TimeFd1,a.TimeFd4,a.tj_jiesuantotal_n,a.tj_shouxufei,a.tj_fenpei,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('a.Status','已完成')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->order('TimeFd4 desc')
          ->select();
          //返现金额
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_fangxian'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_fangxian']=$sin;
          }
          //运营金额
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_yunyingjine'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_yunyingjine']=$sin;
          }
          //结算金额
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_jiesuantotal'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_jiesuantotal']=$sin;
          }
          //佣金金额
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_yongjin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_yongjin']=$sin;
          }
          //平台服务费
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_pingtaifuwufei']=$sin;
          }
          //费率
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_feilv'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_feilv']=$sin.'%';
          }

          //未结金额
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_jiesuantotal_n'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_jiesuantotal_n']=$sin;
          }

          //手续费
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_shouxufei'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_shouxufei']=$sin;
          }

          //订单分配资金
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['tj_fenpei'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $userName[$k]['tj_fenpei']=$sin;
          }

          //支付状态
          foreach ($userName as $k => $v) {
            $tem=$userName[$k]['isJieSuan'];
            if ($tem=='0') {
              $userName[$k]['isJieSuan']="未结";
            }elseif ($tem=='1') {
              $userName[$k]['isJieSuan']="申请中";
            }elseif ($tem=='2') {
              $userName[$k]['isJieSuan']="待付款";
            }elseif ($tem=='3') {
              $userName[$k]['isJieSuan']="已结";
            }
            $userName[$k]['TimeFd1']=substr($userName[$k]['TimeFd1'],0,16);
            $userName[$k]['TimeFd4']=substr($userName[$k]['TimeFd4'],0,16);
              
          }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($userName as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","订单编号")->setCellValue("C1","商家/餐厅")->setCellValue("D1","订单金额")->setCellValue("E1","运营金额")->setCellValue("F1","平台服务费")->setCellValue("G1","返现支出")->setCellValue("H1","佣金支出")->setCellValue("I1","费率%")->setCellValue("J1","结算金额")->setCellValue("K1","结算状态")->setCellValue("L1","未结算金额")->setCellValue("M1","手续费")->setCellValue("N1","订单资金分配")->setCellValue("O1","订单完成时间");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['OrderCode'])->setCellValue('C'.$i,$val['Name'])->setCellValue('D'.$i,$val['Total'])->setCellValue('E'.$i,$val['tj_yunyingjine'])->setCellValue('F'.$i,$val['tj_pingtaifuwufei'])->setCellValue('G'.$i,$val['tj_fangxian'])->setCellValue('H'.$i,$val['tj_yongjin'])->setCellValue('I'.$i,$val['tj_feilv'])->setCellValue('J'.$i,$val['tj_jiesuantotal'])->setCellValue('K'.$i,$val['isJieSuan'])->setCellValue('L'.$i,$val['tj_jiesuantotal_n'])->setCellValue('M'.$i,$val['tj_shouxufei'])->setCellValue('N'.$i,$val['tj_fenpei'])->setCellValue('O'.$i,$val['TimeFd4']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="订单资金分配报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"订单资金分配报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //餐厅日结
    public function dc_day_end($page=1)
    {

        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
       $res=Db::table('ceb_Order')
          // ->page($page,10)
          ->alias('a')
          ->field('a.OrderCode,a.Type,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_jiesuantotal,a.tj_fenpei,a.tj_pingtaifuwufei,a.tj_shouxufei,a.tj_jiesuantotal_y,a.tj_jiesuantotal_n,a.Status,a.TimeFd1,a.TimeFd4,a.ShopID,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("a.Type=1 and a.Status='已完成'")
          ->where($where)
          ->where($where2)
          ->order('TimeFd1 desc')
          ->select();


         

        //       $res=Db::query("select 
        // DATEPART(yyyy,o.[TimeFd1]) as YearInfo,
        // DATEPART(m,o.[TimeFd1]) as MonthInfo,
        // DATEPART(d,o.[TimeFd1]) as DayInfo,
        // sum(ordercount_t) as ordercount_tt,
        // sum(ordertotal_t) as ordertotal_tt,
        // sum(tj_yunyingjine_t) as tj_yunyingjine_tt,
        // sum(tj_pingtaifuwufei_t) as tj_pingtaifuwufei_tt,
        // sum(tj_yongjin_t) as tj_yongjin_tt,
        // sum(tj_feilv_t) as tj_feilv_tt,
        // sum(tj_jiesuantotal_t) as tj_jiesuantotal_tt,
        // sum(tj_jiesuantotal_y_t) as tj_jiesuantotal_y_tt,
        // sum(tj_jiesuantotal_n_t) as tj_jiesuantotal_n_tt,
        // sum(tj_shouxufei_t) as tj_shouxufei_tt,
        // sum(tj_fenpei_t) as tj_fenpei_tt,
        // o.shopid,
        // o.Name
        // from (select p.Name,q.shopid,q.timefd1,
        // count(0) as ordercount_t,
        // sum(Total) as ordertotal_t,
        // sum(tj_yunyingjine) as tj_yunyingjine_t,
        // sum(tj_pingtaifuwufei) as tj_pingtaifuwufei_t,
        // sum(tj_fangxian) as tj_fangxian_t,
        // sum(tj_yongjin) as tj_yongjin_t,
        // sum(tj_feilv) as tj_feilv_t,
        // sum(tj_jiesuantotal) as tj_jiesuantotal_t,
        // sum(tj_jiesuantotal_y) as tj_jiesuantotal_y_t,
        // sum(tj_jiesuantotal_n) as tj_jiesuantotal_n_t,
        // sum(tj_shouxufei) as tj_shouxufei_t,
        // sum(tj_fenpei) as tj_fenpei_t
        // from ceb_order q left join ceb_shopmain p on p.id=q.shopid 
        // where 1=1 and q.[Type]=1 and q.[Status] ='已完成' group by shopid,q.timefd1,Name) o where 1=1 group by  DATEPART(yyyy,o.[TimeFd1]),DATEPART(m,o.[TimeFd1]),DATEPART(d,o.[TimeFd1]),o.shopid,o.Name
        //   order by YearInfo desc, MonthInfo desc,DayInfo desc");

 




          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_pingtaifuwufei']=$sin;
          }
          //手续费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_shouxufei']=$sin;
          }
          //已结金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_jiesuantotal_y']=$sin;
          }
          //未结金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_jiesuantotal_n']=$sin;
          }

          $arr_field = array('tj_yunyingjine','tj_jiesuantotal','tj_fenpei','tj_pingtaifuwufei','tj_shouxufei','tj_jiesuantotal_y','tj_jiesuantotal_n','Total');//因为你需要统计的字段很多，都是做一样的操作，所以写个数组循环会更简洁一些；
          $newdata = array();//新的接收数据数组
          foreach($res as $k => $v){//展开结果集
              $time = explode(' ',$v['TimeFd1'])[0];//这个是你要用来统计的时间，我不知道你要哪个字段，你可以自己替换，用空格分解，只要前面的日期区分
            if(!isset($newdata[$v['Name'].'###'.$time]['order_num'])){//第一次创建的时候是没有值的，用isset判断，防止数据错误
              $newdata[$v['Name'].'###'.$time]['order_num'] = 0;//赋初值，避免类型错误
            }
            if($v['OrderCode'] != ''){//判断订单号是否存在来进行累加，因为你的订单号唯一
              $newdata[$v['Name'].'###'.$time]['order_num']++;
            }

            $newdata[$v['Name'].'###'.$time]['name']=$v['Name'];
            $newdata[$v['Name'].'###'.$time]['time']=$time;

            foreach($arr_field as $kk => $vv){//展开需要累加操作的字段
              if(!isset($newdata[$v['Name'].'###'.$time][$vv])){//同样，isset是用来判断第一次的，然后赋初值
                $newdata[$v['Name'].'###'.$time][$vv] = $v[$vv]*1;//*1是为了保持数据为数字类型
              }else{
                $newdata[$v['Name'].'###'.$time][$vv] += $v[$vv]*1;
              }
            }
          }

          //已结金额
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['Total']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_y']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_yunyingjine'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_yunyingjine']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_fenpei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_fenpei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_pingtaifuwufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_shouxufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_n']=$sin;
          }


          // dump($newdata);exit;
          $sons = array_chunk($newdata, 20);
          // dump($sons);exit;
          if (empty($newdata)) {
            $p=$page;
            $res_data=$sons;
          }else{
            $p=$page-1;
            // dump($sons[$p]);
            $res_data=$sons[$p];//装第一页（第N页）数据的新数组
          }
          
          
          // dump($res_data);
          // //S('sons',$sons,3600);//缓存起来，跟session用法差不多，3600秒即1小时，看你需求，如果数据变动不大更可以时间改的更大点
          // // dump($res);
          //分页在这里
          // $p = I('get.p') > 0? I('get.p'):1;//三元运算 默认1
          // $p = input('p') > 0? input('p'):1;
          // dump($p);

          // //分解数组，每页20条
          // $page = $sons[$p-1];//取页码，前面默认1是怕有负数，这里-1是因为下标0开始
          // //最好做个缓存
          // // dump($page);
          
          //分页自定义
          $num=count($newdata); 
          // dump($num);
          $tem_page=$num/20;
          // dump($tem_page);
          $lastpage=ceil($tem_page);
          // $this->assign('newdata',$newdata);
          $this->assign('res_data',$res_data);
          $this->assign('num',$num);
          $this->assign('page',$page);
          $this->assign('lastpage',$lastpage);
          return $this->fetch(''); 
    } 
    //餐厅日结报表下载
    public function dc_day_endExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel"); 

        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end); 
              $where=array('a.TimeFd1'=>array("elt",$end));          
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
            $res=Db::table('ceb_Order')
              ->alias('a')
              ->field('a.OrderCode,a.Type,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_jiesuantotal,a.tj_fenpei,a.tj_pingtaifuwufei,a.tj_shouxufei,a.tj_jiesuantotal_y,a.tj_jiesuantotal_n,a.Status,a.TimeFd1,a.TimeFd4,a.ShopID,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where("a.Type=1 and a.Status='已完成'")
              ->where($where)
              ->where($where2)
              ->order('TimeFd1 desc')
              ->select();

          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_pingtaifuwufei']=$sin;
          }
          //手续费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_shouxufei']=$sin;
          }
          //已结金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_jiesuantotal_y']=$sin;
          }
          //未结金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_jiesuantotal_n']=$sin;
          }

          $arr_field = array('tj_yunyingjine','tj_jiesuantotal','tj_fenpei','tj_pingtaifuwufei','tj_shouxufei','tj_jiesuantotal_y','tj_jiesuantotal_n','Total');//因为你需要统计的字段很多，都是做一样的操作，所以写个数组循环会更简洁一些；
          $newdata = array();//新的接收数据数组
          foreach($res as $k => $v){//展开结果集
              $time = explode(' ',$v['TimeFd1'])[0];//这个是你要用来统计的时间，我不知道你要哪个字段，你可以自己替换，用空格分解，只要前面的日期区分
            if(!isset($newdata[$v['Name'].'###'.$time]['order_num'])){//第一次创建的时候是没有值的，用isset判断，防止数据错误
              $newdata[$v['Name'].'###'.$time]['order_num'] = 0;//赋初值，避免类型错误
            }
            if($v['OrderCode'] != ''){//判断订单号是否存在来进行累加，因为你的订单号唯一
              $newdata[$v['Name'].'###'.$time]['order_num']++;
            }

            $newdata[$v['Name'].'###'.$time]['name']=$v['Name'];
            $newdata[$v['Name'].'###'.$time]['time']=$time;

            foreach($arr_field as $kk => $vv){//展开需要累加操作的字段
              if(!isset($newdata[$v['Name'].'###'.$time][$vv])){//同样，isset是用来判断第一次的，然后赋初值
                $newdata[$v['Name'].'###'.$time][$vv] = $v[$vv]*1;//*1是为了保持数据为数字类型
              }else{
                $newdata[$v['Name'].'###'.$time][$vv] += $v[$vv]*1;
              }
            }
          }

          //已结金额
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['Total']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_y']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_yunyingjine'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_yunyingjine']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_fenpei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_fenpei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_pingtaifuwufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_shouxufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_n']=$sin;
          }
        
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($newdata as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","下单日期")->setCellValue("B1","餐厅名称")->setCellValue("C1","订单数量")->setCellValue("D1","订单总额")->setCellValue("E1","运营金额")->setCellValue("F1","结算总额")->setCellValue("G1","分配总额")->setCellValue("H1","平台服务费")->setCellValue("I1","手续费")->setCellValue("J1","已结金额")->setCellValue("K1","未结金额");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['time'])->setCellValue('B'.$i,$val['name'])->setCellValue('C'.$i,$val['order_num'])->setCellValue('D'.$i,$val['Total'])->setCellValue('E'.$i,$val['tj_yunyingjine'])->setCellValue('F'.$i,$val['tj_jiesuantotal'])->setCellValue('G'.$i,$val['tj_fenpei'])->setCellValue('H'.$i,$val['tj_pingtaifuwufei'])->setCellValue('I'.$i,$val['tj_shouxufei'])->setCellValue('J'.$i,$val['tj_jiesuantotal_y'])->setCellValue('K'.$i,$val['tj_jiesuantotal_n']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="餐厅日结账报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"餐厅日结账报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件


    }


    //商店日结
    public function shop_day_end($page=1)
    {
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
          // ->page($page,10)
          ->alias('a')
          ->field('a.OrderCode,a.Type,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_jiesuantotal,a.tj_fenpei,a.tj_pingtaifuwufei,a.tj_shouxufei,a.tj_jiesuantotal_y,a.tj_jiesuantotal_n,a.Status,a.TimeFd1,a.TimeFd4,a.ShopID,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("a.Type=0 and a.Status='已完成'")
          ->where($where)
          ->where($where2)
          ->order('TimeFd1 desc')
          ->select();

        $arr_field = array('tj_yunyingjine','tj_jiesuantotal','tj_fenpei','tj_pingtaifuwufei','tj_shouxufei','tj_jiesuantotal_y','tj_jiesuantotal_n','Total');//因为你需要统计的字段很多，都是做一样的操作，所以写个数组循环会更简洁一些；
          $newdata = array();//新的接收数据数组
          foreach($res as $k => $v){//展开结果集
              $time = explode(' ',$v['TimeFd1'])[0];//这个是你要用来统计的时间，我不知道你要哪个字段，你可以自己替换，用空格分解，只要前面的日期区分
            if(!isset($newdata[$v['Name'].'###'.$time]['order_num'])){//第一次创建的时候是没有值的，用isset判断，防止数据错误
              $newdata[$v['Name'].'###'.$time]['order_num'] = 0;//赋初值，避免类型错误
            }
            if($v['OrderCode'] != ''){//判断订单号是否存在来进行累加，因为你的订单号唯一
              $newdata[$v['Name'].'###'.$time]['order_num']++;
            }

            $newdata[$v['Name'].'###'.$time]['name']=$v['Name'];
            $newdata[$v['Name'].'###'.$time]['time']=$time;

            foreach($arr_field as $kk => $vv){//展开需要累加操作的字段
              if(!isset($newdata[$v['Name'].'###'.$time][$vv])){//同样，isset是用来判断第一次的，然后赋初值
                $newdata[$v['Name'].'###'.$time][$vv] = $v[$vv]*1;//*1是为了保持数据为数字类型
              }else{
                $newdata[$v['Name'].'###'.$time][$vv] += $v[$vv]*1;
              }
            }
          }

          //已结金额
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['Total']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_y']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_yunyingjine'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_yunyingjine']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_fenpei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_fenpei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_pingtaifuwufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_shouxufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_n']=$sin;
          }

		  
    // 不分页统计
	  $sons = array_chunk($newdata, 20);
          // dump($sons);exit;
          if (empty($newdata)) {
            $p=$page;
            $res_data=$sons;
          }else{
            $p=$page-1;
            // dump($sons[$p]);
            $res_data=$sons[$p];//装第一页（第N页）数据的新数组
          }
    //自定义分页      
	  $num=count($newdata); 
    $tem_page=$num/20;
    $lastpage=ceil($tem_page);

	  $order_num=0;
		$Total=0;
		$tj_yunyingjine=0;
		$tj_jiesuantotal=0;
		$tj_fenpei=0;
		$tj_pingtaifuwufei=0;
		$tj_shouxufei=0;
		$tj_jiesuantotal_y=0;
		$tj_jiesuantotal_n=0;
	  foreach ($newdata as $t) {
			$order_num+=$t["order_num"];
			$Total+=$t["Total"];
			$tj_yunyingjine+=$t["tj_yunyingjine"];
			$tj_jiesuantotal+=$t["tj_jiesuantotal"];
			$tj_fenpei+=$t["tj_fenpei"];
			$tj_pingtaifuwufei+=$t["tj_pingtaifuwufei"];
			$tj_shouxufei+=$t["tj_shouxufei"];
			$tj_jiesuantotal_y+=$t["tj_jiesuantotal_y"];
			$tj_jiesuantotal_n+=$t["tj_jiesuantotal_n"];
		}
	  $this->assign('order_num', $order_num);
	  $this->assign('Total', $Total);
	  $this->assign('tj_yunyingjine', $tj_yunyingjine);
	  $this->assign('tj_jiesuantotal', $tj_jiesuantotal);
	  $this->assign('tj_fenpei', $tj_fenpei);
	  $this->assign('tj_pingtaifuwufei', $tj_pingtaifuwufei);
	  $this->assign('tj_shouxufei', $tj_shouxufei);
	  $this->assign('tj_jiesuantotal_y', $tj_jiesuantotal_y);
	  $this->assign('tj_jiesuantotal_n', $tj_jiesuantotal_n);
	  $this->assign('AllCount',count($newdata));

    $this->assign('newdata',$newdata);
    $this->assign('res_data',$res_data);
    $this->assign('num',$num);
    $this->assign('page',$page);
    $this->assign('lastpage',$lastpage);
    return $this->fetch(''); 

    }

    //商店日结报表下载
    public function shop_day_endExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel"); 

        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
            $res=Db::table('ceb_Order')
          // ->page($page,10)
          ->alias('a')
          ->field('a.OrderCode,a.Type,a.ID,a.Count,a.Total,a.tj_yunyingjine,a.tj_jiesuantotal,a.tj_fenpei,a.tj_pingtaifuwufei,a.tj_shouxufei,a.tj_jiesuantotal_y,a.tj_jiesuantotal_n,a.Status,a.TimeFd1,a.TimeFd4,a.ShopID,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("a.Type=0 and a.Status='已完成'")
          ->where($where)
          ->where($where2)
          ->order('TimeFd1 desc')
          ->select();
          //平台服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // dump($sin);exit;
              $res[$k]['tj_pingtaifuwufei']=$sin;
          }
          //手续费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_shouxufei']=$sin;
          }
          //已结金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_jiesuantotal_y']=$sin;
          }
          //未结金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['tj_jiesuantotal_n']=$sin;
          }

          $arr_field = array('tj_yunyingjine','tj_jiesuantotal','tj_fenpei','tj_pingtaifuwufei','tj_shouxufei','tj_jiesuantotal_y','tj_jiesuantotal_n','Total');//因为你需要统计的字段很多，都是做一样的操作，所以写个数组循环会更简洁一些；
          $newdata = array();//新的接收数据数组
          foreach($res as $k => $v){//展开结果集
              $time = explode(' ',$v['TimeFd1'])[0];//这个是你要用来统计的时间，我不知道你要哪个字段，你可以自己替换，用空格分解，只要前面的日期区分
            if(!isset($newdata[$v['Name'].'###'.$time]['order_num'])){//第一次创建的时候是没有值的，用isset判断，防止数据错误
              $newdata[$v['Name'].'###'.$time]['order_num'] = 0;//赋初值，避免类型错误
            }
            if($v['OrderCode'] != ''){//判断订单号是否存在来进行累加，因为你的订单号唯一
              $newdata[$v['Name'].'###'.$time]['order_num']++;
            }

            $newdata[$v['Name'].'###'.$time]['name']=$v['Name'];
            $newdata[$v['Name'].'###'.$time]['time']=$time;

            foreach($arr_field as $kk => $vv){//展开需要累加操作的字段
              if(!isset($newdata[$v['Name'].'###'.$time][$vv])){//同样，isset是用来判断第一次的，然后赋初值
                $newdata[$v['Name'].'###'.$time][$vv] = $v[$vv]*1;//*1是为了保持数据为数字类型
              }else{
                $newdata[$v['Name'].'###'.$time][$vv] += $v[$vv]*1;
              }
            }
          }

          //已结金额
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['Total']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_y'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_y']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_yunyingjine'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_yunyingjine']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_fenpei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_fenpei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_pingtaifuwufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_pingtaifuwufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_shouxufei'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_shouxufei']=$sin;
          }
          foreach ($newdata as $k => $v) {
            $tem=$newdata[$k]['tj_jiesuantotal_n'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $newdata[$k]['tj_jiesuantotal_n']=$sin;
          }
        
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($newdata as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","下单日期")->setCellValue("B1","商店名称")->setCellValue("C1","订单数量")->setCellValue("D1","订单总额")->setCellValue("E1","运营金额")->setCellValue("F1","结算总额")->setCellValue("G1","分配总额")->setCellValue("H1","平台服务费")->setCellValue("I1","手续费")->setCellValue("J1","已结金额")->setCellValue("K1","未结金额");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['time'])->setCellValue('B'.$i,$val['name'])->setCellValue('C'.$i,$val['order_num'])->setCellValue('D'.$i,$val['Total'])->setCellValue('E'.$i,$val['tj_yunyingjine'])->setCellValue('F'.$i,$val['tj_jiesuantotal'])->setCellValue('G'.$i,$val['tj_fenpei'])->setCellValue('H'.$i,$val['tj_pingtaifuwufei'])->setCellValue('I'.$i,$val['tj_shouxufei'])->setCellValue('J'.$i,$val['tj_jiesuantotal_y'])->setCellValue('K'.$i,$val['tj_jiesuantotal_n']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="商店日结账报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"商店日结账报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 
    }

    //会员余额
    public function Member_yue($page=1)
    {

        $where='';
        $where2='';
        if (input('keyword')||input('type')) {
          // dump(input(''));exit;
          $type = input('type');

          $keyword = input('keyword');

          $this->assign('type',$type);
          $this->assign('keyword',$keyword);
          if ($type=='fou') {
            $type="0"; 
          }
          if($type!='')
          {
              // $where["m.MemberType"]=$type;
              $where=array('m.MemberType'=>array("eq",$type));
          }

          if($keyword!='')
          {
              $where2="(m.EnrolName like '%". $keyword."%' or m.TrueName like '%". $keyword."%' or m.NickName like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Member')
            ->page($page,20)
            ->alias('m')
            ->field('m.ID,m.MemberType,m.EnrolName,m.TrueName,m.NickName,m.Sex,m.CreateTime,a.Name,m.CurMoney1,m.CurMoney2,m.CurMoney3,m.CurMoney')
            ->join('ceb_ShopMain a','a.MemberID = m.ID','left')
            ->where($where)
            ->where($where2)
            ->order('CurMoney1 desc,CurMoney2 desc,CurMoney3 desc,CurMoney desc,CreateTime desc')
            ->select();
        //获取消费次数及金额
        foreach ($res as $k => $v) {
           $wh['MemberID']=$res[$k]['ID'];
           $tem_data=Db::table('ceb_order')
                   ->field('ID,Status,Total')
                   ->where("Status","已完成")
                   ->where($wh)
                   ->select();
                $xftotal=0;
                foreach ($tem_data as $t) {
                    $xftotal+=$t["Total"];
                }        
                $xfnumber=count($tem_data); 
                $res[$k]['xfnum']=$xfnumber;
                $res[$k]['xftotal']=$xftotal;      

           // dump($xiaofei);       
        }    
            // dump($res);exit;
           //消费金额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['xftotal'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['xftotal']=$sin;
          } 
          //返现
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney1'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney1']=$sin;
          }
          //佣金
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney2'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney2']=$sin;
          }
          //退款
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney3'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney3']=$sin;
          }

          //总余额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney']=$sin;
          }

          foreach ($res as $k => $v) {
            $one=($res[$k]['CurMoney2']-0);
            $thre=($res[$k]['CurMoney3']-0);
            $tem=$one+$thre;
            $res[$k]['ketiyue']=$tem;
            if ($res[$k]['ketiyue']=='0') {
              $res[$k]['ketiyue']="0.00";
            }
            $res[$k]['notiyue']=$res[$k]['CurMoney1'];
            $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,16);
          
          }
      // 不分页统计
	  $resNopage=Db::table('ceb_Member')
            ->alias('m')
            ->field('m.ID,m.MemberType,m.EnrolName,m.TrueName,m.NickName,m.Sex,m.CreateTime,a.Name,m.CurMoney1,m.CurMoney2,m.CurMoney3,m.CurMoney')
            ->join('ceb_ShopMain a','a.MemberID = m.ID','left')
            ->where($where)
            ->where($where2)
            ->order('CreateTime desc')
            ->select();
            //获取消费次数及金额
            // foreach ($resNopage as $k => $v) {
            //    $wh['MemberID']=$resNopage[$k]['ID'];
            //    $tem_data=Db::table('ceb_order')
            //            ->field('ID,Status,Total')
            //            ->where("Status","已完成")
            //            ->where($wh)
            //            ->select();
            //         $xftotal=0;
            //         foreach ($tem_data as $t) {
            //             $xftotal+=$t["Total"];
            //         }        
            //         $xfnumber=count($tem_data); 
            //         $resNopage[$k]['xfnum']=$xfnumber;
            //         $resNopage[$k]['xftotal']=$xftotal;      

            //    // dump($xiaofei);       
            // }    
            // dump($res);
           //消费金额
          // foreach ($resNopage as $k => $v) {
          //   $tem=$res[$k]['xftotal'];
          //   if (substr($tem,0,1)==".") {
          //     $tem="0".$tem;
          //   }
          //     $sin=sprintf("%1\$.2f",$tem);
          //     $res[$k]['xftotal']=$sin;
          // }
          //返现
          foreach ($resNopage as $k => $v) {
            $tem=$resNopage[$k]['CurMoney1'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $resNopage[$k]['CurMoney1']=$sin;
          }
          //佣金
          foreach ($resNopage as $k => $v) {
            $tem=$resNopage[$k]['CurMoney2'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $resNopage[$k]['CurMoney2']=$sin;
          }
          //退款
          foreach ($resNopage as $k => $v) {
            $tem=$resNopage[$k]['CurMoney3'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $resNopage[$k]['CurMoney3']=$sin;
          }

          //总余额
          foreach ($resNopage as $k => $v) {
            $tem=$resNopage[$k]['CurMoney'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $resNopage[$k]['CurMoney']=$sin;
          }

          foreach ($resNopage as $k => $v) {
            $one=($resNopage[$k]['CurMoney2']-0);
            $thre=($resNopage[$k]['CurMoney3']-0);
            $tem=$one+$thre;
            $resNopage[$k]['ketiyue']=$tem;
            if ($resNopage[$k]['ketiyue']=='0') {
              $resNopage[$k]['ketiyue']="0.00";
            }
            $resNopage[$k]['notiyue']=$resNopage[$k]['CurMoney1'];
            $resNopage[$k]['CreateTime']=substr($resNopage[$k]['CreateTime'],0,16);
          
          }
      // $xfnum=0;
      // $xftotal=0;
      $CurMoney1=0;
	  $CurMoney2=0;
	  $CurMoney3=0;
	  $CurMoney=0;
	  $ketiyue=0;
	  $notiyue=0;
	  foreach ($resNopage as $t) {
        // $xfnum+=$t["xfnum"];
        // $xftotal+=$t["xftotal"];
		$CurMoney1+=$t["CurMoney1"];
		$CurMoney2+=$t["CurMoney2"];
		$CurMoney3+=$t["CurMoney3"];
		$CurMoney+=$t["CurMoney"];
		$ketiyue+=$t["ketiyue"];
		$notiyue+=$t["notiyue"];
	   }

       //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);  
      // $this->assign('xfnum', $xfnum);
      // $this->assign('xftotal', $xftotal);
	  $this->assign('CurMoney1', $CurMoney1);
	  $this->assign('CurMoney2', $CurMoney2);
	  $this->assign('CurMoney3', $CurMoney3);
	  $this->assign('CurMoney', $CurMoney);
	  $this->assign('ketiyue', $ketiyue);
	  $this->assign('notiyue', $notiyue);
	  $this->assign('AllCount',count($resNopage)); 
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('lastpage',$lastpage);
      return $this->fetch(''); 

    }
    //会员余额下载
    public function Member_yueExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where2='';
        if (input('keyword')||input('type')) {
          // dump(input(''));exit;
          $type = input('type');
         
          $keyword = input('keyword');

          $this->assign('type',$type);
          $this->assign('keyword',$keyword);
          if ($type=='fou') {
            $type="0"; 
          }
          if($type!='')
          {
              // $where["m.MemberType"]=$type;
              $where=array('m.MemberType'=>array("eq",$type));
          }

          if($keyword!='')
          {
              $where2="(m.EnrolName like '%". $keyword."%' or m.TrueName like '%". $keyword."%' or m.NickName like '%". $keyword."%')";
          }

        }  
          $res=Db::table('ceb_Member')
          ->alias('m')
          ->field('m.ID,m.MemberType,m.EnrolName,m.TrueName,m.NickName,m.Sex,m.CreateTime,a.Name,m.CurMoney1,m.CurMoney2,m.CurMoney3,m.CurMoney')
          ->join('ceb_ShopMain a','a.MemberID = m.ID','left')
          ->where($where)
          ->where($where2)
          ->order('CreateTime desc')
          ->select();
          //返现
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney1'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney1']=$sin;
          }
          //佣金
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney2'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney2']=$sin;
          }
          //退款
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney3'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney3']=$sin;
          }

          //总余额
          foreach ($res as $k => $v) {
            $tem=$res[$k]['CurMoney'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['CurMoney']=$sin;
          }

          foreach ($res as $k => $v) {
            $one=($res[$k]['CurMoney2']-0);
            $thre=($res[$k]['CurMoney3']-0);
            $tem=$one+$thre;
            $res[$k]['ketiyue']=$tem;
            if ($res[$k]['ketiyue']=='0') {
              $res[$k]['ketiyue']="0.00";
            }
            $res[$k]['notiyue']=$res[$k]['CurMoney1'];
            $res[$k]['CreateTime']=substr($res[$k]['CreateTime'],0,16);
          
          }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","会员类型")->setCellValue("C1","会员名称")->setCellValue("D1","真是姓名")->setCellValue("E1","昵称")->setCellValue("F1","性别")->setCellValue("G1","注册时间")->setCellValue("H1","店铺名称")->setCellValue("I1","返现金额")->setCellValue("J1","佣金金额")->setCellValue("K1","退款金额")->setCellValue("L1","总余额")->setCellValue("M1","可提现")->setCellValue("N1","不可提现");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['MemberType'])->setCellValue('C'.$i,$val['EnrolName'])->setCellValue('D'.$i,$val['TrueName'])->setCellValue('E'.$i,$val['NickName'])->setCellValue('F'.$i,$val['Sex'])->setCellValue('G'.$i,$val['CreateTime'])->setCellValue('H'.$i,$val['Name'])->setCellValue('I'.$i,$val['CurMoney1'])->setCellValue('J'.$i,$val['CurMoney2'])->setCellValue('K'.$i,$val['CurMoney3'])->setCellValue('L'.$i,$val['CurMoney'])->setCellValue('M'.$i,$val['ketiyue'])->setCellValue('N'.$i,$val['notiyue']);//表格数据   
               $i++;  
           }  
        //创建生成的格式  
        header('Content-Type: application/vnd.ms-excel');
		    header("Content-Disposition: attachment;filename=\"会员余额报表.xls\"");
		    header('Cache-Control: max-age=0'); 
		    $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  
    }


    //点餐订单明细报表
    public function dc_order_detail($page=1)
    {
           
        $where='';
        $where1='';
        $where2='';
        $where3='';
        $where4='';
        if (input('keyword')||input('start')||input('end')||input('pay_state')||input('maidan_state')||input('order_state')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $pay_state = input('pay_state');
          $maidan_state = input('maidan_state');
          $order_state = input('order_state');


          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          $this->assign('order_state',$order_state);
          $this->assign('maidan_state',$maidan_state);
          $this->assign('pay_state',$pay_state);
          if ($pay_state=='fou') {
              $pay_state="0";
          }
          if($start!='')
          {
              // $where["o.TimeFd1"]=array("egt",$start);
              $where=array('o.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where=array('o.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('o.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($pay_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where1=array('o.PayWay'=>"$pay_state");           
          }

          if($maidan_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where3=array('o.DecFd3'=>"$maidan_state");           
          }

          if($order_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where4=array('o.Status'=>"$order_state");           
          }

          if($keyword!='')
          {
              $where2="(o.OrderCode like '%". $keyword."%' or o.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
            ->page($page,20)
            ->alias('o')
            ->field('o.ID,o.Type,o.EnrolName,o.OrderCode,o.TimeFd1,o.PayWay,o.Status,o.PayStatus,o.Total,o.Total,t.Price,o.Count,o.From_XianJin,o.From_FanXian,o.From_XianJin_Type,o.DecFd3,s.Name,t.Name as tName')
            ->join('ceb_OrderItem t','t.OrderID = o.ID','left')
            ->join('ceb_ShopMain s','o.ShopID = s.ID')
            ->where('o.Type','1')
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->where($where3)
            ->where($where4)
            ->order('ID desc')
            ->select();
            // dump($res);exit;
            foreach ($res as $k => $v) {
            $order=$res[$k]['OrderCode'];
            // $str1="O18020711123080_f1";
            $one_order = explode("_",$order)[0];
            // dump($one_order);
            // dump($order);exit;
            $wh['OrderCode']=$one_order;
            $one = Db::table('ceb_Order')
                 ->field('From_XianJin,From_FanXian,From_XianJin_Type')
                 ->where($wh)
                 ->find();
                 // dump($one);exit;
                 // $res[$k]['From_XianJin']=$one['From_XianJin'];
                 $res[$k]['From_FanXian']=$one['From_FanXian'];
                 if ($res[$k]['From_XianJin_Type']=='0') {
                  $res[$k]['weixin']=$one['From_XianJin'];
                  $res[$k]['zhifubao']="0.00";
                 }elseif ($res[$k]['From_XianJin_Type']=='1') {
                  $res[$k]['weixin']="0.00";
                  $res[$k]['zhifubao']=$one['From_XianJin'];
                 }
            // dump($res[$k]['From_XianJin']);exit;      
          }
          // dump($res);exit;
          //微信支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['weixin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['weixin']=$sin;
            if ($res[$k]['PayStatus']=="未付") {
                $res[$k]['weixin']="0.00";
              }  
          }
          //支付宝
          foreach ($res as $k => $v) {
            $tem=$res[$k]['zhifubao'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['zhifubao']=$sin;
          }
          //反现支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['From_FanXian'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['From_FanXian']=$sin;
          }
          //订单金额支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['Total']=$sin;
          }
          //单价
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Price'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['Price']=$sin;
          }
          //直接付款OR扫码支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['DecFd3'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // $res[$k]['DecFd3']=$sin;
            if ($sin=='0.00') {
              $res[$k]['DecFd3']="点餐买单";
            }elseif ($sin=='0.01') {
              $res[$k]['DecFd3']="直接买单";
            }
          }

        // 不分页统计
		$resNopage=Db::table('ceb_Order')
            ->alias('o')
            ->field('o.ID,o.Type,o.EnrolName,o.OrderCode,o.TimeFd1,o.PayWay,o.Status,o.PayStatus,o.Total,o.Total,t.Price,o.Count,o.From_XianJin,o.From_FanXian,o.From_XianJin_Type,o.DecFd3,s.Name,t.Name as tName')
            ->join('ceb_OrderItem t','t.OrderID = o.ID','left')
            ->join('ceb_ShopMain s','o.ShopID = s.ID')
            ->where('o.Type','1')
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->where($where3)
            ->where($where4)
            ->order('ID desc')
            ->select();
         
		// dump($resNopage);exit;
		  $Total=0;
		  foreach ($resNopage as $t) {
				$Total+=$t["Total"];
			}
        //分页自定义
        $num=count($resNopage); 
        $tem_page=$num/20;
        $lastpage=ceil($tem_page); 

		$this->assign('Total', $Total);
		$this->assign('AllCount',count($resNopage)); 		
        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('lastpage',$lastpage);
        return $this->fetch('');

    }
    //点餐订单明细报表下载 
    public function dc_order_detailExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel");
        $where='';
        $where1='';
        $where2='';
        $where3='';
        $where4='';
        if (input('keyword')||input('start')||input('end')||input('pay_state')||input('maidan_state')||input('order_state')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $pay_state = input('pay_state');
          $maidan_state = input('maidan_state');
          $order_state = input('order_state');
          if ($pay_state=='fou') {
              $pay_state="0";
          }
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["o.TimeFd1"]=array("egt",$start);
              $where=array('o.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where=array('o.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('o.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($pay_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where1=array('o.PayWay'=>"$pay_state");           
          }

          if($maidan_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where3=array('o.DecFd3'=>"$maidan_state");           
          }

          if($order_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where4=array('o.Status'=>"$order_state");           
          }

          if($keyword!='')
          {
              $where2="(o.OrderCode like '%". $keyword."%' or o.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
            ->alias('o')
            ->field('o.ID,o.Type,o.EnrolName,o.OrderCode,o.TimeFd1,o.PayWay,o.Status,o.PayStatus,o.Total,o.Total,t.Price,o.Count,o.From_XianJin,o.From_FanXian,o.From_XianJin_Type,o.DecFd3,s.Name,t.Name as tName')
            ->join('ceb_OrderItem t','t.OrderID = o.ID','left')
            ->join('ceb_ShopMain s','o.ShopID = s.ID')
            ->where('o.Type','1')
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->where($where3)
            ->where($where4)
            ->order('ID desc')
            ->select();
            foreach ($res as $k => $v) {
            $order=$res[$k]['OrderCode'];
            // $str1="O18020711123080_f1";
            $one_order = explode("_",$order)[0];
            // dump($one_order);
            // dump($order);exit;
            $wh['OrderCode']=$one_order;
            $one = Db::table('ceb_Order')
                 ->field('From_XianJin,From_FanXian,From_XianJin_Type')
                 ->where($wh)
                 ->find();
                 // dump($one);exit;
                 // $res[$k]['From_XianJin']=$one['From_XianJin'];
                 $res[$k]['From_FanXian']=$one['From_FanXian'];
                 if ($res[$k]['From_XianJin_Type']=='0') {
                  $res[$k]['weixin']=$one['From_XianJin'];
                  $res[$k]['zhifubao']="0.00";
                 }elseif ($res[$k]['From_XianJin_Type']=='1') {
                  $res[$k]['weixin']="0.00";
                  $res[$k]['zhifubao']=$one['From_XianJin'];
                 }
            // dump($res[$k]['From_XianJin']);exit;      
          }
          //微信支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['weixin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['weixin']=$sin;
            if ($res[$k]['PayStatus']=="未付") {
                $res[$k]['weixin']="0.00";
            }
          }
          //支付宝
          foreach ($res as $k => $v) {
            $tem=$res[$k]['zhifubao'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['zhifubao']=$sin;
          }
          //反现支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['From_FanXian'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['From_FanXian']=$sin;
          }
          //订单金额支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Total'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['Total']=$sin;
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'], 0,19);
          }
          //单价
          foreach ($res as $k => $v) {
            $tem=$res[$k]['Price'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['Price']=$sin;
          }
          //支付方式
          foreach ($res as $k => $v) {
            $tem=$res[$k]['PayWay'];
            if ($tem=='0') {
              $res[$k]['PayWay']="现金";
            }else if($tem=='1'){
              $res[$k]['PayWay']="会员卡";  
            }else if($tem=='2'){
              $res[$k]['PayWay']="支付宝";  
            }else if($tem=='3'){
              $res[$k]['PayWay']="微支付";  
            }else if($tem=='4'){
              $res[$k]['PayWay']="代金券";  
            }else if($tem=='5'){
              $res[$k]['PayWay']="其他";  
            }
              
          }
          //直接付款OR扫码支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['DecFd3'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              // $res[$k]['DecFd3']=$sin;
            if ($sin=='0.00') {
              $res[$k]['DecFd3']="点餐买单";
            }elseif ($sin=='0.01') {
              $res[$k]['DecFd3']="直接买单";
            }
          }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","餐厅名称")->setCellValue("C1","会员名称")->setCellValue("D1","订单号")->setCellValue("E1","下单日期")->setCellValue("F1","支付方式")->setCellValue("G1","买单方式")->setCellValue("H1","订单状态")->setCellValue("I1","结算状态")->setCellValue("J1","订单金额")->setCellValue("K1","微信支付")->setCellValue("L1","支付宝")->setCellValue("M1","余额返现")->setCellValue("N1","菜品名称")->setCellValue("O1","单价")->setCellValue("P1","数量")->setCellValue("Q1","小计");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['EnrolName'])->setCellValue('D'.$i,$val['OrderCode'])->setCellValue('E'.$i,$val['TimeFd1'])->setCellValue('F'.$i,$val['PayWay'])->setCellValue('G'.$i,$val['DecFd3'])->setCellValue('H'.$i,$val['Status'])->setCellValue('I'.$i,$val['PayStatus'])->setCellValue('J'.$i,$val['Total'])->setCellValue('K'.$i,$val['weixin'])->setCellValue('L'.$i,$val['zhifubao'])->setCellValue('M'.$i,$val['From_FanXian'])->setCellValue('N'.$i,$val['tName'])->setCellValue('O'.$i,$val['Price'])->setCellValue('P'.$i,$val['Count'])->setCellValue('Q'.$i,$val['Total']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="点餐订单明细报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"点餐订单明细报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }


    //商品菜品销量汇总报表
    public function sales_volume($page=1)
    {
      // dump(12313);exit;
      // $res=Db::table('ceb_Product')
      //     // ->page($page,10)
      //     ->alias('p')
      //     ->field('p.ID,P.ProductClass,p.Title,p.SaleNum,p.Price,s.Name,s.SaleNum as dSaleNum')
      //     ->join('ceb_Order o','o.ShopID = p.ID','left')
      //     ->join('ceb_ShopMain s','p.ShopID = s.ID','left')
      //     ->where('p.Title','广州瑞紫测试')
      //     // ->limit(50)
      //     // ->order('ID desc')
      //     ->select();

      $res=Db::query("select p.[ShopID],p.[Price],p.[Title] as [pTitle],tj.*,dic1.[Name] as TypeName,sss.Name as SName
 from [ceb_Product] p left join (SELECT item.ProductID, SUM(item.Count) AS TotalNum, SUM(item.Total) 
AS TotalAmount, SUM(1) AS OrderCount FROM  ceb_OrderItem AS item LEFT OUTER JOIN  ceb_Order AS o 
ON item.OrderCode = o.OrderCode WHERE o.Status = '已完成' GROUP BY item.ProductID, item.OrderCode) 
tj on p.[ID] = tj.[ProductID] left join [ceb_Dictionary] dic1 on p.[ProductClass] = dic1.[Code] left join 
[ceb_ShopMain] sss on sss.[ID]= p.[ShopID] order by TotalNum desc");     
      // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');    
    }

    //普通订单明细报表
    public function pu_order_detail($page=1)
    {
        $where='';
        $where1='';
        $where2='';
        $where3='';
        $where4='';
        if (input('keyword')||input('start')||input('end')||input('pay_state')||input('maidan_state')||input('order_state')) {
          // dump(input(''));
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $pay_state = input('pay_state');
          $maidan_state = input('maidan_state');
          $order_state = input('order_state');


          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          $this->assign('order_state',$order_state);
          $this->assign('maidan_state',$maidan_state);
          $this->assign('pay_state',$pay_state);
          if ($pay_state=="fou") {
            $pay_state='0';
         
          }
          if ($maidan_state=="fou") {
            $maidan_state='0';
          }
          // dump($pay_state);
          if($start!='')
          {
              // $where["o.TimeFd1"]=array("egt",$start);
              $where=array('o.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end); 
              $where=array('o.TimeFd1'=>array("elt",$end));          
          }

          if($start!='' && $end!='')
          {     
              $where=array('o.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($pay_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where1=array('o.PayWay'=>"$pay_state");           
          }

          if($maidan_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where3=array('o.isJieSuan'=>"$maidan_state");           
          }

          if($order_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where4=array('o.Status'=>"$order_state");           
          }

          if($keyword!='')
          {
              $where2="(o.OrderCode like '%". $keyword."%' or o.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
      
        $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('o')
          ->field('o.ID,o.Type,o.EnrolName,o.OrderCode,o.TimeFd1,o.TimeFd4,o.Receiver,o.Address,o.Mobile,o.PayWay,o.Status,o.PayStatus,o.isJieSuan,o.Total,o.Total,t.Price,o.Count,s.Name,t.Name as tName,o.From_XianJin,o.From_FanXian,o.From_XianJin_Type')
          ->join('ceb_OrderItem t','t.OrderID = o.ID')
          ->join('ceb_ShopMain s','o.ShopID = s.ID')
          ->where('o.Type','0')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->where($where4)
          ->order('TimeFd1 desc')
          ->select();
          foreach ($res as $k => $v) {
            $order=$res[$k]['OrderCode'];
            // $str1="O18020711123080_f1";
            $one_order = explode("_",$order)[0];
            // dump($one_order);
            // dump($order);exit;
            $wh['OrderCode']=$one_order;
            $one = Db::table('ceb_Order')
                 ->field('From_XianJin,From_FanXian,From_XianJin_Type')
                 ->where($wh)
                 ->find();
                 // dump($one);exit;
                 // $res[$k]['From_XianJin']=$one['From_XianJin'];
                 $res[$k]['From_FanXian']=$one['From_FanXian'];
                 if ($res[$k]['From_XianJin_Type']=='0') {
                  $res[$k]['weixin']=$one['From_XianJin'];
                  $res[$k]['zhifubao']="0.00";
                 }elseif ($res[$k]['From_XianJin_Type']=='1') {
                  $res[$k]['weixin']="0.00";
                  $res[$k]['zhifubao']=$one['From_XianJin'];
                 }
            // dump($res[$k]['From_XianJin']);exit;      
          }
          // dump($res);exit;
          foreach ($res as $k => $v) {
            if ($res[$k]['isJieSuan']==0) {
              $res[$k]['isJieSuan']="未结";
            }else{
              $res[$k]['isJieSuan']="已结";
            }
          }

          //微信支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['weixin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['weixin']=$sin;
              if ($res[$k]['PayStatus']=="未付") {
                $res[$k]['weixin']="0.00";
              }
          }
          //支付宝
          foreach ($res as $k => $v) {
            $tem=$res[$k]['zhifubao'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['zhifubao']=$sin;
          }

          //反现支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['From_FanXian'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['From_FanXian']=$sin;
          }


      // 不分页统计
	  $resNopage=Db::table('ceb_Order')
          ->alias('o')
          ->field('o.ID,o.Type,o.EnrolName,o.OrderCode,o.TimeFd1,o.TimeFd4,o.Receiver,o.Address,o.Mobile,o.PayWay,o.Status,o.PayStatus,o.isJieSuan,o.Total,o.Total,t.Price,o.Count,s.Name,t.Name as tName,o.From_XianJin,o.From_FanXian,o.From_XianJin_Type')
          ->join('ceb_OrderItem t','t.OrderID = o.ID')
          ->join('ceb_ShopMain s','o.ShopID = s.ID')
          ->where('o.Type','0')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->where($where4)
          ->order('TimeFd1 desc')
          ->select();
      //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);    
      $this->assign('AllCount',count($resNopage));     
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');
    
    }
    //普通订单明细表下载
    public function pu_order_detailExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where1='';
        $where2='';
        $where3='';
        $where4='';
        if (input('keyword')||input('start')||input('end')||input('pay_state')||input('maidan_state')||input('order_state')) {
          // dump(input(''));
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $pay_state = input('pay_state');
          $maidan_state = input('maidan_state');
          $order_state = input('order_state');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["o.TimeFd1"]=array("egt",$start);
              $where=array('o.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where=array('o.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('o.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($pay_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where1=array('o.PayWay'=>"$pay_state");           
          }

          if($maidan_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where3=array('o.isJieSuan'=>"$maidan_state");           
          }

          if($order_state!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where4=array('o.Status'=>"$order_state");           
          }

          if($keyword!='')
          {
              $where2="(o.OrderCode like '%". $keyword."%' or o.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
      
        $res=Db::table('ceb_Order')
          ->alias('o')
          ->field('o.ID,o.Type,o.EnrolName,o.OrderCode,o.TimeFd1,o.TimeFd4,o.Receiver,o.Address,o.Mobile,o.PayWay,o.Status,o.PayStatus,o.isJieSuan,o.Total,o.Total,t.Price,o.Count,s.Name,t.Name as tName,o.From_XianJin,o.From_FanXian,o.From_XianJin_Type')
          ->join('ceb_OrderItem t','t.OrderID = o.ID')
          ->join('ceb_ShopMain s','o.ShopID = s.ID')
          ->where('o.Type','0')
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->where($where4)
          ->order('TimeFd1 desc')
          ->select();
          foreach ($res as $k => $v) {
            $order=$res[$k]['OrderCode'];
            // $str1="O18020711123080_f1";
            $one_order = explode("_",$order)[0];
            // dump($one_order);
            // dump($order);exit;
            $wh['OrderCode']=$one_order;
            $one = Db::table('ceb_Order')
                 ->field('From_XianJin,From_FanXian,From_XianJin_Type')
                 ->where($wh)
                 ->find();
                 // dump($one);exit;
                 // $res[$k]['From_XianJin']=$one['From_XianJin'];
                 $res[$k]['From_FanXian']=$one['From_FanXian'];
                 if ($res[$k]['From_XianJin_Type']=='0') {
                  $res[$k]['weixin']=$one['From_XianJin'];
                  $res[$k]['zhifubao']="0.00";
                 }elseif ($res[$k]['From_XianJin_Type']=='1') {
                  $res[$k]['weixin']="0.00";
                  $res[$k]['zhifubao']=$one['From_XianJin'];
                 }
            // dump($res[$k]['From_XianJin']);exit;      
          }
          // dump($res);exit;
          foreach ($res as $k => $v) {
            if ($res[$k]['isJieSuan']==0) {
              $res[$k]['isJieSuan']="未结";
            }else{
              $res[$k]['isJieSuan']="已结";
            }
          }

          //微信支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['weixin'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['weixin']=$sin;
              if ($res[$k]['PayStatus']=="未付") {
                $res[$k]['weixin']="0.00";
              }
          }
          //支付宝
          foreach ($res as $k => $v) {
            $tem=$res[$k]['zhifubao'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['zhifubao']=$sin;
          }

          //反现支付
          foreach ($res as $k => $v) {
            $tem=$res[$k]['From_FanXian'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['From_FanXian']=$sin;
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['PayWay'];
              if ($tem==0) {
                $res[$k]['PayWay']="现金";
              }elseif ($tem==1) {
                $res[$k]['PayWay']="会员卡";
              }elseif ($tem==2) {
                $res[$k]['PayWay']="支付宝";
              }elseif ($tem==3) {
                $res[$k]['PayWay']="微支付";
              }elseif ($tem==4) {
                $res[$k]['PayWay']="代金券";
              }elseif ($tem==5) {
                $res[$k]['PayWay']="其他";
              }
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'], 0,16);
              $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'], 0,16);
            }
            // dump($res);exit;  
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称 
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","会员名称")->setCellValue("D1","订单号")->setCellValue("E1","下单日期")->setCellValue("F1","收货人")->setCellValue("G1","收货地址")->setCellValue("H1","联系电话")->setCellValue("I1","支付方式")->setCellValue("J1","订单状态")->setCellValue("K1","结算状态")->setCellValue("L1","订单金额")->setCellValue("M1","微信支付")->setCellValue("N1","支付宝")->setCellValue("O1","余额支付")->setCellValue("P1","商品名称")->setCellValue("Q1","单价")->setCellValue("R1","数量")->setCellValue("S1","订单完成时间")->setCellValue("T1","小计");//表格数据 
               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['EnrolName'])->setCellValue('D'.$i,$val['OrderCode'])->setCellValue('E'.$i,$val['TimeFd1'])->setCellValue('F'.$i,$val['Receiver'])->setCellValue('G'.$i,$val['Address'])->setCellValue('H'.$i,$val['Mobile'])->setCellValue('I'.$i,$val['PayWay'])->setCellValue('J'.$i,$val['Status'])->setCellValue('K'.$i,$val['PayStatus'])->setCellValue('L'.$i,$val['Total'])->setCellValue('M'.$i,$val['weixin'])->setCellValue('N'.$i,$val['zhifubao'])->setCellValue('O'.$i,$val['From_FanXian'])->setCellValue('P'.$i,$val['tName'])->setCellValue('Q'.$i,$val['Price'])->setCellValue('R'.$i,$val['Count'])->setCellValue('S'.$i,$val['TimeFd4'])->setCellValue('T'.$i,$val['Total']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="普通订单明细报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"普通订单明细报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5"); 
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
    
    
    //商城月报表
    public function shopmon_report($page=1)  
    {
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["o.TimeFd1"]=array("egt",$start);
              $where=array('o.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);
              $where=array('o.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('o.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(o.OrderCode like '%". $keyword."%' or o.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
      
          $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('o')
          ->field('o.ID,o.Type,o.OrderCode,t.Name as tName,t.Price,t.Count,t.Total as tTotal,o.EnrolName,o.TimeFd4,o.TimeFd1,o.Receiver,o.Address,o.Mobile,o.Total,o.Status,o.PayWay,o.PayStatus,o.isJieSuan,s.Name')
          ->join('ceb_OrderItem t','t.OrderID = o.ID','left')
          ->join('ceb_ShopMain s','o.ShopID = s.ID','left')
          ->where("o.Type=0 and o.Status='已完成'")
          ->where($where)
          ->where($where2)
          ->order('TimeFd4 desc')
          ->select();
          foreach ($res as $k => $v) {
            if ($res[$k]['isJieSuan']==0) {
              $res[$k]['isJieSuan']="未结";
            }else{
              $res[$k]['isJieSuan']="未结";
            }
            
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['PayWay'];
              if ($tem==0) {
                $res[$k]['PayWay']="现金";
              }elseif ($tem==1) {
                $res[$k]['PayWay']="会员卡";
              }elseif ($tem==2) {
                $res[$k]['PayWay']="支付宝";
              }elseif ($tem==3) {
                $res[$k]['PayWay']="微支付";
              }elseif ($tem==4) {
                $res[$k]['PayWay']="代金券";
              }elseif ($tem==5) {
                $res[$k]['PayWay']="其他";
              }
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'], 0,16);
              $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'], 0,16);
            }
        // 不分页统计
		$resNopage=Db::table('ceb_Order')
          ->alias('o')
          ->field('o.ID,o.Type,o.OrderCode,t.Name as tName,t.Price,t.Count,t.Total as tTotal,o.EnrolName,o.TimeFd4,o.TimeFd1,o.Receiver,o.Address,o.Mobile,o.Total,o.Status,o.PayWay,o.PayStatus,o.isJieSuan,s.Name')
          ->join('ceb_OrderItem t','t.OrderID = o.ID','left')
          ->join('ceb_ShopMain s','o.ShopID = s.ID','left')
          ->where("o.Type=0 and o.Status='已完成'")
          ->where($where)
          ->where($where2)
          ->order('TimeFd4 desc')
          ->select();
		    $Total=0;
		  foreach ($resNopage as $t) {
				$Total+=$t["Total"];
			}
      //分页自定义
      $num=count($resNopage); 
      $tem_page=$num/20;
      $lastpage=ceil($tem_page);

		  $this->assign('Total', $Total);
		  $this->assign('AllCount',count($resNopage)); 
      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');     
    }

    public function shopmon_reportExcel($page=1)  
    {
      
        vendor("phpexcel.PHPExcel");  
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["o.TimeFd1"]=array("egt",$start);
              $where=array('o.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["o.TimeFd1"]=array("elt",$end);  
              $where=array('o.TimeFd1'=>array("elt",$end));         
          }

          if($start!='' && $end!='')
          {     
              $where=array('o.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(o.OrderCode like '%". $keyword."%' or o.EnrolName like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
      
          $res=Db::table('ceb_Order')
          ->alias('o')
          ->field('o.ID,o.Type,o.OrderCode,t.Name as tName,t.Price,t.Count,t.Total as tTotal,o.EnrolName,o.TimeFd4,o.TimeFd1,o.Receiver,o.Address,o.Mobile,o.Total,o.Status,o.PayWay,o.PayStatus,o.isJieSuan,s.Name')
          ->join('ceb_OrderItem t','t.OrderID = o.ID','left')
          ->join('ceb_ShopMain s','o.ShopID = s.ID','left')
          ->where("o.Type=0 and o.Status='已完成'")
          ->where($where)
          ->where($where2)
          ->order('TimeFd4 desc')
          ->select();
          foreach ($res as $k => $v) {
            if ($res[$k]['isJieSuan']==0) {
              $res[$k]['isJieSuan']="未结";
            }else{
              $res[$k]['isJieSuan']="未结";
            }
            
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['PayWay'];
              if ($tem==0) {
                $res[$k]['PayWay']="现金";
              }elseif ($tem==1) {
                $res[$k]['PayWay']="会员卡";
              }elseif ($tem==2) {
                $res[$k]['PayWay']="支付宝";
              }elseif ($tem==3) {
                $res[$k]['PayWay']="微支付";
              }elseif ($tem==4) {
                $res[$k]['PayWay']="代金券";
              }elseif ($tem==5) {
                $res[$k]['PayWay']="其他";
              }
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'], 0,16);
              $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'], 0,16);
            }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","订单号")->setCellValue("C1","商店名称")->setCellValue("D1","单价")->setCellValue("E1","数量")->setCellValue("F1","小计")->setCellValue("G1","会员名")->setCellValue("H1","订单完成时间")->setCellValue("I1","下单日期")->setCellValue("J1","收货人")->setCellValue("K1","收货地址")->setCellValue("L1","联系电话")->setCellValue("M1","订单金额")->setCellValue("n1","成本")->setCellValue("o1","利润")->setCellValue("p1","订单状态")->setCellValue("q1","支付方式")->setCellValue("r1","店铺名称")->setCellValue("s1","结算状态");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['OrderCode'])->setCellValue('C'.$i,$val['tName'])->setCellValue('D'.$i,$val['Price'])->setCellValue('E'.$i,$val['Count'])->setCellValue('F'.$i,$val['tTotal'])->setCellValue('G'.$i,$val['EnrolName'])->setCellValue('H'.$i,$val['TimeFd4'])->setCellValue('I'.$i,$val['TimeFd1'])->setCellValue('J'.$i,$val['Receiver'])->setCellValue('K'.$i,$val['Address'])->setCellValue('L'.$i,$val['Mobile'])->setCellValue('M'.$i,$val['Total'])->setCellValue('n'.$i,'')->setCellValue('o'.$i,'')->setCellValue('p'.$i,$val['Status'])->setCellValue('q'.$i,$val['PayStatus'])->setCellValue('r'.$i,$val['Name'])->setCellValue('s'.$i,$val['isJieSuan']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="商城月报表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"商城月报表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //返现预警报表
    public function defend($page=1)
    {
        $where='';
        $where2='';
        if (input('keyword')||input('start')||input('end')) {
          // dump(input(''));exit;
          $start = input('start');
          $end = input('end');
          $keyword = input('keyword');
          $this->assign('start',$start);
          $this->assign('end',$end);
          $this->assign('keyword',$keyword);
          if($start!='')
          {
              // $where["a.TimeFd1"]=array("egt",$start);
              $where=array('a.TimeFd1'=>array("egt",$start));
          }

          if($end!='')
          {
              // $where["a.TimeFd1"]=array("elt",$end);
              $where=array('a.TimeFd1'=>array("elt",$end));           
          }

          if($start!='' && $end!='')
          {     
              $where=array('a.TimeFd1'=>array('between time',[$start,$end]));
          }

          if($keyword!='')
          {
              $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
          }

        }
        $res=Db::table('ceb_Order')
            ->page($page,20)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_fangxian,s.Name,m.EnrolName')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->join('ceb_Member m','a.MemberID = m.ID')
            ->where("a.Status='已完成' and Type=0")
            ->where('a.tj_fangxian','<=',-10)
            ->where($where)
            ->where($where2)
            ->order('TimeFd1 desc')
            ->select();
            // dump($res);exit;
            foreach ($res as $k => $v) {
              $order=$res[$k]['OrderCode'];
              $fin=Db::table('ceb_OrderItem')
                  ->where("OrderCode='$order'")
                  ->find();

              $res[$k]['DE_ID']=$fin['ID'];
            }
            foreach ($res as $k => $v) {
              $tem=$res[$k]['tj_fangxian'];
              // $tem=abs($tem);
              if (substr($tem,0,1)==".") {
                $tem="0".$tem;
                

              }
                $one=abs($tem); 
                $sin=sprintf("%1\$.2f",$one);
                $res[$k]['tj_fangxian']=$sin;
                $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,19);
                $res[$k]['TimeFd4']=substr($res[$k]['TimeFd4'],0,19);
            }
      // 不分页统计
	  $resNopage=Db::table('ceb_Order')
            ->page($page,20)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Status,a.TimeFd1,a.TimeFd4,a.tj_fangxian,s.Name,m.EnrolName')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->join('ceb_Member m','a.MemberID = m.ID')
            ->where("a.Status='已完成' and Type=0")
            ->where('a.tj_fangxian','<=',-10)
            ->where($where)
            ->where($where2)
            ->order('TimeFd1 desc')
            ->select();
	   $Total=0;
	   $tj_fangxian=0;
	  foreach ($resNopage as $t) {
			$Total+=$t["Total"];
			$tj_fangxian+=$t["tj_fangxian"];
		}
    //分页自定义
    $num=count($resNopage); 
    $tem_page=$num/20;
    $lastpage=ceil($tem_page);
	  $this->assign('Total', $Total);
	  $this->assign('tj_fangxian', -$tj_fangxian);
	  $this->assign('AllCount',count($resNopage)); 
    $this->assign('res2',$res);
    $this->assign('page',$page);
    $this->assign('lastpage',$lastpage);
    return $this->fetch(''); 
      
    }

    //返现预警报表详情
    public function defend_info($page=1)
    {
      if (input('')) {
        // dump(input(''));
        $order=input('order_id');
        // dump($order);
        $res=Db::table('ceb_OrderItem')
          ->alias('a')
          ->field('a.ID,a.OrderID,a.DemoImg,a.Name,a.Price,a.Count,a.Total as unTotal,b.EnrolName,b.OrderCode,b.Receiver,b.Address,b.Mobile,b.Tel,b.Total,b.Status,b.PayStatus,b.PayWay,b.NvrFd1,b.NvrFd2')
          // ->field('a.*')
          ->join('ceb_Order b','a.OrderID = b.ID','left')
          ->where("a.OrderCode='$order'")
          ->find();
        $tem_id=$res['OrderID'];
        // dump($tem_id);
        $data_info=Db::table('ceb_OrderItem')
             ->where("OrderID='$tem_id'")
             ->select();
             // dump($data_info);exit; 
        if (!empty($res['NvrFd1'])) {
            $tem=$res['NvrFd1'];
            $wh_com['Remark']=$tem;
            $com=Db::table('ceb_Dictionary')->field('Remark,Name')->where($wh_com)->find();
            $res['uname']=$com['Name'];
            // dump($com);exit;
          }else{
            $res['uname']=$res['NvrFd1'];
          }
          if ($res['PayWay']==0) {
            $res['PayWay']="现金";
          }elseif ($res['PayWay']==1) {
            $res['PayWay']="会员卡";
          }elseif ($res['PayWay']==2) {
            $res['PayWay']="支付宝";
          }elseif ($res['PayWay']==3) {
            $res['PayWay']="微支付";
          }elseif ($res['PayWay']==4) {
            $res['PayWay']="代金券";
          }else{
            $res['PayWay']="其他";
          }

          // 快递查询(参数设置)
          $post_data = array();
          $post_data["customer"] = '59E2B21B895B16C4FAF1D3B5F1462EC6';
          $key= 'RLgUdDwy8445' ;

          //测试单号
          // $com='tnt';
          // $num='382351534';
          // $com='yunda';
          // $num='3831831056925';
          
          //获取单号
          $com=$res['NvrFd1'];
          $num=$res['NvrFd2'];
          $post_data["param"] = "{'com':'$com','num':'$num'}";
          //快递查询
          $url='http://poll.kuaidi100.com/poll/query.do';
          $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
          $post_data["sign"] = strtoupper($post_data["sign"]);
          $o="";
          foreach ($post_data as $k=>$v)
          {
              $o.= "$k=".urlencode($v)."&";       //默认UTF-8编码格式
          }
          $post_data=substr($o,0,-1);
          $ch = curl_init();
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_HEADER, 0);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_URL,$url);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
              $result = curl_exec($ch);
              $data = str_replace("\"",'"',$result );
              $sin=explode('"data":', $data);
              if (isset($sin[1])) {
                $sin_time=$sin[1];
                $sin_f_time=explode(',', $sin_time);
                foreach ($sin_f_time as $k => $v) {
                    
                    if (substr($sin_f_time[$k],0,2)=='"f') {
                        $sin_f_time[$k]='"'.substr($sin_f_time[$k],9,20);
                    }
                    if (substr($sin_f_time[$k],0,2)=='"c') {
                        $sin_f_time[$k]='{"'.substr($sin_f_time[$k],11,200);
                    }
                    if (substr($sin_f_time[$k],0,4)=='[{"t' || substr($sin_f_time[$k],0,3)=='{"t') {
                    unset($sin_f_time[$k]);
                    }
                }
                // dump($sin_f_time);
                $this->assign('express',$sin_f_time);    
            }else{
                $sin=explode('"message":', $data);
                foreach ($sin as $k => $v) {
                    if (substr($sin[$k], 0,2)!='{"') {
                        $sin[$k]='{'.$sin[$k];
                    }
                    if (substr($sin[$k], 0,3)=='{"r') {
                        unset($sin[$k]);
                    }
                }
                // dump($sin);
                $this->assign('express',$sin);   
            }      
        // dump($res);
        $this->assign('res2',$res);
        $this->assign('data_info',$data_info);
        return $this->fetch('');    
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
