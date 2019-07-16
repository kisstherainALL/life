<?php
namespace app\admin\controller;
use think\Db;
class System extends Admin
{
    

    public function journal($page=1)
    {
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

        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('keyword',$keyword);
        if($start!='')
        {
            // $where["c_time"]=array("egt",$start);
            $where=array('c_time'=>array("egt",$start));
        }

        if($end!='')
        {
            // $where["c_time"]=array("elt",$end); 
            $where=array('c_time'=>array("elt",$end));           
        }

        if($start!='' && $end!='')
        {     
            $where=array('c_time'=>array('between time',[$start,$end]));
        }

        if($keyword!='')
        {
            $where2="(Name like '%". $keyword."%' or Prname like '%". $keyword."%')";
        }

        }
      
        $res=DB::table('ceb_WeLog')
          ->page($page,20)
          ->order('c_time desc')
          ->where($where)
          ->where($where2)
          ->select();
        foreach ($res as $k => $v) {
            $res[$k]['c_time']=substr($res[$k]['c_time'],0,19);
        }  
        $num=count($res);
        // dump($res);exit;
        $this->assign('res',$res);
        $this->assign('num',$num);
        $this->assign('page',$page);
        return $this->fetch('');
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
