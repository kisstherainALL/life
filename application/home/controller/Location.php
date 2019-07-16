<?php
namespace app\Home\controller;
use think\Db;
//地图定位（距离计算）
class Location extends \think\Controller
{
    public function index()
    {
        // dump(222333);
        //获取数据
        if (input('')) {
        define('PI',3.1415926535898);
        define('EARTH_RADIUS',6378.137);
        $id = input('');
        
        //获取当前位置的经纬度
        $lat2=$id['loc']['1'];
        $lng2=$id['loc']['0'];
        
        // dump($id['id']);
       
        $one=implode($id['id'], ','); //店铺ID数据
        // dump($one);
        $wh['ID'] = array('in',$one); //符合的条件的店铺ID
        // dump($wh);
        $res=Db::table('ceb_ShopMain')->field('ID,NvrFd2')->where($wh)->select(); //查询店铺获取店铺经纬度
        // dump($res);
        foreach ($res as $k => $v) {
            $tem=explode(',',$res[$k]['NvrFd2']); //分割字段，形成数组形式
            // dump($tem);
            $lat1=$tem['0'];
            $lng1=$tem['1'];
            // dump($lat1);
            // dump($lng1);
            $radLat1 = $lat1 * (PI / 180);
            $radLat2 = $lat2 * (PI / 180);
            $a = $radLat1 - $radLat2;
            $b = ($lng1 * (PI / 180)) - ($lng2 * (PI / 180));
            $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
            $s = $s * EARTH_RADIUS;
            $s = round($s * 10000) / 10000;
            // dump($s);
            $res[$k]['range']=$s; //获取距离$s，拼接进$res 数组里面
        }
        // dump($res);
        echo json_encode($res); //以json格式，返回给ajax
        
        // // //获取2点之间的距离
        // // function GetDistance($lat1, $lng1, $lat2, $lng2){
        // $radLat1 = $lat1 * (PI / 180);
        // $radLat2 = $lat2 * (PI / 180);
        // $a = $radLat1 - $radLat2;
        // $b = ($lng1 * (PI / 180)) - ($lng2 * (PI / 180));
        // $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
        // $s = $s * EARTH_RADIUS;
        // $s = round($s * 10000) / 10000;
        // return $s;

        }
        
    }
}
