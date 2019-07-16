<?php
namespace app\restadmin\controller;
use think\Db;
class Refund extends Restadmin
{
    public function index($page=1)
    {
        if(!isset($_SESSION)){session_start();}
        dump($_SESSION['think']['memberid']); 
        $userid=$_SESSION['think']['memberid'];
        $res=Db::table('ceb_ShopMain')
            ->where("MemberID","$userid")
            ->find();
        // dump($res); 
        dump($res['ID']);
        $shopid=$res['ID'];
        $all=Db::table('ceb_WeReturn')
            ->where("ShopID","$shopid")
            ->select();
        // dump($all); 
        $NewArray=array();    
        foreach ($all as $k) {
           if (!isset($NewArray[$k['Reorder']])) {
               $NewArray[$k['Reorder']]=$k;
           }
           if(!isset($NewArray[$k['Reorder']]['son'])){$NewArray[$k['Reorder']]['son']=array();}
            array_push($NewArray[$k['Reorder']]['son'],$k);
        }
        // dump($NewArray);
        // $abc =  'R'.date('Ymd') . str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);随机数
        // dump($abc);
        $this->assign('NewArray',$NewArray);   
        return $this->fetch(''); 
    }

    //退款详情
    public function RefundInfo()
    {
        // dump(input(''));exit;
        $order=input('oid');
        $all=Db::table('ceb_WeReturn')
            ->where("Oid","$order")
            ->select();
        // dump($all);
        $NewArray=array();    
        foreach ($all as $k) {
           if (!isset($NewArray[$k['OrderCode']])) {
               $NewArray[$k['OrderCode']]=$k;
           }
           if(!isset($NewArray[$k['OrderCode']]['son'])){$NewArray[$k['OrderCode']]['son']=array();}
            array_push($NewArray[$k['OrderCode']]['son'],$k);
        }
        $res=Db::table('ceb_Order')
            ->alias('a')
            ->field('a.ID,a.Receiver,a.Mobile,a.Address,a.NvrFd1,a.NvrFd2,s.Name,s.Phone,s.YunFei')
            ->join('ceb_ShopMain s','s.ID = a.ShopID','left')
            ->where("a.ID","$order")
            ->find();
        // dump($res);
        $relog= Db::table('ceb_WeReturnLog')
              ->where("OrderID","$order")
              ->order('C_Time desc')
              ->select();
        foreach ($relog as $k => $v) {
            $relog[$k]['C_Time'] = substr($relog[$k]['C_Time'],0,19);       
        }      
        // dump($NewArray);      
        $this->assign('NewArray',$NewArray);
        $this->assign('relog',$relog);
        $this->assign('res',$res);    
        return $this->fetch('');
    }
    //物流
    public function express()
    {
        // dump(input(''));exit;
        $excom=input('gongsi');
        $exnumber=input('danhao');
        //查询字典表获取快递公司名字
        if (!empty($excom)) {
            $kuaidi = Db::table('ceb_Dictionary')
                ->where("Remark","$excom")
                ->find();
            if ($kuaidi) {
               $kuaidi['num']=$exnumber;
            }
            $this->assign('kuaidi',$kuaidi); 
        }
                    
        // dump($kuaidi);exit;        
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
          $com=$excom;
          $num=$exnumber;
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
            
            return $this->fetch('');
    }

    //同意退款
    public function agree()
    {
       // dump(input(''));exit;
       $order=input('oid');
        $all=Db::table('ceb_WeReturn')
            ->where("Oid","$order")
            ->select();
        // dump($all);
        // 启动事务
        Db::startTrans();
        try{
        //修改状态    
        $res = Db::table('ceb_WeReturn')->where('Oid',"$order")->update(['Status' => "寄回中"]);
        $res_order= Db::table('ceb_Order')->where('ID',"$order")->update(['Status' => "退款中"]);
        
        // 提交事务
        Db::commit(); 
        } catch (\Exception $e) {
        // 回滚事务
        Db::rollback();
        }
        if ($res!=0 && $res_order!=0) {
            //添加日志
            $c_time=date("Y-m-d H:i:s",time());// 记录操作时间
            $data = ['OrderID' => "$order", 'Text' => "商家同意退款", 'C_Time' => "$c_time"];
            $log=Db::table('ceb_WeReturnLog')->insert($data);
            $this->redirect('restadmin/Refund/index');
        }
        
       // $this->redirect('restadmin/Refund/index');
    }

