<?php
namespace app\admin\controller;
use think\Db;
class Food extends \think\Controller
{
    //菜品列表+菜品搜索
    public function food_list($page=1){
      $keyword = input('keyword');
      if (input('keyword')!='') {
        // dump(input('keyword'));
        // $hot = iconv("utf-8","gbk","$keyword");
        $hot = $keyword;
        $res=DB::table('ceb_Product')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
          ->join('ceb_ShopMain s','a.ShopID=s.ID')
          ->where("a.Type='0' and a.NvrFd12='1' and a.Title like '%". $hot."%'")
          ->order('ID desc')
          ->select();

      }else{
        $res=DB::table('ceb_Product')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0' and a.NvrFd12='1'")
            ->order('ID desc')
            ->select();  
      } 
      foreach ($res as $k => $v) {
        $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
      } 
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }

    //商店搜索列表
    public function goods_shop($page=1){
      $keyword = input('keyword');
      if (input('keyword')!='') {
        // dump(input('keyword'));
        $hot = $keyword;
        $res=DB::table('ceb_Product')
          ->page($page,20)
          ->alias('a')
          ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
          ->join('ceb_ShopMain s','a.ShopID=s.ID')
          ->where("a.Type='0' and a.NvrFd12='1' and s.Name like '%". $hot."%'")
          ->order('ID desc')
          ->select();
      }else{
        $res=DB::table('ceb_Product')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0' and a.NvrFd12='1'")
            ->order('ID desc')
            ->select();
        foreach ($res as $k => $v) {
          $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
        }
      }

      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('food_list'); 
    }

