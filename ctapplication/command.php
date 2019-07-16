<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
// 

//时间是现在时间
function welog($text="123")
{
    // $insert['text']=$text;// 记录实体内容
    $text=$text;// 记录实体内容
    $Name=session('gg_uname');// 记录操作人
    $c_time=date("Y-m-d H:i:s",time());// 记录操作时间
    $OpenID=session('gg_uid');
    $OpenIP=\think\Request::instance()->ip();// 记录当前用户IP
    $data = ['Name' => "$Name", 'OpenID' => "$OpenID", 'OpenIP' => "$OpenIP", 'text' => "$text", 'c_time' => "$c_time"];
    $rizhi=Db::table('ceb_WeLog')->insert($data);
}