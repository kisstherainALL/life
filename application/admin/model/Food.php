<?php
//声明命名空间 
namespace app\admin\model;
use think\Db;//调用框架的Db类 
//导入框架的数据模型
use think\Model;

//声明Restaurant（餐厅）控制器的模型
class Food extends Model
{
    // //如果模型名字和表明不一致，设置保护类
    // protected $table="ceb_Member";
    public function index($page=1)
    {
        $where='';
        $where1='';
        $where2='';
        if (input('keyword')||input('shop')||input('pro_satus')) {
            $keyword = input('keyword');
            $shop = input('shop');
            $pro_satus = input('pro_satus');
            if($keyword!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where="(a.Title like '%". $keyword."%')";
            }

            if($shop!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where1="(s.Name like '%". $shop."%')";
            }

            if($pro_satus!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where2=array('a.Status'=>"$pro_satus");
            }
        
        }
        $res=DB::table('ceb_Product')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0' and a.NvrFd12='1'")
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->order('ID desc')
            ->select();   
      foreach ($res as $k => $v) {
        $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
      }
           
        return $res;
    }

    public function sum()
    {
        $where='';
        $where1='';
        $where2='';
        if (input('keyword')||input('shop')||input('pro_satus')) {
            $keyword = input('keyword');
            $shop = input('shop');
            $pro_satus = input('pro_satus');
            if($keyword!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where="(a.Title like '%". $keyword."%')";
            }

            if($shop!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where1="(s.Name like '%". $shop."%')";
            }

            if($pro_satus!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where2=array('a.Status'=>"$pro_satus");
            }
        
        }
        $sum=DB::table('ceb_Product')
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0' and a.NvrFd12='1'")
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->count();
        return $sum;

    }
    public function sales()
    {
        $where='';
        $where1='';
        $where2='';
        if (input('keyword')||input('shop')||input('pro_satus')) {
            $keyword = input('keyword');
            $shop = input('shop');
            $pro_satus = input('pro_satus');
            if($keyword!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where="(a.Title like '%". $keyword."%')";
            }

            if($shop!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where1="(s.Name like '%". $shop."%')";
            }

            if($pro_satus!='')
            {
            // $where["a.TimeFd1"]=array("egt",$start);
            $where2=array('a.Status'=>"$pro_satus");
            }
        
        }
        $sales=DB::table('ceb_Product')
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0' and a.NvrFd12='1'")
            ->where($where)
            ->where($where1)
            ->where($where2)
            ->sum('a.SaleNum');
            // dump($sales);
        return $sales;

    }

    public function examinesum()
    {
        
    }


}







?>