    //详情
    public function info()
    {
      // $all=DB::table('ceb_Product')->field('TagCodeList')->where('TagCodeList','neq','')->select();
      // dump($all);exit;
      // dump(input(''));
      $wh['p.ID']=input('id');
      $res=Db::table('ceb_Product')
          ->alias('p')
          ->field('p.*,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where($wh)
          ->find();
      // dump($res);exit;
      // $more_img = explode(',',$res['ImgList']);ProductClass
      // dump($more_img);
      $pid=substr($res['ProductClass'],0,6);
    
      $res['StartTime']=substr($res['StartTime'],0,19);
      $res['EndTime']=substr($res['EndTime'],0,19);
      $res['TimeFd1']=substr($res['TimeFd1'],0,19);
      // dump($pid);exit;
      // if ($pid=='') {
      //   $pid='002';
      // }
      // dump($pid);exit;
      $wh['Code']=$pid;
      $cate_pid=Db::query("select * from ceb_Dictionary where Left(Code,3) = '002'");
      foreach ($cate_pid as $k => $v) {
        if (strlen($cate_pid[$k]['Code'])!=6) {
            unset($cate_pid[$k]);
        }
      }
      $data_pid=Db::table('ceb_Dictionary')->where("Code","$pid")->find();
      
      // dump($data_pid);exit;
 
      $where['Code']='006';
      $data=Db::query("select * from ceb_Dictionary where Left(Code,3) = '006'");
      foreach ($data as $k => $v) {
        if (strlen($data[$k]['Code'])!=6) {
            unset($data[$k]);
           }
      }

     
      // dump($data);exit;
      // $data=Db::query("select * from ceb_Dictionary where Left(Code,6) = '$pid'");TagCodeList
      // foreach ($data as $k => $v) {
      //   if (strlen($data[$k]['Code'])!=9) {
      //       unset($data[$k]);
      //   }
      // }  
      // // dump($data);exit;
      // if ($pid) {
      //   $data_pid=Db::table('ceb_Dictionary')->where("Code","$pid")->find();
      // }
      // // dump($data_pid);
      // // dump($data);
      // $cate_pid=Db::query("select * from ceb_Dictionary where Left(Code,4) = '0030' and Display=1");
      // foreach ($cate_pid as $k => $v) {
      //   if (strlen($cate_pid[$k]['Code'])!=6) {
      //       unset($cate_pid[$k]);
      //   }
      // }
      // dump($res);
      $this->assign('res2',$res);
      $this->assign('data',$data);

      $this->assign('data_pid',$data_pid);
      $this->assign('cate_pid',$cate_pid);
      return $this->fetch('');
    }

    //状态修改
    public function onedollar_status_edit()
    {
      if (input('')) {
        // dump(input(''));
        $id=input('movieId');
        $wh['ID']=$id;
        $status = input('status');
        // dump($id);
        // dump($status);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['Status' => "$status"]);
        if ($res!=0) {
          echo json_encode($res);
        }
            
      }
    }

    //添加
    public function add($page=1){
      if (input('goodsname')) {
        // dump(input(''));exit;
        $wh['ID']=input('id_hd');
        $StartTime=input('starttime');
        $EndTime=input('endtime');
        $Filled=input('full');
        $Reduce=input('reduce');
        // dump($StartTime);
        // dump($EndTime);
        // dump($Quantity);
        // dump($Leastnum);
        // dump($Mostnum);exit;

        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['ActivityType' => '3','StartTime' => "$StartTime",'EndTime' => "$EndTime",'Filled' => "$Filled",'Reduce' => "$Reduce"]);
        if ($res!=0) {
         $this->success('修改成功',url('admin/Full/index'));
        }else{
           $this->error('修改失败');
        }    
      }

      if (input('keyword')) {
        $keyword = input('keyword');
        $hot = $keyword;
        $res=Db::table("ceb_Product")
          ->page($page,20)
          ->alias('p')
          ->field('p.ID,p.Title,p.price,p.SaleNum,p.ClickNum,p.Quantity,p.TimeFd1,p.Status,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where("ActivityType='0' and p.Title like '%". $hot."%' or s.Name like '%". $hot."%'")
          ->where("Type='1'")
          ->where("p.Status='上架'")
          // ->where("s.Name='Life风自营'")
          ->order('TimeFd1 desc')
          ->select();
      }else{
        $res=Db::table("ceb_Product")
          ->page($page,20)
          ->alias('p')
          ->field('p.ID,p.Title,p.price,p.SaleNum,p.ClickNum,p.Quantity,p.TimeFd1,p.Status,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where("ActivityType='0'")
          ->where("Type='1'")
          ->where("p.Status='上架'")
          // ->where("s.Name='Life风自营'")
          ->order('TimeFd1 desc')
          ->select();

      }
     
          foreach ($res as $k => $v) {
            if ($res[$k]['Quantity']=='') {
                $res[$k]['Quantity']="0";
            }
          }
      // dump($res);exit;
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }

    //修改
    public function edit(){
      // dump(input(''));exit;
      $wh['ID']=input('id');
      $title=input('title');
      $cate_pid=input('cate_pid');
      $cate_son=input('cate_son');
      $c_time=input('c_time');
      $only=input('recommend');
      $gongyingshang=input('gongyingshang');
      $price=input('price');
      $salenum=input('salenum');
      $clicknum=input('clicknum');
      $content=input('content');
      $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();
      $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['Title' => "$title",'ProductClass' => "$cate_pid",'TimeFd1' => "$c_time",'Price' => "$price",'SaleNum' => "$salenum",'ClickNum' => "$clicknum",'Detail' => "$content",'TagCodeList'=>"$only"]);
        if ($res!=0) {
          welog($text='修改菜品'.'—'.$one['Title'].'ID:'.$one['ID']);
          $this->success('修改成功',url('admin/Food/food_list'));
        }else{
          $this->error('修改失败');
        }  

    }

    //删除
    public function delete()
    {
      if (input('')) {
        // dump(input(''));
        $wh['ID']=input('movieId');
        $rizi=Db::table("ceb_Product")
            ->where($wh)
            ->find();
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->delete();
        if ($res!=0) {
          welog($text='删除菜品'.' — ID:'.$rizi['ID'].' — 菜名:'.$rizi['Title']);
         $this->success('删除成功',url('admin/Food/food_list'));
        }else{
           $this->error('删除失败');
        }  
        // $a = array("msg"=>"hello"); 
        echo json_encode($res);      
      }
      
      // dump('123123');
    }

    //批量删除
    public function delete_all()
    {
      if (input('')) {
        $id = input('');
        // dump($id);
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        $all=Db::table("ceb_Product")
            ->field('ID,Title')
            ->where($wh)
            ->select();
       
        // dump($all);exit;
        
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->delete();
        if($res){
          // dump($all);
            foreach ($all as $k => $v) {
              // dump($all[$k]['ID']);
              $tem_id=$all[$k]['ID'];
              $tem_title=$all[$k]['Title'];
              // dump($tem_id);
              // dump($tem_title);
              welog($text='批量删除删除菜品'.' —— ID: '.$tem_id.' —— 菜品: '.$tem_title);
            }
            // exit;
            $this->success('批量删除菜品成功',url('admin/Food/food_list'));
        }else{
            $this->error('批量删除菜品失败');
        }
      }
    }

    //菜品分类
    public function food_cate(){
      $cate_p=Db::query("select * from ceb_Dictionary where Left(Code,6) like '015%' order by OrderID ASC");
      foreach ($cate_p as $k => $v) {
        if (strlen($cate_p[$k]['Code'])!=6) {
            unset($cate_p[$k]);
        }
      }
      // dump($cate_p);
      $this->assign('cate_p',$cate_p);
      return $this->fetch('');
    }
    //分类详情
    public function food_info(){
      // dump(input(''));
      $wh['ID']=input('id');
      $res=Db::table("ceb_Dictionary")
            ->where($wh)
            ->find();
      // dump($res); 
      $this->assign('res2',$res);
      return $this->fetch('');     

    }
    //分类修改
    public function food_edit(){
      if (input('')) {
        // dump(input(''));
        $wh['ID']=input('cate_id');
        $name=input('cate_name');
        $sort=input('sort');
        $res=Db::table("ceb_Dictionary")
            ->where($wh)
            ->update(['Name' => "$name",'OrderID' => "$sort"]);
        if($res){
            welog($text='修改菜品分类'.'——'.$name);
            $this->success('修改菜品分类成功',url('admin/Food/food_cate'));
        }else{
            $this->error('修改菜品分类失败 ');
        }    
      }

    }
    //删除分类
    public function food_delete_all(){
      if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        // dump($wh);exit;
       $res=Db::table("ceb_Dictionary")
            ->where($wh)
            ->delete();
        if($res){
            welog($text='批量删除菜品分类');
            $this->success('批量删除菜品分类成功',url('admin/Food/food_cate'));
        }else{
            $this->error('批量删除菜品分类失败');
        }
      }

    }

