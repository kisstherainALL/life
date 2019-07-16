<?php
namespace app\index\controller;

class Index extends \think\Controller
{
    public function index()
    {
        // print_r($_COOKIE);exit;
        // return $this->fetch('');
        // dump('123123');
        // dump($_COOKIE);exit;
        // header("localhref: http://http://a.gzruizi.com/admin");
        // header('Location:http://a.gzruizi.com/ct.php/restadmin/index/index.html');
        $response = new \think\response\Redirect('restadmin/Index/index');
        throw new \think\exception\HttpResponseException($response);
    }


    
}
