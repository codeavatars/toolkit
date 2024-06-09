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


class ArrayTool
{
    /**
     * 数组转换XML
     * @param $array
     * @return string
     */
    public static function formatArrayToXML($array){
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .="<xml>";
        if(is_array($array)){
            foreach($array as $key => $value){
                $xml .= "<$key><![CDATA[".$value."]]></$key>";
            }
        }
        $xml .="</xml>";
        return $xml;
    }
    
    /**
     * XML转换数组
     * @param $xmlstr
     * @return mixed
     */
    public static function formatXmlToArray($xmlstr){
        //提取目标数据，过滤响应头部信息
        $xmlstr = substr($xmlstr, strpos($xmlstr,'<xml>'));
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xml = simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xmlArray = json_decode(json_encode($xml),true);
        return $xmlArray;
    }
    
    /**
     * 检测二维数组
     * @param $arr
     * @return bool
     */
    public static function is2Array($arr): bool
    {
        //过滤Collection类型
        if($arr instanceof \think\Collection){
            $arr = $arr->toArray();
        }
        
        //检测第一层
        if(is_array($arr)){
            $chkResult = true;
            foreach ($arr as $key=>$item){
                //检测第二层
                //                if(is_numeric($key) && is_array($item)){
                //                    continue; //通过检测
                //                }
                if(is_numeric($key)){
                    continue; //通过检测
                }
                $chkResult = false; break;
            }
            return $chkResult;
        }
        return false;
    }
    
    //数组差集
    public static function diff(array $a,array $b){
        return array_merge(array_diff($a,$b),array_diff($b,$a));
    }

    //复制数组
    public static function copy(array $arr):array{
        return clone $arr;
    }
    
    //复制数组
    public static function copy2(array $arr):array {
        return json_decode(json_encode($arr),true);
    }
}