    //菜品审核
    public function food_examine($page=1){
      $where='';
      $where2='';
      if (input('keyword')||input('adopt')) {
        // dump('123123');
        // dump(input(''));exit;
        $adopt = input('adopt');
        $keyword = input('keyword');

        // dump($adopt);
        // dump($keyword);
        if($adopt!='')
        {
            // $where["a.Adopt"]=array("=",$status);
            $where=array('a.NvrFd12'=>array("eq",$adopt));
        }
        if($keyword!='')
        {
            $where2="(s.Name like '%". $keyword."%' or a.Title like '%". $keyword."%')";
        }

      }
      $res=DB::table('ceb_Product')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0'")
            ->where($where)
            ->where($where2)
            ->order('ID desc')
            ->select();
            // dump($res);exit;
        foreach ($res as $k => $v) {
          $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
        } 
        $all=DB::table('ceb_Product')
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='0'")
            ->where($where)
            ->where($where2)
            ->order('ID desc')
            ->select();     
      $num=count($all);     
      $this->assign('num',$num);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }

    //审核通过
    public function food_examine_pass(){
      if (input('')) {
        // dump(input(''));
        $id=input('movieId');
        $wh['ID']=$id;
        $adopt = "1";
        $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();
        // dump($status);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['NvrFd12' => "$adopt"]);
        if ($res!=0) {
          welog($text='审核通过 —— '.$one['Title']);
          echo json_encode($res);
        }
          // log("修改了菜品信息");  
      }
    }

    //驳回
    public function food_examine_rebut(){
       if (input('')) {
        // dump(input(''));
        $id=input('movieId');
        $wh['ID']=$id;
        $adopt = "2";
        $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();
        // dump($status);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['NvrFd12' => "$adopt"]);
        if ($res!=0) {
          welog($text='驳回菜品 —— '.$one['Title']);
          echo json_encode($res);
        }
          // log("修改了菜品信息");  
      }

    }

    public function food_examine_exit(){

      dump(input(''));


    }
    //审核修改（批量）
    public function food_adopt_exit(){
      // dump(input(''));
      $pass=input('yes');
      $rebut=input('no');
      $one=input('');
      if (!isset($one['id'])) {
        $this->error('请选择要操作的菜品');
      }else{
        $id=$one['id'];
      }
      

      // dump($pass);
      // dump($rebut);
      // dump($id);
      if ($pass!='') {
        // dump(1111111);
        $all_id=implode($id, ',');
        // dump($all_id);
        $wh['ID'] = array('in',$all_id);
        // dump($wh);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['NvrFd12' => "1"]);
        if($res){
            welog($text='菜品批量审核通过成功！'.' - '.'ID:'.$all_id);
            $this->success('菜品批量审核通过成功！',url('admin/Food/food_examine'));
        }else{
            $this->error('菜品批量审核通过失败');
        }
      }elseif ($rebut!='') {
        // dump(2222222);
        $all_id=implode($id, ',');
        // dump($all_id);
        $wh['ID'] = array('in',$all_id);
        // dump($wh);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['NvrFd12' => "2"]);
        if($res){
            welog($text='菜品批量审驳回成功！'.' - '.'ID:'.$all_id);
            $this->success('菜品批量审驳回成功！',url('admin/Food/food_examine'));
        }else{
            $this->error('菜品批量驳回失败');
        }
      }


    }

    //菜品列表中菜品详情中的所属分类（不知名分类）
    public function category(){
      if (input('')) {
        // dump(input(''));
        $pid=input('code');
        // dump($pid);
        // $data=Db::table('ceb_Dictionary')
        //      ->where("Code","like","003034%")
        //      ->select();
        // dump($data);
        $cate_son=Db::query("select * from ceb_Dictionary where Left(Code,6) = '$pid'");
          foreach ($cate_son as $k => $v) {
            if (strlen($cate_son[$k]['Code'])!=9) {
                unset($cate_son[$k]);
            }
          }
        dump($cate_son);
        $this->assign('cate_son',$cate_son);
        return $this->fetch('cate');
      }
    }
    //会长神秘代码
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
