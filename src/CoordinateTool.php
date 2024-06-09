<?php
/**
 * +----------------------------------------------------------------------
 * | Library for ThinkPHP
 * +----------------------------------------------------------------------
 * | @Copyright: codeavatar   @Year: 2024
 * +----------------------------------------------------------------------
 * | @Email: codeavatar@aliyun.com
 * +----------------------------------------------------------------------
 * | @Website: https://www.codeavatar.vip
 * +----------------------------------------------------------------------
 * | @Source: https://gitee.com/codeavatar/toolkit
 * +----------------------------------------------------------------------
 **/
declare (strict_types=1);

namespace codeavatar\toolkit;


class CoordinateTool
{
    const EARTH_RADIUS = 6371.393; //地球半径（选项：6377.830》赤道半径；6356.9088》极半径；6371.393》平均半径；）单位KM

    public function returnSquarePoint($lng, $lat,$distance = 1){
        $dlng =  2 * asin(sin($distance / (2 * self::EARTH_RADIUS)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance/self::EARTH_RADIUS;
        $dlat = rad2deg($dlat);
        return array(
            'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
            'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
            'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
            'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );

        //使用此函数计算得到结果后，带入sql查询。
//    $squares = returnSquarePoint($lng, $lat);
//    $info_sql = "select id,locateinfo,lat,lng from `lbs_info` where lat<>0 and lat>{$squares['right-bottom']['lat']} and lat<{$squares['left-top']['lat']} and lng>{$squares['left-top']['lng']} and lng<{$squares['right-bottom']['lng']} ";
    }

    /**
     * 计算地球两点间的距离
     * @param $lng1
     * @param $lat1
     * @param $lng2
     * @param $lat2
     * @return float
     */
    public function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1);// deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * self::EARTH_RADIUS;

        return $s;
    }

    /**
     * 指定半径，计算球体两点间的距离
     * @param $lon1
     * @param $lat1
     * @param $lon2
     * @param $lat2
     * @param float $radius 默认地球半径
     * @return float|int
     */
    public function getDistanceByRadius($lon1, $lat1, $lon2, $lat2, $radius = self::EARTH_RADIUS)
    {
        $rad = floatval(M_PI / 180.0);

        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lon1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lon2) * $rad;

        $theta = $lon2 - $lon1;

        $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

        if ($dist < 0) {
            $dist += M_PI;
        }
        return $dist = $dist * $radius;
    }
}