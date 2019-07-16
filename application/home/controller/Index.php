<?php
namespace app\Home\controller;

class Index extends \think\Controller
{
    public function index()
    {
		$res=\think\Db::table("ceb_Cart")->select();
		dump($res);
		$var["name"]="123";
		$var["name2"]="45555";
		return $this->fetch('index',$var);
    }
}
