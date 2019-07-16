<?php
namespace app\admin\controller;
use think\Db;
class Order extends \think\Controller
{
    public function order_list($page=1)
    {
      $where='';
      $where1='';
      $where2='';
      $where3='';
      if (input('keyword')||input('order')||input('payment')||input('start')||input('end')) {
        // dump(input(''));
        $start = input('start');
        $end = input('end');
        $payment = input('payment');
        $status = input('order');
        $keyword = input('keyword');        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('payment',$payment);
        $this->assign('status',$status);
        $this->assign('keyword',$keyword);
        
       
        // dump($start);
        // dump($end);
        // dump($payment);
        // dump($status);
        // dump($keyword);


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

        if($payment!='')
        {
            // $where["a.PayStatus"]=array("=",$payment);
            $where3=array('a.PayStatus'=>array("eq",$payment));
        }
        if($status!='')
        {
            // $where["a.Status"]=array("=",$status); 
            $where1=array('a.Status'=>array("eq",$status));
        }
        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
        }

      }
      
      
        $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("Type=0")
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->order('TimeFd1 desc')
          ->select();
        $data=Db::table('ceb_Order')
               ->alias('a')
               ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.MemberID,s.Name')
               ->join('ceb_ShopMain s','a.ShopID = s.ID')
               ->where("Type=0")
               ->where($where)
               ->where($where1)
               ->where($where2)
               ->where($where3)
               ->select();
        $ressum=count($data);
        // dump($ressum);
        // 金额数据处理
        foreach ($res as $k => $v) {
          $tem=$res[$k]['Total'];
          if (substr($tem,0,1)==".") {
            $tem="0".$tem;
          }
          $one=abs($tem); 
          $sin=sprintf("%1\$.2f",$one);
          $res[$k]['Total']=$sin;
        }       
        //复购值
        if ($ressum=='0') {
           $percentage='0.00%';
        
        }else{
            $order_all=count($data);
            $NewArray=array();    
            foreach ($data as $k) {
               if (!isset($NewArray[$k['MemberID']])) {
                   $NewArray[$k['MemberID']]=$k;
               }
               if(!isset($NewArray[$k['MemberID']]['son'])){$NewArray[$k['MemberID']]['son']=array();}
                array_push($NewArray[$k['MemberID']]['son'],$k);

            }
            foreach ($NewArray as $k => $v) {
                $NewArray[$k]['sum']=count($NewArray[$k]['son']);
            }
            $all=count($NewArray);

            $tem=$NewArray;
            foreach ($tem as $k => $v) {
                if ($tem[$k]['sum']<2) {
                    unset($tem[$k]);
                }
            }
            $two=count($tem);
            $twoall=$tem;
            $sum = 0;  
            foreach($twoall as $item){  
                $sum += (int) $item['sum'];  
            } 
            // dump($all);
            // dump($order_all);
            // dump($sum);
            // dump($two);
            $shang=$sum-$two;
          // dump($shang);
          $result=($shang/$order_all)*100;
          $list=($result/4);
          $one=abs($list); 
          $percentage=sprintf("%1\$.2f",$one)."%";
          // dump($result);
          // dump($list);
          // dump($percentage);
          // exit;
            // $percentage='100';

        }
        //需求数据统计处理  
        $alltotal=Db::table('ceb_Order')
               ->alias('a')
               ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.MemberID,s.Name')
               ->join('ceb_ShopMain s','a.ShopID = s.ID')
               ->where("Type=0")
               ->where($where)
               ->where($where1)
               ->where($where2)
               ->where($where3)
               ->sum('Total');          
        $tem_page=$ressum/20;
        $lastpage=ceil($tem_page); 

