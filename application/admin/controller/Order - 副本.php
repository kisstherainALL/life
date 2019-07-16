<?php
namespace app\admin\controller;
use think\Db;
class Order extends \think\Controller
{
    public function order_list($page=1)
    {
      if (input('keyword')||input('order')||input('payment')) {
        // dump(input(''));
        $order = input('order');
        $payment = input('payment');
        $keyword = input('keyword');

        if (empty($order)&&empty($payment)&&!empty($keyword)) {
          //商店名
          // $wh['Type'] = 0;
          // $wh['a.Receiver'] = array('like',array('%'.$keyword.'%'));
          // $wh['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
          
          $wh['Type'] = 0;
          $wh['s.Name'] = array('like',array('%'.$keyword.'%'));
          // $wh['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
              // dump($res);
          if (!$res) {
            $wh_one['Type'] = 0;
            $wh_one['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
            $res=Db::table('ceb_Order')
                ->page($page,10)
                ->alias('a')
                ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                ->join('ceb_ShopMain s','a.ShopID = s.ID')
                ->where($wh_one)
                ->order('ID desc')
                ->select();
                // dump($res);
                if (!$res) {
                  $where['Type'] = 0;
                  $where['a.Receiver'] = array('like',array('%'.$keyword.'%'));
                  $res=Db::table('ceb_Order')
                      ->page($page,10)
                      ->alias('a')
                      ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                      ->join('ceb_ShopMain s','a.ShopID = s.ID')
                      ->where($where)
                      ->order('ID desc')
                      ->select();
                      // dump($res);
                }
          }

        }else if (!empty($order)&&empty($payment)&&!empty($keyword)) {
          //订单状态和商店名
          $wh['Type'] = 0;
          $wh['a.Status'] = $order;
          // $wh['a.PayStatus'] = $payment;
          $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
          if (!$res) {
            $wh_one['Type'] = 0;
            $wh_one['a.Status'] = $order;
            $wh_one['s.Name'] = array('like',array('%'.$keyword.'%'));
            $res=Db::table('ceb_Order')
                ->page($page,10)
                ->alias('a')
                ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                ->join('ceb_ShopMain s','a.ShopID = s.ID')
                ->where($wh_one)
                ->order('ID desc')
                ->select();
            if (!$res) {
              $where['Type'] = 0;
              $where['a.Status'] = $order;
              // $wh['a.PayStatus'] = $payment;
              $where['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
              $res=Db::table('ceb_Order')
                  ->page($page,10)
                  ->alias('a')
                  ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                  ->join('ceb_ShopMain s','a.ShopID = s.ID')
                  ->where($where)
                  ->order('ID desc')
                  ->select();
            }

          }
        }else if (empty($order)&&!empty($payment)&&empty($keyword)) {
          //支付状态
          $wh['Type'] = 0;
          // $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          // $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
        }else if (!empty($order)&&!empty($payment)&&empty($keyword)) {
          //订单状态+支付状态
          $wh['Type'] = 0;
          $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          // $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
        }else if (empty($order)&&!empty($payment)&&!empty($keyword)) {
          //支付状态+商店名
          $wh['Type'] = 0;
          // $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          $wh['s.Name'] = array('like',array('%'.$keyword.'%'));
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
              if (!$res) {
                $wh_one['Type'] = 0;
                $wh_one['a.PayStatus'] = $payment;
                $wh_one['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
                $res=Db::table('ceb_Order')
                    ->page($page,10)
                    ->alias('a')
                    ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                    ->join('ceb_ShopMain s','a.ShopID = s.ID')
                    ->where($wh_one)
                    ->order('ID desc')
                    ->select();
                    // dump($res);
                    if (!$res) {
                      $where['Type'] = 0;
                      $where['a.PayStatus'] = $payment;
                      $where['a.Receiver'] = array('like',array('%'.$keyword.'%'));
                      $res=Db::table('ceb_Order')
                          ->page($page,10)
                          ->alias('a')
                          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                          ->join('ceb_ShopMain s','a.ShopID = s.ID')
                          ->where($where)
                          ->order('ID desc')
                          ->select();
                          // dump($res);
                    }
              }
        }else if (!empty($order)&&empty($payment)&&empty($keyword)) {
          //订单状态
          $wh['Type'] = 0;
          $wh['a.Status'] = $order;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
          // dump($res);   
          
        }else if (!empty($order)&&!empty($payment)&&!empty($keyword)) {
          $wh['Type'] = 0;
          $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
          if (!$res) {
            $wh_one['Type'] = 0;
            $wh_one['a.Status'] = $order;
            $wh_one['a.PayStatus'] = $payment;
            $wh_one['a.OrderCode'] = $keyword;
            $res=Db::table('ceb_Order')
                ->alias('a')
                ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                ->join('ceb_ShopMain s','a.ShopID = s.ID')
                ->where($wh_one)
                ->order('ID desc')
                ->select();
                if (!$res) {
                  $where['Type'] = 0;
                  $where['a.Status'] = $order;
                  $where['a.PayStatus'] = $payment;
                  $where['s.Name'] = $keyword;
                  $res=Db::table('ceb_Order')
                      ->alias('a')
                      ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                      ->join('ceb_ShopMain s','a.ShopID = s.ID')
                      ->where($where)
                      ->order('ID desc')
                      ->select();
                }
          }
        }
        $this->assign('res2',$res); 
        $this->assign('page',$page);
        return $this->fetch('');
      }

      
      $res=Db::table('ceb_Order')
          ->page($page,10)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('Type',0)
          // ->limit(10)
          ->order('ID desc')
          ->select();
      $this->assign('res2',$res);
      // dump('12313'); 
      $this->assign('page',$page);
      return $this->fetch('');
    }

    public function order_dclist($page=1)
    {
      if (input('keyword')||input('order')||input('payment')) {
        // dump(input(''));
        $order = input('order');
        $payment = input('payment');
        $keyword = input('keyword');

        if (empty($order)&&empty($payment)&&!empty($keyword)) {
          //商店名
          // $wh['Type'] = 0;
          // $wh['a.Receiver'] = array('like',array('%'.$keyword.'%'));
          // $wh['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
          
          $wh['Type'] = 1;
          $wh['s.Name'] = array('like',array('%'.$keyword.'%'));
          // $wh['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
              // dump($res);
          if (!$res) {
            $wh_one['Type'] = 1;
            $wh_one['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
            $res=Db::table('ceb_Order')
                ->page($page,10)
                ->alias('a')
                ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                ->join('ceb_ShopMain s','a.ShopID = s.ID')
                ->where($wh_one)
                ->order('ID desc')
                ->select();
                // dump($res);
                if (!$res) {
                  $where['Type'] = 1;
                  $where['a.Receiver'] = array('like',array('%'.$keyword.'%'));
                  $res=Db::table('ceb_Order')
                      ->page($page,10)
                      ->alias('a')
                      ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                      ->join('ceb_ShopMain s','a.ShopID = s.ID')
                      ->where($where)
                      ->order('ID desc')
                      ->select();
                      // dump($res);
                }
          }

        }else if (!empty($order)&&empty($payment)&&!empty($keyword)) {
          //订单状态和商店名
          $wh['Type'] = 1;
          $wh['a.Status'] = $order;
          // $wh['a.PayStatus'] = $payment;
          $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
          if (!$res) {
            $wh_one['Type'] = 1;
            $wh_one['a.Status'] = $order;
            $wh_one['s.Name'] = array('like',array('%'.$keyword.'%'));
            $res=Db::table('ceb_Order')
                ->page($page,10)
                ->alias('a')
                ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                ->join('ceb_ShopMain s','a.ShopID = s.ID')
                ->where($wh_one)
                ->order('ID desc')
                ->select();
            if (!$res) {
              $where['Type'] = 1;
              $where['a.Status'] = $order;
              // $wh['a.PayStatus'] = $payment;
              $where['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
              $res=Db::table('ceb_Order')
                  ->page($page,10)
                  ->alias('a')
                  ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                  ->join('ceb_ShopMain s','a.ShopID = s.ID')
                  ->where($where)
                  ->order('ID desc')
                  ->select();
            }

          }
        }else if (empty($order)&&!empty($payment)&&empty($keyword)) {
          //支付状态
          $wh['Type'] = 1;
          // $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          // $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
        }else if (!empty($order)&&!empty($payment)&&empty($keyword)) {
          //订单状态+支付状态
          $wh['Type'] = 1;
          $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          // $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
        }else if (empty($order)&&!empty($payment)&&!empty($keyword)) {
          //支付状态+商店名
          $wh['Type'] = 1;
          // $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          $wh['s.Name'] = array('like',array('%'.$keyword.'%'));
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
              if (!$res) {
                $wh_one['Type'] = 1;
                $wh_one['a.PayStatus'] = $payment;
                $wh_one['a.OrderCode'] = array('like',array('%'.$keyword.'%'));
                $res=Db::table('ceb_Order')
                    ->page($page,10)
                    ->alias('a')
                    ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                    ->join('ceb_ShopMain s','a.ShopID = s.ID')
                    ->where($wh_one)
                    ->order('ID desc')
                    ->select();
                    // dump($res);
                    if (!$res) {
                      $where['Type'] = 1;
                      $where['a.PayStatus'] = $payment;
                      $where['a.Receiver'] = array('like',array('%'.$keyword.'%'));
                      $res=Db::table('ceb_Order')
                          ->page($page,10)
                          ->alias('a')
                          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                          ->join('ceb_ShopMain s','a.ShopID = s.ID')
                          ->where($where)
                          ->order('ID desc')
                          ->select();
                          // dump($res);
                    }
              }
        }else if (!empty($order)&&empty($payment)&&empty($keyword)) {
          //订单状态
          $wh['Type'] = 1;
          $wh['a.Status'] = $order;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
          // dump($res);   
          
        }else if (!empty($order)&&!empty($payment)&&!empty($keyword)) {
          $wh['Type'] = 1;
          $wh['a.Status'] = $order;
          $wh['a.PayStatus'] = $payment;
          $wh['a.Receiver'] = $keyword;
          $res=Db::table('ceb_Order')
              ->page($page,10)
              ->alias('a')
              ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
              ->join('ceb_ShopMain s','a.ShopID = s.ID')
              ->where($wh)
              ->order('ID desc')
              ->select();
          if (!$res) {
            $wh_one['Type'] = 1;
            $wh_one['a.Status'] = $order;
            $wh_one['a.PayStatus'] = $payment;
            $wh_one['a.OrderCode'] = $keyword;
            $res=Db::table('ceb_Order')
                ->page($page,10)
                ->alias('a')
                ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                ->join('ceb_ShopMain s','a.ShopID = s.ID')
                ->where($wh_one)
                ->order('ID desc')
                ->select();
                if (!$res) {
                  $where['Type'] = 1;
                  $where['a.Status'] = $order;
                  $where['a.PayStatus'] = $payment;
                  $where['s.Name'] = $keyword;
                  $res=Db::table('ceb_Order')
                      ->page($page,10)
                      ->alias('a')
                      ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
                      ->join('ceb_ShopMain s','a.ShopID = s.ID')
                      ->where($where)
                      ->order('ID desc')
                      ->select();
                }
          }
        }
        foreach ($res as $k => $v) {
          $tem=$res[$k]['Total'];
          // $tem=abs($tem);
          if (substr($tem,0,1)==".") {
            $tem="0".$tem;
            

          }
            // $one=bcmul($tem, 1, 2);
            $one=abs($tem); 
            $sin=sprintf("%1\$.2f",$one);
            // dump($sin);exit;
            $res[$k]['Total']=$sin;
        } 
        $this->assign('res2',$res);
        $this->assign('page',$page); 
        return $this->fetch('');
      }

      $res=Db::table('ceb_Order')
          ->page($page,10)
          ->alias('a')
          ->field('a.OrderCode,a.ID,a.Total,a.Receiver,a.Mobile,a.Address,a.PayStatus,a.Status,a.TimeFd1,s.Name')
          ->join('ceb_ShopMain s','a.ShopID = s.ID')
          ->where('Type',1)
          // ->limit(10)
          ->order('ID desc')
          ->select();
      foreach ($res as $k => $v) {
        $tem=$res[$k]['Total'];
        // $tem=abs($tem);
        if (substr($tem,0,1)==".") {
          $tem="0".$tem;
          

        }
          // $one=bcmul($tem, 1, 2);
          $one=abs($tem); 
          $sin=sprintf("%1\$.2f",$one);
          // dump($sin);exit;
          $res[$k]['Total']=$sin;
      }    
      $this->assign('res2',$res);
      $this->assign('page',$page); 
      return $this->fetch('');
    }

    public function return_list($page=1)
    { 
      if (input('')) {
        // dump(input(''));
        $keyword=input('keyword');
        // dump($keyword);
        $res=Db::table('ceb_ProReturns')
          ->page($page,10)
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
        $wh['a.OrderID']=$id;
        $res=Db::table('ceb_OrderItem')
          ->alias('a')
          ->field('a.ID,a.DemoImg,a.Name,a.Price,a.Count,a.Total as unTotal,b.EnrolName,b.OrderCode,b.Receiver,b.Address,b.Mobile,b.Tel,b.Total,b.Status,b.PayStatus,b.PayWay,b.NvrFd2')
          ->join('ceb_Order b','a.OrderID = b.ID')
          ->where($wh)
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
          // dump($res);exit;
      $this->assign('res2',$res);
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
