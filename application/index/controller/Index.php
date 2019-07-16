<?php
namespace app\index\controller;

class Index extends \think\Controller
{
    public function index()
    {
        // dump('123123');
        // header("localhref: http://http://a.gzruizi.com/admin");
        header('Location:http://a.gzruizi.com/admin/login/index.html');
    }


    
}
