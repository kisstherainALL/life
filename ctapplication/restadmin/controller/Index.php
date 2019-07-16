<?php
namespace app\restadmin\controller;
use think\Db;
class Index extends Restadmin
{
    public function index()
    {   
       
        // print_r($_COOKIE);exit;
        // dump($_COOKIE['memberid']);
        SESSION('memberid',$_COOKIE['memberid']);
        SESSION('membername',$_COOKIE['membername']);
        SESSION('password',$_COOKIE['password']);
        // dump($_SESSION);
        // SESSION('membername','13533771031');
        // SESSION('password','TA6732');
        return $this->fetch(''); 
    }

    public function welcome()
    {

      return $this->fetch('welcome');
    }
  

}
