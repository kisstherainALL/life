<?php
namespace app\admin\controller;
use think\Db;
class Advert extends \think\Controller
{
    public function index()
    {
      // $res=Db::table('ceb_Order')->field('ID,Receiver')->select();
      // dump($res3);
  

      return $this->fetch('index');
    }

    public function welcome()
    {
      return $this->fetch('welcome');
    }

    public function home_banner($page=1)
    {
      $res=DB::table('ceb_WeBanner')
          ->page($page,20)
          ->order('sort asc')
          ->select();
         
      // dump($res);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }

    public function dc_banner($page=1)
    {
      $res=DB::table('ceb_Article')->page($page,10)->where("ClassCode=009001002")->select();
      foreach ($res as $k => $v) {
            if ($res[$k]['NvrFd13']=='|001002|^##^|显示|') {
              $res[$k]['NvrFd13']="显示";
            }elseif ($res[$k]['NvrFd13']=='|001001|001002|^##^|推荐|显示|') {
              $res[$k]['NvrFd13']="推荐 显示";
            }elseif ($res[$k]['NvrFd13']=='|001002|^##^|推荐|') {
              $res[$k]['NvrFd13']="推荐";
            }           
      }
      // dump($res);
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
    }
    
    public function shop_banner($page=1)
    {
      $res=DB::table('ceb_Article')->page($page,10)->where("ClassCode=009001003")->order('TimeFd1 desc')->select();
      foreach ($res as $k => $v) {
            if ($res[$k]['NvrFd13']=='|001002|^##^|显示|') {
              $res[$k]['NvrFd13']="显示";
            }elseif ($res[$k]['NvrFd13']=='|001001|001002|^##^|推荐|显示|') {
              $res[$k]['NvrFd13']="推荐 显示";
            }elseif ($res[$k]['NvrFd13']=='|001002|^##^|推荐|') {
              $res[$k]['NvrFd13']="推荐";
            }           
      }
      $this->assign('res2',$res);
      $this->assign('page',$page);
      return $this->fetch('');
      
    }
    
    public function dc_home_pic($page=1)
    {
      $res=DB::table('ceb_Article')
          ->page($page,10)
          ->where("ClassCode=009001004")
          ->find();
      // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');
    }

    public function banner_info()
    {
      // dump(input(''));
      $wh['ID']=input('id');
      // dump($wh);
      $res=DB::table('ceb_Article')
          ->where($wh)
          ->find();
      // dump($res);exit;
      if ($res['NvrFd13']=='|001002|^##^|显示|') {
        $res['NvrFd13']="显示";
      }elseif ($res['NvrFd13']=='|001001|001002|^##^|推荐|显示|') {
        $res['NvrFd13']="推荐 显示";
      }elseif ($res['NvrFd13']=='|001002|^##^|推荐|') {
        $res['NvrFd13']="推荐";
      }           
      if ($res['TimeFd1']) {
        $res['TimeFd1']=substr($res['TimeFd1'], 0,19);
      }
      // dump($res);
      $this->assign('res2',$res);
      return $this->fetch('');
    }

    //Bananer添加
    public function banner_add()
    {
      if (input('')) {
        dump(input(''));exit;
        $file = request()->file('image');
        if($file){
        $info =  $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/imge');
        $w = $info->getpathName();
        // dump($w);
        $banner=$info->getsaveName();
        $title=input('title');
        $link=input('link');
        $sort=input('sort');
        $pic_url = '/public/uploads/imge/'.$banner;
        $c_time=date("Y-m-d H:i:s",time());// 记录操作时间
        // dump($pic_url);exit;
        // dump($link);
        // dump($w);
        // dump($info);
          if($info){
            $data = ['Banner' => "$pic_url", 'Title' => "$title", 'sort' => "$sort", 'Link' => "$link", 'c_time' => "$c_time"];
            $res=Db::table('ceb_WeBanner')->insert($data);
            if ($res!=0) {
              welog($text='添加Banner图');  
              $this->success('添加成功',url('admin/Advert/home_banner'));

            }else{
               $this->error('添加失败');
            }
              
              
          }else{
              // 上传失败获取错误信息
              echo $file->getError();
          }

      }
        // dump($file);
      }else{
        return $this->fetch('');
      }
      // 
      // dump(input('imge'));
      
      
    }

    //Bananer添加
    public function banner_del(){
      if (input('id')) {
        // dump(input(''));
        $id=input('id');
        $wh['ID']=$id;
        $one=Db::table('ceb_WeBanner')->where($wh)->find();
        $res=Db::table('ceb_WeBanner')->where($wh)->delete();
        if ($res) {
          welog($text='删除Banner图'.' - ID:'.$one['ID'].' - '.$one['Title']);  
          $this->success('删除成功',url('admin/Advert/home_banner'));
        }else{
          $this->error('删除失败');
        }
      }
    }

    //Bananer修改
    public function banner_edit()
    {
      $file = request()->file('image');
      // dump(input('imge'));
      dump($file);
    }


    function expExcel()  
    {  
        vendor("phpexcel.PHPExcel");  
        $userName=DB::table('ceb_Article')->where("ClassCode=009001004")->field('ID')->select();  
        $PHPExcel = new \PHPExcel();//实例化  
        $PHPSheet = $PHPExcel->getActiveSheet();  
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称  
       //$PHPSheet->setCellValue("A1","ID")->setCellValue("B1","username");//表格数据  
       //$PHPSheet->setCellValue("A2","001")->setCellValue("B2","元宝");//表格数据  
        $i=1;  
        foreach($userName as $key=>$val){  
               // $PHPSheet->setCellValue('A'.$i,$val['uid'])->setCellValue('B'.$i,$val['username']);//表格数据
               $PHPSheet->setCellValue('A'.$i,$val['ID']);//表格数据   
               $i++;  
           }  
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式  
        header('Content-Disposition: attachment;filename="userName.xlsx"');//下载下来的表格名  
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件  
    }  

    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

  

}
