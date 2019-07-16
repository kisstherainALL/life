<?php
use think\Db;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function array_iconv($arr, $in_charset="gbk", $out_charset="utf-8")
    {
     $ret = eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
     return $ret;
     // 这里转码之后可以输出json
     // return json_encode($ret);
    }

/*
    utf-8编码下截取中文字符串,参数可以参照substr函数
    @param $str 要进行截取的字符串
    @param $start 要进行截取的开始位置，负数为反向截取
    @param $end 要进行截取的长度
    func_num_args 返回传递到函数的参数数目 
*/
function utf8_substr($str,$start=0,$end) {
    if(empty($str)){
        return false;
    }
    if (function_exists('mb_substr')){
        if(func_num_args() >= 3) {
            $end = func_get_arg(2);
            return mb_substr($str,$start,$end,'utf-8');
        }
        else {
            mb_internal_encoding("UTF-8");
            return mb_substr($str,$start);
        }      
 
    }
    else {
        $null = "";
        preg_match_all("/./u", $str, $ar);
        if(func_num_args() >= 3) {
            $end = func_get_arg(2);
            return join($null, array_slice($ar[0],$start,$end));
        }
        else {
            return join($null, array_slice($ar[0],$start));
        }
    }
}

/**
* 可以统计中文字符串长度的函数
* @param $str 要计算长度的字符串
* @param $type 计算长度类型，0(默认)表示一个中文算一个字符，1表示一个中文算两个字符
*
*/
function abslength($str)
{
    if(empty($str)){
        return 0;
    }
    if(function_exists('mb_strlen')){
        // return 123;
        // function_exists 检查函数是否已定义
        return mb_strlen($str,'utf-8');
    }
    else {
        // return 235;
        preg_match_all("/./u", $str, $ar);
        return count($ar[0]);
    }
}
/**
 * 字符串超出长度尾部追加省略号
 * $text 要截取的字符串
 * $length 截取的长度
 * 超过长度 用什么代替
 */
function subtext($text, $length,$dot='...'){
    // return abslength($text);
    if(abslength($text) > $length){
        return utf8_substr($text, 0, $length).$dot;
    }
    return $text;
}

/**
 * excel表格导出
 * @param string $fileName 文件名称
 * @param array $headArr 表头名称
 * @param array $data 要导出的数据
 * @author static7  */
function excelExport($fileName = '', $headArr = [], $data = []) {
    $fileName .= "_" . date("Y_m_d", Request::instance()->time()) . ".xls";
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties();
    $key = ord("A"); // 设置表头
    foreach ($headArr as $v) {
        $colum = chr($key);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $key += 1;
    }
    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach ($data as $key => $rows) { // 行写入
        $span = ord("A");
        foreach ($rows as $keyName => $value) { // 列写入
            $objActSheet->setCellValue(chr($span) . $column, $value);
            $span++;
        }
        $column++;
    }
    $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表
    $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename='$fileName'");
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output'); // 文件通过浏览器下载
    exit();
}

function get_category($data,$pid = 0,$level = 1)
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
                $son = get_category($data,$v['ID'],$level+1);

                if($son){
                    $data['new'] = $son;
                }

            }
        }
        return $data['new'];
    } 

//时间是现在时间
function welog($text)
{
    // $insert['text']=$text;// 记录实体内容
    $text=$text;// 记录实体内容
    $Name=session('gg_uname');// 记录操作人
    $c_time=date("Y-m-d H:i:s",time());// 记录操作时间
    $OpenID=session('gg_uid');
    $Prname=session('gg_privlege');
    $OpenIP=\think\Request::instance()->ip();// 记录当前用户IP
    $data = ['Name' => "$Name", 'Prname' => "$Prname", 'OpenID' => "$OpenID", 'OpenIP' => "$OpenIP", 'text' => "$text", 'c_time' => "$c_time"];
    $rizhi=Db::table('ceb_WeLog')->insert($data);
}  

function wedown($title)
{
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=\"$title.xls\"");
    header('Cache-Control: max-age=0'); 
    $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");
    $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
}    