        $this->assign('res2',$res);
        $this->assign('page',$page);
        $this->assign('ressum',$ressum);
        $this->assign('percentage',$percentage);
        $this->assign('alltotal',$alltotal);
        $this->assign('lastpage',$lastpage);
        return $this->fetch(''); 
        
    }
    //普通订单导出
    public function order_listExcel()  
    {
      vendor("phpexcel.PHPExcel");  
      $where='';
      $where1='';
      $where2='';
      $where3='';
      if (input('keyword')||input('status')||input('payment')||input('start')||input('end')) {
        // dump(input(''));exit;
        $start = input('start');
        $end = input('end');
        $payment = input('payment');
        $status = input('status');
        $keyword = input('keyword');

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

        if($payment!='')
        {
            // $where["a.PayStatus"]=array("=",$payment);
            $where3=array('a.PayStatus'=>array("eq",$payment));
        }
        if($status!='')
        {
            // $where["a.Status"]=array("=",$status);
            $where1=array('a.Status'=>array("eq",$status));
        }
        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
        }

      }
      
      
        $res=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("Type=0")
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->order('ID desc')
          ->select();
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","订单号")->setCellValue("D1","订单金额")->setCellValue("E1","收货人")->setCellValue("F1","联系电话")->setCellValue("G1","收货地址")->setCellValue("H1","是否付款")->setCellValue("I1","订单状态")->setCellValue("J1","下单时间");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['OrderCode'])->setCellValue('D'.$i,$val['Total'])->setCellValue('E'.$i,$val['Receiver'])->setCellValue('F'.$i,$val['Mobile'])->setCellValue('G'.$i,$val['Address'])->setCellValue('H'.$i,$val['PayStatus'])->setCellValue('I'.$i,$val['Status'])->setCellValue('J'.$i,$val['TimeFd1']);//表格数据   
               $i++;  
           }  
          

        // header('Content-Type: application/vnd.ms-excel');
        // header("Content-Disposition: attachment;filename=\"普通订单表.xls\"");
        // header('Cache-Control: max-age=0'); 
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 
        
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"普通订单表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //点餐订单表
    public function order_dclist($page=1)
    {
      $where='';
      $where1='';
      $where2='';
      $where3='';
      if (input('keyword')||input('order')||input('payment')||input('start')||input('end')) {
       $start = input('start');
        $end = input('end');
        $payment = input('payment');
        $status = input('order');
        $keyword = input('keyword');
        
        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('payment',$payment);
        $this->assign('status',$status);
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

        if($payment!='')
        {
            // $where["a.PayStatus"]=array("=",$payment);
            $where3=array('a.PayStatus'=>array("eq",$payment));
        }
        if($status!='')
        {
            // $where["a.Status"]=array("=",$status);
            $where1=array('a.Status'=>array("eq",$status));
        }
        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
        }

      } 

      $res=Db::table('ceb_Order')
          ->page($page,20)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("Type=1")
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->order('ID desc')
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
          $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
      } 

      $data=Db::table('ceb_Order')
           ->alias('a')
           ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.MemberID,s.Name')
           ->join('ceb_ShopMain s','a.ShopID = s.ID')
           ->where("Type=1")
           ->where($where)
           ->where($where1)
           ->where($where2)
           ->where($where3)
           ->order('ID desc')
           ->select();

       $ressum=count($data);
        // dump($ressum);       
        //复购值
        if ($ressum=='0') {
           $percentage='0.00%';
        
        }else{
              $order_all=count($data);
            $NewArray=array();    
            foreach ($data as $k) {
               if (!isset($NewArray[$k['MemberID']])) {
                   $NewArray[$k['MemberID']]=$k;
               }
               if(!isset($NewArray[$k['MemberID']]['son'])){$NewArray[$k['MemberID']]['son']=array();}
                array_push($NewArray[$k['MemberID']]['son'],$k);

            }
            foreach ($NewArray as $k => $v) {
                $NewArray[$k]['sum']=count($NewArray[$k]['son']);
            }
            $all=count($NewArray);

            $tem=$NewArray;
            foreach ($tem as $k => $v) {
                if ($tem[$k]['sum']<2) {
                    unset($tem[$k]);
                }
            }
            $two=count($tem);
            $twoall=$tem;
            $sum = 0;  
            foreach($twoall as $item){  
                $sum += (int) $item['sum'];  
            } 
            // dump($all);
            // dump($order_all);
            // dump($sum);
            // dump($two);
            $shang=$sum-$two;
          // dump($shang);
          $result=($shang/$order_all)*100;
          $list=($result/4);
          $one=abs($list); 
          $percentage=sprintf("%1\$.2f",$one)."%";
          // dump($result);
          // dump($list);
          // dump($percentage);
          // exit;
            // $percentage='100';

        }    
        //需求数据统计处理  
        $alltotal=Db::table('ceb_Order')
               ->alias('a')
               ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,a.MemberID,s.Name')
               ->join('ceb_ShopMain s','a.ShopID = s.ID')
               ->where("Type=1")
               ->where($where)
               ->where($where1)
               ->where($where2)
               ->where($where3)
               ->sum('Total');          
        $tem_page=$ressum/20;
        $lastpage=ceil($tem_page);

      $this->assign('res2',$res);
      $this->assign('page',$page);
      $this->assign('ressum',$ressum);
      $this->assign('percentage',$percentage); 
      $this->assign('alltotal',$alltotal);
      $this->assign('lastpage',$lastpage);
      return $this->fetch('');
      
    }
    //点餐订单导出
    public function order_dclistExcel()  
    {
      vendor("phpexcel.PHPExcel");  
      $where='';
      $where1='';
      $where2='';
      $where3='';
      if (input('keyword')||input('status')||input('payment')||input('start')||input('end')) {
        // dump(input(''));exit;
        $start = input('start');
        $end = input('end');
        $payment = input('payment');
        $status = input('status');
        $keyword = input('keyword');

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

        if($payment!='')
        {
            // $where["a.PayStatus"]=array("=",$payment);
            $where3=array('a.PayStatus'=>array("eq",$payment));
        }
        if($status!='')
        {
            // $where["a.Status"]=array("=",$status);
            $where1=array('a.Status'=>array("eq",$status));
        }
        if($keyword!='')
        {
            $where2="(a.OrderCode like '%". $keyword."%' or a.Receiver like '%". $keyword."%' or s.Name like '%". $keyword."%')";
        }

      }
      
      
        $res=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where("Type=1")
          ->where($where)
          ->where($where1)
          ->where($where2)
          ->where($where3)
          ->order('ID desc')
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
              $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
          }
           
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称   
        $i=2;  
        foreach($res as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue("A1","序号")->setCellValue("B1","店铺名称")->setCellValue("C1","订单号")->setCellValue("D1","订单金额")->setCellValue("E1","收货人")->setCellValue("F1","联系电话")->setCellValue("G1","收货地址")->setCellValue("H1","是否付款")->setCellValue("I1","订单状态")->setCellValue("J1","下单时间");//表格数据 

               $PHPSheet->setCellValue('A'.$i,$val['ROW_NUMBER'])->setCellValue('B'.$i,$val['Name'])->setCellValue('C'.$i,$val['OrderCode'])->setCellValue('D'.$i,$val['Total'])->setCellValue('E'.$i,$val['Receiver'])->setCellValue('F'.$i,$val['Mobile'])->setCellValue('G'.$i,$val['Address'])->setCellValue('H'.$i,$val['PayStatus'])->setCellValue('I'.$i,$val['Status'])->setCellValue('J'.$i,$val['TimeFd1']);//表格数据   
               $i++;  
           }  
        // $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        // header('Content-Disposition: attachment;filename="普通订单表.xlsx"');//下载下来的表格名  
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        // $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件 

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"餐厅订单表.xls\"");
        header('Cache-Control: max-age=0'); 
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5"); 
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    public function return_list($page=1)
    { 
      if (input('')) {
        // dump(input(''));
        $keyword=input('keyword');
        // dump($keyword);
        $res=Db::table('ceb_ProReturns')
          ->page($page,20)
          ->alias('p')
          ->field('p.ID,p.OrderCode,p.Name,p.AppReason,p.Price,p.Count,p.Total,p.ReturnTotal,p.Status,p.IsBackMoney,m.EnrolName')
          ->join('ceb_Member m','p.MemberID = m.ID','left')
          ->where("p.Name like '%". $keyword."%' or p.OrderCode='$keyword' or m.EnrolName='$keyword'")
          ->select();
      }else{
        $res=Db::table('ceb_ProReturns')
          ->alias('p')
          ->field('p.ID,p.OrderCode,p.Name,p.AppReason,p.Price,p.Count,p.Total,p.ReturnTotal,p.Status,p.IsBackMoney,m.EnrolName')
          ->join('ceb_Member m','p.MemberID = m.ID','left')
          ->select();
      }
       
      $this->assign('res2',$res);  
      return $this->fetch('');
    }

    public function sec_method($wh)
    {
       $res=Db::table('ceb_Order')
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where($wh)
          // ->limit(10)
          ->order('ID desc')
          ->select();
        // dump($res); 
      
    }

    //普通订单时间查询
    public function pt_screen($page=1)
    {
      
      $start_time=input('start');
      $end_time=input('end');
      // dump(input('start'));
      // dump(input('end'));
      if ($start_time!='' && $end_time=='') {
        $wh=array('Type' => 0,'a.TimeFd1'=>array('gt',$start_time));
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->order('ID desc')
            ->select();
      }elseif ($start_time!='' && $end_time!='') {
        $wh['Type']=0;
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->whereTime('a.TimeFd1', 'between', ["$start_time", "$end_time"])
            ->order('ID desc')
            ->select();
      }elseif ($start_time=='' && $end_time!='') {
        $wh=array('Type' => 0,'a.TimeFd1'=>array('lt',$end_time));
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->order('ID desc')
            ->select();
      }else{
        $wh['Type']=0;
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->order('ID desc')
            ->select();
      }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('order_list');
      
    }

    //点餐时间查询
    public function dc_screen($page=1)
    {
      
      $start_time=input('start');
      $end_time=input('end');
      // dump(input('start'));
      // dump(input('end'));
      if ($start_time!='' && $end_time=='') {
        $wh=array('Type' => 1,'a.TimeFd1'=>array('gt',$start_time));
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->order('ID desc')
            ->select();
      }elseif ($start_time!='' && $end_time!='') {
        $wh['Type']=1;
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->whereTime('a.TimeFd1', 'between', ["$start_time", "$end_time"])
            ->order('ID desc')
            ->select();
      }elseif ($start_time=='' && $end_time!='') {
        $wh=array('Type' => 1,'a.TimeFd1'=>array('lt',$end_time));
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->order('ID desc')
            ->select();
      }else{
        $wh['Type']=1;
        $res=Db::table('ceb_Order')
            ->page($page,10)
            ->alias('a')
            ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
            ->join('ceb_ShopMain s','a.ShopID = s.ID')
            ->where($wh)
            ->order('ID desc')
            ->select();
      }
      // dump($res);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('order_list');
      
    }

    //退货换货订单详情
    public function return_info()
    {
        
      $id= input('id');
      dump($id);
      $wh['a.ID']=$id;
      $res=Db::table('ceb_ProReturns')
          ->alias('p')
          ->field('p.ID,p.OrderCode,p.Name,p.Img,p.AppReason,p.Price,p.Count,p.Total,p.ReturnTotal,p.Status,p.IsBackMoney,m.EnrolName')
          ->join('ceb_Member m','p.MemberID = m.ID','left')
          ->find();
          
            if ($res['IsBackMoney']==1) {
              $res['IsBackMoney']="是";
            }else{
              $res['IsBackMoney']="否";
            }
          // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');
    }

    //普通订单详情
    public function order_info(){
        
        $id= input('id');
        // dump($id);
         $order=input('order_id');
        // dump($order);
        $res=Db::table('ceb_OrderItem')
          ->alias('a')
          ->field('a.ID,a.OrderID,a.DemoImg,a.Name,a.Price,a.Count,a.Total as unTotal,b.EnrolName,b.OrderCode,b.Receiver,b.Address,b.Mobile,b.Tel,b.Total,b.Status,b.PayStatus,b.PayWay,b.NvrFd1,b.NvrFd2')
          // ->field('a.*')
          ->join('ceb_Order b','a.OrderID = b.ID','left')
          ->where("a.OrderCode='$order'")
          ->find();
          // dump($res);exit;
        $tem_id=$res['OrderID'];  
        $data_info=Db::table('ceb_OrderItem')
             ->where("OrderID='$tem_id'")
             ->select();
        // dump($data_info);     
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
              
              // dump($sin_f_time);
              // dump($data);
          // dump($res);exit;
      $this->assign('res2',$res);
      $this->assign('data_info',$data_info);
      return $this->fetch('order_info');
    }


    //点餐订单详情
    public function dclist_info(){
        
        $id= input('id');
        // dump($id);
        $wh['a.OrderID']=$id;
        $where['ID']=$id;
        $data=Db::table('ceb_OrderItem')
          ->alias('a')
          ->field('a.ID,a.DemoImg,a.Name,a.Price,a.Count,a.Total as unTotal,b.EnrolName,b.OrderCode,b.Receiver,b.Address,b.Mobile,b.Tel,b.Total,b.Status,b.PayStatus,b.PayWay,b.NvrFd2')
          ->join('ceb_Order b','a.OrderID = b.ID')
          ->where($wh)
          ->select();
        
        $res=Db::table('ceb_Order')         
          ->where($where)
          ->find();  
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
         
          if (substr($res['Total'],0,1)==".") {
              $res['Total']="0".$res['Total'];
            }
          // dump($res);exit;
      $this->assign('data',$data);
      $this->assign('res2',$res);
      return $this->fetch('dclist_info');
    }

    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

  

}
