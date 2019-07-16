<?php
//声明命名空间 
namespace app\admin\model;
use think\Db;//调用框架的Db类 
//导入框架的数据模型
use think\Model;

//声明Restaurant（餐厅）控制器的模型
class Restmember extends Model
{
    // //如果模型名字和表明不一致，设置保护类
    // protected $table="ceb_Member";
    public function index($page=1)
    {
    }

    public function sum()
    {

    }
    public function sales()
    {

    }

    public function examinesum()
    {
        
    }
    //餐厅会员结算
    public function settlement($page=1)
    {
        $where='';
        $where1='';
        $where2='';
        $where3='';
        if (input('keyword')||input('start')||input('end')||input('payment_state')||input('pay_state')) {
        dump(input(''));exit;
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
              ->where("MemberType='1'")
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
          return  $res;       

    }


}







?>