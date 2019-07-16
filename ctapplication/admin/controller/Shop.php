<?php
namespace app\admin\controller;
use think\Db;
class Shop extends \think\Controller
{
    public function index()
    {
    
  

		  return $this->fetch('index');
    }

    public function welcome()
    {
      return $this->fetch('welcome');
    }

    public function dc_list($page=1){
      // dump(123);
      $res=DB::table('ceb_ShopMain')
          ->page($page,10)
          ->alias('s')
          ->field('s.ID,s.Name,m.EnrolName,s.HyCodeName,s.AreaName,s.RenJun,s.Hits,s.PingFen,s.DecFd2,s.IsOpen,s.IsRecommend,s.CreateTime')
          ->join('ceb_Member m','s.MemberID=m.ID')
          ->where("ShopType=1")
          ->order('ID desc')
          ->select();
          //服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['DecFd2'];
            // $tem=abs($tem);
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
              

            }
              // $one=bcmul($tem, 1, 2);
              $one=abs($tem); 
              $sin=sprintf("%1\$.2f",$one);
              // dump($sin);exit;
              $res[$k]['DecFd2']=$sin;
          }
          foreach ($res as $k => $v) {
            $tem=$res[$k]['PingFen'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['PingFen']=$sin;
          }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }

    //供应商店铺
    public function supplier_list($page=1){
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
        
        if($start!='')
        {
            $where["s.CreateTime"]=array("egt",$start);
        }

        if($end!='')
        {
            $where["s.CreateTime"]=array("elt",$end);           
        }

        if($start!='' && $end!='')
        {     
            $where=array('s.CreateTime'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%')";
        }

      }
        // dump($where);exit;
       $res=DB::table('ceb_ShopMain')
          ->page($page,10)
          ->alias('s')
          ->field('s.ID,s.Name,m.EnrolName,s.HyCodeName,s.AreaName,s.RenJun,s.Hits,s.PingFen,s.DecFd2,s.IsOpen,s.IsRecommend,s.CreateTime,s.PaiXu')
          ->join('ceb_Member m','s.MemberID=m.ID','left')
          // ->join('ceb_Product p','p.ShopID=s.ID','left')
          ->where("ShopType=2")
          ->where($where)
          ->where($where2)
          ->order('ID desc')
          ->select();
          foreach ($res as $k => $v) {
            $wh['ShopID']=$res[$k]['ID'];
            $one=DB::table('ceb_Product')->field('ID')->where($wh)->count();
            $res[$k]['num']=$one;
            // dump($one);
          }
          //服务费
          foreach ($res as $k => $v) {
            $tem=$res[$k]['DecFd2'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            }
              $sin=sprintf("%1\$.2f",$tem);
              $res[$k]['DecFd2']=$sin;
          }
      
          // dump($res);exit;
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');


    }


    public function info(){
      // dump(input(''));
      $wh['s.ID']=input('id');
       $res=DB::table('ceb_ShopMain')
          ->alias('s')
          ->field('s.*,m.EnrolName')
          ->join('ceb_Member m','s.MemberID=m.ID')
          // ->join('ceb_Product p','p.ShopID=s.ID','left')
          ->where($wh)
          // ->order('ID desc')
          ->find();

         $tem=explode(',',$res['TextFd2']);
         // dump($tem);exit;
         // dump($res);exit;
          
          
          if(!isset($tem['1'])){
              $fist="01";
          }else{
            $fist=$tem['1']; 
          }
          if(!isset($tem['2'])){
              $two="02";
          }else{
            $two=$tem['2'];
          }
          if(!isset($tem['3'])){
            $tree="03";
          }else{
            $tree=$tem['3'];
          }
          if(!isset($tem['4'])){
            $four="04";
          }else{
            $four=$tem['4'];
          }
          
          $this->assign('fist',$fist);
          $this->assign('two',$two);
          $this->assign('tree',$tree);
          $this->assign('four',$four);
         // dump($four);
          //服务费
       
            $tem=$res['DecFd2'];
            if (substr($tem,0,1)==".") {
              $tem="0".$tem;
            
              $sin=sprintf("%1\$.2f",$tem);
              $res['DecFd2']=$sin;
            }       
          
     // dump($res);exit;
     $this->assign('res2',$res);

     $this->assign('tem',$tem);
     return $this->fetch('');
    }

    public function edit(){
      
    
        // dump(input(''));exit;
        $data=input('');
        $id=input('id');
        $name=input('name');
        $fuwu=input('fuwu');
        $sort=input('sort');
        $hits=input('hits');
        $openshop=input('openshop');
        $tuijian=input('tuijian');
        $newshop=input('newshop');
        // $biaoqian=$data['bq'];
        // $bq=input('bq');
        // $kong=input('kong');
        // $qitian=input('qitian');
        $content=input('content');
        $qian = implode($data['bq'], ',');
           
        // dump($qian);
      
          //  dump($id);
          //  dump($name);
          //  dump($fuwu);
          //  dump($fuwu);
          //  dump($sort);
          //  dump($hits);
          //  dump($openshop);
          //  dump($tuijian);
          //  dump($content);
          // exit;
          $wh['ID']=$id;
          $riz=Db::table('ceb_ShopMain')
              ->where($wh)
              ->find();
          $res=Db::table('ceb_ShopMain')
                  ->where($wh)
                  ->update(["Name" => "$name","DecFd2" => "$fuwu","PaiXu" => "$sort","Hits" => "$hits","IsOpen" => "$openshop","IsRecommend" => "$tuijian","Detail" => "$content","TagCodeList" => "$newshop","TextFd2" => "$qian"]);
                  if ($res) {
                    welog($text='修改店铺'.' —— '.$riz['Name'].' —— ID:'.$riz['ID']);
                    $this->success('修改成功',url('admin/Shop/supplier_list'));
                  }else{
                    $this->error('修改失败');
                  }

    }

    //批量删除
    public function delete_all()
    {
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        
        $wh['ID'] = array('in',$one);
        // dump($wh);exit;
        
        $riz=Db::table("ceb_ShopMain")
            ->where($wh)
            ->select();

        foreach ($riz as $k => $v) {
              $tem[]=$riz[$k]['Name'];
              $tem_name=implode($tem, ',');
            }

       $res=Db::table("ceb_ShopMain")
            ->where($wh)
            ->delete();
        if($res){
            welog($text='批量删除删除供应商店铺 —— ID：'.$one.'-'.$tem_name);
            $this->success('批量删除“供应商店铺”成功',url('admin/Shop/supplier_list'));
        }else{
            $this->error('批量删除“供应商店铺”失败');
        }
      }
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
