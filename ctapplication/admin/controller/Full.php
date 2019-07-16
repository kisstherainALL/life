<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller ;
class Full extends \think\Controller
{
    public function index($page=1)
    {
      $res=Db::table("ceb_Product")
          ->page($page,20)
          ->alias('p')
          ->field('P.ID,P.Title,P.Price,P.SaleNum,P.ClickNum,P.Quantity,P.TimeFd1,P.Status,P.Img,p.StartTime,p.Filled,p.Reduce,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where("ActivityType='3' and Type='1'")
          ->order('StartTime desc')
          ->select();

      foreach ($res as $k => $v) {
        $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
      }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }

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
        // dump($id);
        // dump($status);exit;
        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['Status' => "$status"]);
        welog($text=$status.'(满减)——'.$one['Title']);    
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
        if ($Filled <= $Reduce) {
          $this->error('请填写真确的满减活动规则',url('admin/Full/add'));
        }
        // dump($StartTime);
        $one=Db::table("ceb_Product")
            ->where($wh)
            ->find();

        $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['ActivityType' => '3','StartTime' => "$StartTime",'EndTime' => "$EndTime",'Filled' => "$Filled",'Reduce' => "$Reduce"]);
        if ($res!=0) {
          welog($text='添加满减活动商品'.'——'.$one['Title']); 
          $this->success('添加成功',url('admin/Full/index'));
        }else{
           $this->error('添加失败');
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
     
      // $res=Db::table("ceb_Product")
      //     ->page($page,20)
      //     ->alias('p')
      //     ->field('p.ID,p.Title,p.price,p.SaleNum,p.ClickNum,p.Quantity,p.TimeFd1,p.Status,s.Name as sname')
      //     ->join('ceb_ShopMain s','p.ShopID=s.ID')
      //     ->where("ActivityType='0'")
      //     ->where("Type='1'")
      //     ->where("p.Status='上架'")
      //     // ->where("s.Name='Life风自营'")
      //     ->order('TimeFd1 desc')
      //     ->select();
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
            ->update(['ActivityType' => '0','StartTime' => "",'EndTime' => "",'Quantity' => "",'Leastnum' => "",'Mostnum' => ""]);
        if ($res!=0) {
          welog($text='删除满减活动商品'.'——'.$one['Title']);
          $this->success('删除成功',url('admin/Full/index'));
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
        // dump($wh);exit;
       $res=Db::table("ceb_Product")
            ->where($wh)
            ->update(['ActivityType' => '0','StartTime' => "",'EndTime' => "",'Quantity' => "",'Leastnum' => "",'Mostnum' => ""]);
        if($res){
            welog($text='批量删除删除(满减活动)商品');
            $this->success('批量删除商品成功',url('admin/Full/index'));
        }else{
            $this->error('批量删除商品失败');
        }
      }
      
      // dump('123123');
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
      // $more_img = explode(',',$res['ImgList']);ProductClass
      // dump($more_img);
      $where['Code']=$res['ProductClass'];
      $pid=substr($res['ProductClass'],0,6);
      $res['StartTime']=substr($res['StartTime'],0,19);
      $res['EndTime']=substr($res['EndTime'],0,19);
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

      $this->assign('res2',$res);
      $this->assign('data',$data);
      $this->assign('data_pid',$data_pid);
      $this->assign('cate_pid',$cate_pid);
      return $this->fetch('');
    }

    //商店搜索列表
    public function onedollar_shop($page=1){
      $keyword = input('keyword');
      if (input('keyword')!='') {
        // dump(input('keyword'));
        $hot = $keyword;
          $res=Db::table("ceb_Product")
          ->page($page,20)
          ->alias('p')
          ->field('P.ID,P.Title,P.Price,P.SaleNum,P.ClickNum,P.Quantity,P.TimeFd1,P.Status,P.Img,p.StartTime,p.Filled,p.Reduce,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where("ActivityType='3' and Type='1' and s.Name like '%". $hot."%'")
          ->order('StartTime desc')
          ->select();

      }else{
        $res=Db::table("ceb_Product")
            ->page($page,20)
            ->alias('p')
            ->field('P.ID,P.Title,P.Price,P.SaleNum,P.ClickNum,P.Quantity,P.TimeFd1,P.Status,P.Img,p.StartTime,p.Filled,p.Reduce,s.Name as sname')
            ->join('ceb_ShopMain s','p.ShopID=s.ID')
            ->where("ActivityType='3' and Type='1'")
            ->order('StartTime desc')
            ->select(); 
      }
      foreach ($res as $k => $v) {
          $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
        }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('index'); 
    }

    //商品名搜索
    public function onedollar_list($page=1){
      $keyword = input('keyword');
      if (input('keyword')!='') {
        $hot = $keyword;
          $res=Db::table("ceb_Product")
          ->page($page,20)
          ->alias('p')
          ->field('P.ID,P.Title,P.Price,P.SaleNum,P.ClickNum,P.Quantity,P.TimeFd1,P.Status,P.Img,p.StartTime,p.Filled,p.Reduce,s.Name as sname')
          ->join('ceb_ShopMain s','p.ShopID=s.ID')
          ->where("ActivityType='3' and Type='1' and p.Title like '%". $hot."%'")
          ->order('StartTime desc')
          ->select();

      }else{
        $res=Db::table("ceb_Product")
            ->page($page,20)
            ->alias('p')
            ->field('P.ID,P.Title,P.Price,P.SaleNum,P.ClickNum,P.Quantity,P.TimeFd1,P.Status,P.Img,p.StartTime,p.Filled,p.Reduce,s.Name as sname')
            ->join('ceb_ShopMain s','p.ShopID=s.ID')
            ->where("ActivityType='3' and Type='1'")
            ->order('StartTime desc')
            ->select();
        
      }
      foreach ($res as $k => $v) {
          $res[$k]['TimeFd1']=substr($res[$k]['TimeFd1'],0,16);
        }

      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('index'); 
    }

    // public function edit(){
    //   dump(input(''));

    //   $file = request()->file('image');
    //   $files = request()->file('photo');
    //   if ($file) {

    //     $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    //     if($info){
    //         // 成功上传后 获取上传信息
    //         // 输出 jpg
    //         // echo $info->getExtension(); //时间文件夹+图片名
    //         // 输出 20160820/42a79759f284b767dfcb2a01979042jpg
    //         // echo $info->getSaveName(); //时间文件夹+图片名
    //         // 输出 42a79759f284b767dfcb2a01979042jpg
    //         echo $info->getFilename();    //图片名
    //         // $imge=$info->getFilename();
    //         // dump($imge);
    //     }else{
    //         // 上传失败获取错误信息
    //         echo $file->getError();
    //     }
    //   }

    //   if ($files) {
    //     foreach($files as $file){
    //         // 移动到框架应用根目录/public/uploads/ 目录下
    //         $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    //         if($info){
    //             // 成功上传后 获取上传信息
    //             // 输出 jpg
    //             echo $info->getExtension(); 
    //             // 输出 42a79759f284b767dfcb2a0197904287.jpg// implode
    //             echo $info->getFilename();
    //             $photo['all']=$info->getFilename();
    //             $one=count($photo['all']);
    //             // $more_img[] = explode(',',$photo['all']);
    //             // print_r($photo['all']);
                 
    //         }else{
    //             // 上传失败获取错误信息
    //             echo $file->getError();
    //         } 

    //     }
    //     // dump($more_img);
        
    //   }

    // }
    //修改
    public function edit(){
      // dump(input(''));
      $wh['ID']=input('id');
      $title=input('title');
      $cate_pid=input('cate_pid');
      $cate_son=input('cate_son');
      $starttime=input('starttime');
      $endtime=input('endtime');
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
            ->update(['Title' => "$title",'ProductClass' => "$cate_son",'StartTime' => "$starttime",'EndTime' => "$endtime",'Price' => "$price",'SaleNum' => "$salenum",'ClickNum' => "$clicknum",'Detail' => "$content"]);
        if ($res!=0) {
          welog($text='修改满减活动商品'.'——'.$one['Title']);
          $this->success('修改成功',url('admin/Full/index'));
        }else{
           $this->error('修改失败');
        }  

    }
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
        // dump($cate_son);
        $this->assign('cate_son',$cate_son);
        return $this->fetch('cate');
      }
    }


    
    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

    public function get_category($data,$pid = 0,$level = 1)
    {
        if(!isset($data['old'])){
            $da['old'] = $data;//用来循环的数据
            $da['new'] = array();//记录循环好的新数据
            $data = $da;
            unset($da);
        }
        foreach ($data['old'] as $k => $v) {
            if($v['pid'] == $pid){
                $v['level'] = $level;
                // var_dump($v);exit;
                $data['new'][$v['ID']] = $v;
                unset($data['old'][$k]);//把当前选中分类清除 因为我自己不可能是自己的分类
                $son = $this->get_category($data,$v['ID'],$level+1);
                if($son){
                    $data['new'] = $son;
                }
            }
        }
        return $data['new'];
    }

  

}