    //拒绝退款
    public function refuse()
    {
       // dump(input(''));exit;
       $order=input('oid');
        $all=Db::table('ceb_WeReturn')
            ->where("Oid","$order")
            ->select();
        // dump($all);
        // 启动事务
        Db::startTrans();
        try{
        //修改状态    
        $res = Db::table('ceb_WeReturn')->where('Oid',"$order")->update(['Status' => "拒绝退款"]);
        // $res_order= Db::table('ceb_Order')->where('ID',"$order")->update(['Status' => "退款中"]);
        
        // 提交事务
        Db::commit(); 
        } catch (\Exception $e) {
        // 回滚事务
        Db::rollback();
        }
        if ($res!=0 && $res_order!=0) {
            //添加日志
            $c_time=date("Y-m-d H:i:s",time());// 记录操作时间
            $data = ['OrderID' => "$order", 'Text' => "商家拒绝退款", 'C_Time' => "$c_time"];
            $log=Db::table('ceb_WeReturnLog')->insert($data);
            $this->redirect('restadmin/Refund/index');
        }
        
       // $this->redirect('restadmin/Refund/index');
    }
 
    //确认商品寄回以及退款完成订单
    public function sendback()
    {
        dump(input(''));
        $code= input('code');
        $res_log=Db::table('ceb_Order')
                ->field('ID')
                ->where('OrderCode',"$code")
                ->find();
        $tem = explode("_",$code)[0];
        dump($tem);
        $res= Db::table('ceb_Order')
            ->alias('o')
            ->field('o.ID,o.EnrolName,o.IntFd4,o.From_TuiKuan,o.From_YongJin,o.From_FanXian,o.From_XianJin,o.From_XianJin_Type,m.ID as mid')
            ->join('ceb_Member m','m.EnrolName = o.EnrolName','left')
            ->where('o.OrderCode',"$tem")
            ->select();
        // dump($res);exit;
        //数据处理
        foreach ($res as $k => $v) {
            $tuikan=$res[$k]['From_TuiKuan'];
            $yongjin=$res[$k]['From_YongJin'];
            $fanxian=$res[$k]['From_FanXian'];
            $xianjin=$res[$k]['From_XianJin'];
            if (substr($tuikan,0,1)==".") {
                $tuikan="0".$tuikan;
                
            }else if(substr($yongjin,0,1)=="."){
                $yongjin="0".$yongjin;
                
            }else if(substr($fanxian,0,1)=="."){
                $fanxian="0".$fanxian;
                
            }else if(substr($xianjin,0,1)=="."){
                $xianjin="0".$xianjin;
                
            }
            $tuikan=sprintf("%1\$.2f",$tuikan);
            $res[$k]['From_TuiKuan']=$tuikan;
            $yongjin=sprintf("%1\$.2f",$yongjin);
            $res[$k]['From_YongJin']=$yongjin;
            $fanxian=sprintf("%1\$.2f",$fanxian);
            $res[$k]['From_FanXian']=$fanxian;
            $xianjin=sprintf("%1\$.2f",$xianjin);
            $res[$k]['From_XianJin']=$xianjin;

        }
        $tem_meber=$res[0]['mid'];
        $data = Db::table('ceb_Member')
              ->field('ID,CurMoney1,CurMoney2,CurMoney3')
              ->where("ID","$tem_meber") 
              ->select(); 
        dump($res);
        dump($res[0]['EnrolName']);
        dump($data);
        $orderid=$res_log['ID'];
        $c_time=date("Y-m-d H:i:s",time());// 记录操作时间
        if ($res[$k]['From_TuiKuan']!=0.00) {
            // dump('退款余额');
            $sum=($res[0]['From_TuiKuan']-0)+($data[0]['CurMoney3']-0);
            $sin=sprintf("%1\$.2f",$sum);
            $sum=$sin;
            // 启动事务
            Db::startTrans();
            try{
               
                $member_up=Db::table('ceb_Member')->where('ID',"$tem_meber")->update(['CurMoney3' => "$sum"]);
                $order_up=Db::table('ceb_Order')->where('OrderCode',"$code")->update(['Status' => "已完成"]);
                $return_up=Db::table('ceb_WeReturn')->where('OrderCode',"$code")->update(['Status' => "已完成"]);

                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($member_up!=0 && $order_up!=0 && $return_up!=0) {
              dump("成功");
              //写日志
              $dataLog = ['OrderID' => "$orderid", 'Text' => "商家确认收货", 'C_Time' => "$c_time"];
              $log=Db::table('ceb_WeReturnLog')->insert($dataLog);
              $this->redirect('restadmin/Refund/index');
            }else{
              dump("失败");
            }

        }
        if ($res[$k]['From_YongJin']!=0.00) {
            // dump('退款佣金');
            $sum=($res[0]['From_YongJin']-0)+($data[0]['CurMoney2']-0);
            $sin=sprintf("%1\$.2f",$sum);
            $sum=$sin;
            // 启动事务
            Db::startTrans();
            try{
               
                $member_up=Db::table('ceb_Member')->where('ID',"$tem_meber")->update(['CurMoney2' => "$sum"]);
                $order_up=Db::table('ceb_Order')->where('OrderCode',"$code")->update(['Status' => "已完成"]);
                $return_up=Db::table('ceb_WeReturn')->where('OrderCode',"$code")->update(['Status' => "已完成"]);

                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($member_up!=0 && $order_up!=0 && $return_up!=0) {
              dump("成功");
              //写日志
              $dataLog = ['OrderID' => "$orderid", 'Text' => "商家确认收货", 'C_Time' => "$c_time"];
              $log=Db::table('ceb_WeReturnLog')->insert($dataLog);
              $this->redirect('restadmin/Refund/index');
            }else{
              dump("失败");
            }
        }
        if ($res[$k]['From_FanXian']!=0.00) {
            // dump('退款返现');
            dump($res[0]['From_FanXian']);
            dump($data[0]['CurMoney1']);
            $sum=($res[0]['From_FanXian']-0)+($data[0]['CurMoney1']-0);
            $sin=sprintf("%1\$.2f",$sum);
            $sum=$sin;
            // 启动事务
            Db::startTrans();
            try{
               
                $member_up=Db::table('ceb_Member')->where('ID',"$tem_meber")->update(['CurMoney1' => "$sum"]);
                $order_up=Db::table('ceb_Order')->where('OrderCode',"$code")->update(['Status' => "已完成"]);
                $return_up=Db::table('ceb_WeReturn')->where('OrderCode',"$code")->update(['Status' => "已完成"]);

                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($member_up!=0 && $order_up!=0 && $return_up!=0) {
              dump("成功");
              //写日志
              $dataLog = ['OrderID' => "$orderid", 'Text' => "商家确认收货", 'C_Time' => "$c_time"];
              $log=Db::table('ceb_WeReturnLog')->insert($dataLog);
              $this->redirect('restadmin/Refund/index');
            }else{
              dump("失败");
            }

            
            // dump($sum);

        }
        if ($res[$k]['From_XianJin']!=0.00) {
            // dump('退款微信支付宝');
            // dump($res[0]['From_XianJin']);
            // dump($data[0]['CurMoney3']);
            $sum=($res[0]['From_XianJin']-0)+($data[0]['CurMoney3']-0);
            $sin=sprintf("%1\$.2f",$sum);
            $sum=$sin;
            // 启动事务
            Db::startTrans();
            try{
               
                $member_up=Db::table('ceb_Member')->where('ID',"$tem_meber")->update(['CurMoney3' => "$sum"]);
                $order_up=Db::table('ceb_Order')->where('OrderCode',"$code")->update(['Status' => "已完成"]);
                $return_up=Db::table('ceb_WeReturn')->where('OrderCode',"$code")->update(['Status' => "已完成"]);

                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($member_up!=0 && $order_up!=0 && $return_up!=0) {
              dump("成功");
              //写日志
              $dataLog = ['OrderID' => "$orderid", 'Text' => "商家确认收货", 'C_Time' => "$c_time"];
              $log=Db::table('ceb_WeReturnLog')->insert($dataLog);
              $this->redirect('restadmin/Refund/index');
            }else{
              dump("失败");
            }
        }

       

    }

    public function service()
    {
        session_start();
        dump($_SESSION);
        
    }
  

}
