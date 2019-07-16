<?php
namespace app\admin\controller;
use think\Db;
class Goods extends \think\Controller
{
    public function index()
    {
    
  

      return $this->fetch('index');
    }

    public function welcome()
    {
      return $this->fetch('welcome');
    }

    //商品列表+商品搜索
    public function goods_list($page=1){
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
          ->where("a.Type='1'  and NvrFd12='1' and a.Title like '%". $hot."%'")
          ->order('ID desc')
          ->select();

      }else{
        $res=DB::table('ceb_Product')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='1' and NvrFd12='1'")
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
          ->where("a.Type='1' and NvrFd12='1' and s.Name like '%". $hot."%'")
          ->order('ID desc')
          ->select();
      }else{
        $res=DB::table('ceb_Product')
            ->page($page,20)
            ->alias('a')
            ->field('a.ID,a.Title,a.Price,a.SaleNum,a.ClickNum,a.Status,a.TimeFd1,a.Img,a.NvrFd12,s.Name')
            ->join('ceb_ShopMain s','a.ShopID=s.ID')
            ->where("a.Type='1' and NvrFd12='1'")
            ->order('ID desc')
            ->select();
        
      }
      foreach ($res as $k => $v) {
          $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
        }

      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('goods_list'); 
    }
    //商品审核
    public function goods_examine($page=1){
      $where='';
      $where2='';
      if (input('keyword')||input('adopt')) {
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
            ->where("a.Type='1'")
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
            ->where("a.Type='1'")
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
    public function goods_examine_pass(){
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
          // log("修改了商品信息");  
      }
    }

    //驳回
    public function goods_examine_rebut(){
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
          welog($text='驳回商品 —— '.$one['Title']);
          echo json_encode($res);
        }
          // log("修改了商品信息");  
      }

    }

    public function goods_examine_exit(){

      dump(input(''));


    }
    //审核修改
    public function goods_adopt_exit(){
      // dump(input(''));
      $pass=input('yes');
      $rebut=input('no');
      $one=input('');
      if (!isset($one['id'])) {
        $this->error('请选择要操作的商品');
      }else{
        $id=$one['id'];
      }
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
            welog($text='商品批量审核通过成功！'.' - '.'ID:'.$all_id);
            $this->success('商品批量审核通过成功！',url('admin/Goods/goods_examine'));
        }else{
            $this->error('商品批量审核通过失败');
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
            welog($text='商品批量审驳回成功！'.' - '.'ID:'.$all_id);
            $this->success('商品批量审驳回成功！',url('admin/Goods/goods_examine'));
        }else{
            $this->error('商品批量驳回失败');
        }
      }


    }

    //详情
    public function info()
    {
      // dump(input(''));
      $wh['p.ID']=input('id');
      $res=Db::table('ceb_Product')
          ->alias('p')
           ->field('p.*,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where($wh)
          ->find();
      // dump($res);
      $more_img = explode('|',$res['ImgList']);
      // dump($more_img);
      foreach ($more_img as $k => $v) {
      
        if (substr($more_img[$k],1,10)!="UploadFile") {
          unset($more_img[$k]);
          

        }
      }
      $Imglist=array_unique($more_img);//去除数组的重复元素
      
      // dump($Imglist);
      $where['Code']=$res['ProductClass'];
      $pid=substr($res['ProductClass'],0,6);
      $res['StartTime']=substr($res['StartTime'],0,19);
      $res['EndTime']=substr($res['EndTime'],0,19);
      $res['TimeFd1']=substr($res['TimeFd1'],0,19);
      // dump($pid);
      // $where['Code']="003034";
      // $data=Db::table('ceb_Dictionary')->where($where)->select();
      $data=Db::query("select * from ceb_Dictionary where Left(Code,6) = '$pid'");
      foreach ($data as $k => $v) {
        if (strlen($data[$k]['Code'])!=9) {
            unset($data[$k]);
        }
      }  
      // dump($data);exit;
      if ($pid) {
        $data_pid=Db::table('ceb_Dictionary')->where("Code","$pid")->find();
      }
      // dump($data_pid);
      // dump($data);
      $cate_pid=Db::query("select * from ceb_Dictionary where Left(Code,4) = '0030' and Display=1");
      foreach ($cate_pid as $k => $v) {
        if (strlen($cate_pid[$k]['Code'])!=6) {
            unset($cate_pid[$k]);
        }
      }

      $this->assign('Imglist',$Imglist);//多图
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
        $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();
        // dump($status);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['Status' => "$status"]);
        if ($res!=0) {
          welog($text=$status.'(商品) —— '.$one['Title']);
          echo json_encode($res);
        }
          // log("修改了商品信息");  
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
        $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();

        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['ActivityType' => '3','StartTime' => "$StartTime",'EndTime' => "$EndTime",'Filled' => "$Filled",'Reduce' => "$Reduce"]);
        if ($res!=0) {
          welog($text='添加商品'.'——'.$one['Title']); 
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
      // dump(input(''));
      $wh['ID']=input('id');
      $title=input('title');
      $cate_pid=input('cate_pid');
      $cate_son=input('cate_son');
      $c_time=input('c_time');
      
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
            ->update(['Title' => "$title",'ProductClass' => "$cate_son",'TimeFd1' => "$c_time",'Price' => "$price",'SaleNum' => "$salenum",'ClickNum' => "$clicknum",'Detail' => "$content"]);
        if ($res!=0) {
          welog($text='修改商品'.'——'.$one['Title'].'ID:'.$one['ID']);
          $this->success('修改成功',url('admin/Goods/goods_list'));
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
        $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->delete();
        if ($res!=0) {
          welog($text='删除商品'.' — '.$one['Title']);
         $this->success('删除成功',url('admin/Goods/goods_list'));
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
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        $all=Db::table("ceb_Product")
            ->field('ID,Title')
            ->where($wh)
            ->select();
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->delete();
        if($res){
            foreach ($all as $k => $v) {
              $tem_id=$all[$k]['ID'];
              $tem_title=$all[$k]['Title'];
              welog($text='批量删除删除商品'.' — ID: '.$tem_id.' — 商品: '.$tem_title);
            }
            $this->success('批量删除商品成功',url('admin/Goods/goods_list'));
        }else{
            $this->error('批量删除商品失败');
        }
      }
    }

    //商品类目
    public function goodsctae()
    {
      
      $cate_p=Db::query("select * from ceb_Dictionary where Left(Code,6) like '003%' order by OrderID ASC");
      foreach ($cate_p as $k => $v) {
        if (strlen($cate_p[$k]['Code'])!=6) {
            unset($cate_p[$k]);
        }
      }
      // dump($cate_p);
      $this->assign('cate_p',$cate_p);
      return $this->fetch('');
    }

    public function cate_info(){
        
        $wh['ID']=input('id');
        $res=Db::table("ceb_Dictionary")
              ->where($wh)
              ->find();
        // dump($res); 
        $this->assign('res2',$res);
        return $this->fetch('');     

    }

    public function cate_edit(){
      // dump(input(''));
      $id=input('cate_id');
      $name=input('cate_name');
      $sort=input('sort');
      $open=input('openshop');

      $wh['ID']=$id;
      $one=Db::table("ceb_Dictionary")
            ->where($wh)
            ->find();

      $res=Db::table("ceb_Dictionary")
            ->where($wh)
            ->update(['Name' => "$name",'OrderID' => "$sort",'IsShow' => "$open"]);
        if ($res!=0) {
          welog($text='修改商品分类'.'——'.$one['Name'].'ID:'.$one['ID']);
          $this->success('修改成功',url('admin/Goods/goodsctae'));
        }else{
          $this->error('修改失败');
        }      


    }

    public function catep_delete_all(){
      if (input('')) {
          $id = input('');
          $one=implode($id['id'], ',');
          // dump($one);
          $wh['ID'] = array('in',$one);
          $riz=Db::table("ceb_Dictionary")
              ->where($wh)
              ->select();
          foreach ($riz as $k => $v) {
                $tem[]=$riz[$k]['Name'];
                $tem_name=implode($tem, ',');
              }
          // dump($tem_name);        
          // dump($riz);exit;
         $res=Db::table("ceb_Dictionary")
              ->where($wh)
              ->delete();
          if($res){
              welog($text='批量删除删除商品一级类目 —— ID：'.$one.'-'.$tem_name);
              $this->success('批量删除商品一级分类成功',url('admin/Goods/goodsctae'));
          }else{
              $this->error('批量删除商品一级分类失败');
          }
        }
    }

    public function goodsctae_add() {

      if (input('cate_name')) {
        // dump(input(''));exit;
        $cate_code=input('cate_code');
        $name=input('cate_name');
        $sort=input('sort');
        $openshop=input('openshop');
        $cate_p=Db::query("select * from ceb_Dictionary where Left(Code,6) like '003%' order by Code DESC");
        foreach ($cate_p as $k => $v) {
          if (strlen($cate_p[$k]['Code'])!=6) {
              unset($cate_p[$k]);
          }
        }
        if (empty($cate_p)) {
          $max=$cate_code.'001';
        }else{
          $tem=$cate_p[1]['Code'];
          // dump($tem);
          $num_code=$tem+1;
          $max='00'.$num_code;
        }
        
        // dump($max);
        // dump($cate_p);exit;
        
       
        $data = ['Name' => "$name", 'OrderID' => "$sort", 'Code' => "$max", 'IsShow' => "$openshop"];
        $res=Db::table('ceb_Dictionary')->insert($data);
        
          if ($res!=0) {
            welog($text='添加一级商品分类'.' —— '.$name); 
            $this->success('添加一级商品分类成功',url('admin/Goods/goodsctae'));
          }else{
             $this->error('添加一级商品分类失败');
          }
      }else{
        return $this->fetch('');
      }
    }  

   

  public function goodscate_son(){

    $keyword=input('Code');

    // $wh['Code']=array('like','%'.$keyword.'%');
    $cate_p=Db::query("select * from ceb_Dictionary where Left(Code,6) like '". $keyword."%' order by OrderID ASC");
    $cate_fz=Db::query("select * from ceb_Dictionary where Left(Code,6) like '". $keyword."%' order by OrderID ASC");
    foreach ($cate_fz as $k => $v) {
        if (strlen($cate_fz[$k]['Code'])!=6) {
            unset($cate_fz[$k]);
        }
      }
    // $cate_p=Db::table("ceb_Dictionary")
    //        ->where($wh)
    //        ->select();
      foreach ($cate_p as $k => $v) {
        if (strlen($cate_p[$k]['Code'])!=9) {
            unset($cate_p[$k]);
        }
      }
    // dump($cate_p);
    $this->assign('cate_p',$cate_p);
    $this->assign('cate_fz',$cate_fz);
    $this->assign('keyword',$keyword);
    return $this->fetch('');
  }

  public function cateson_info(){
      
      $wh['ID']=input('id');
      $res=Db::table("ceb_Dictionary")
            ->where($wh)
            ->find();
      // dump($res); 
      $this->assign('res2',$res);
      return $this->fetch('');     

  }

  public function cateson_edit(){
    // dump(input(''));
    $id=input('cate_id');
    $name=input('cate_name');
    $sort=input('sort');
    // $open=input('openshop');

    $wh['ID']=$id;
    $one=Db::table("ceb_Dictionary")
          ->where($wh)
          ->find();

    $res=Db::table("ceb_Dictionary")
          ->where($wh)
          ->update(['Name' => "$name",'OrderID' => "$sort"]);
      if ($res!=0) {
        welog($text='修改商品子集分类'.'——'.$one['Name'].'ID:'.$one['ID']);
        $this->success('修改成功',url('admin/Goods/goodsctae'));
      }else{
        $this->error('修改失败');
      }      


  }

  public function cates_delete_all(){
    if (input('')) {
        $id = input('');
        $one=implode($id['id'], ',');
        // dump($one);
        $wh['ID'] = array('in',$one);
        $riz=Db::table("ceb_Dictionary")
            ->where($wh)
            ->select();
        foreach ($riz as $k => $v) {
              $tem[]=$riz[$k]['Name'];
              $tem_name=implode($tem, ',');
            }
       $res=Db::table("ceb_Dictionary")
            ->where($wh)
            ->delete();
        if($res){
            welog($text='批量删除删除商品二级类目 —— ID：'.$one.'-'.$tem_name);
            $this->success('批量删除商品二级分类成功',url('admin/Goods/goodsctae'));
        }else{
            $this->error('批量删除商品二级分类失败');
        }
      }
  }

  public function sonctae_add() {
    
      $add_code=input('sonadd_code');
      // dump($add_code);
      $this->assign('add_code',$add_code);
    
    
    
    if (input('cate_name')) {
      // dump(input(''));exit;
      $cate_code=input('cate_code');
      $name=input('cate_name');
      $sort=input('sort');
      $cate_p=Db::query("select * from ceb_Dictionary where Left(Code,9) like '". $cate_code."%' order by Code DESC");
      foreach ($cate_p as $k => $v) {
        if (strlen($cate_p[$k]['Code'])!=9) {
            unset($cate_p[$k]);
        }
      }
      if (empty($cate_p)) {
        $max=$cate_code.'001';
      }else{
        $tem=$cate_p[0]['Code'];
        // dump($tem);
        $num_code=$tem+1;
        $max='00'.$num_code;
      }
      
      // dump($max);
      // dump($cate_p);exit;
      
     
      $data = ['Name' => "$name", 'OrderID' => "$sort", 'Code' => "$max"];
      $res=Db::table('ceb_Dictionary')->insert($data);
      
        if ($res!=0) {
          welog($text='添加二级商品分类'.' —— '.$name); 
          $this->success('添加成功',url('admin/Goods/goodsctae'));
        }else{
           $this->error('修改失败');
        }
    }else{
      return $this->fetch('');
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
