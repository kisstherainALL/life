<?php
namespace app\common\model;
class Func
{
    public static function array_iconv($arr, $in_charset="gbk", $out_charset="utf-8")
    {
     $ret = eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
     return $ret;
     // 这里转码之后可以输出json
     // return json_encode($ret);
    }

    public function _initialize(){
        $uid = session('gg_uid');
        if($uid == null){
            // $this->rediect('Login/index','请先登录后操作');
          $this->success('请先登录后操作',url('admin/Login/index'));
        }
    }

}