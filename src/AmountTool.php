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


class AmountTool
{
    //统一金额格式
    public function format(float $amount,$decimals=4):float{
        if($amount<0) return 0;
        return (float) number_format($amount,$decimals);
    }
    
    //模
    public function mod($val1,$val2){
        $this->calcParamFilter($val1,$val2);
        return bcmod($val1,$val2,self::DECIMALS);
    }
    //除
    public function div($val1,$val2){
        $this->calcParamFilter($val1,$val2);
        return bcdiv($val1,$val2,self::DECIMALS);
    }
    //乘
    public function mul($val1,$val2){
        $this->calcParamFilter($val1,$val2);
        return bcmul($val1,$val2,self::DECIMALS);
    }
    //减
    public function sub($val1,$val2){
        $this->calcParamFilter($val1,$val2);
        return bcsub($val1,$val2,self::DECIMALS);
    }
    //加
    public function add($val1,$val2){
        $this->calcParamFilter($val1,$val2);
        return bcadd($val1,$val2,self::DECIMALS);
    }
    
    //清除末尾0
    public function clearEndZero($amount)
    {
        $amount = trim(strval($amount));
        if (preg_match('#^-?\d+?\.0+$#', $amount)) {
            return preg_replace('#^(-?\d+?)\.0+$#','$1',$amount);
        }
        if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $amount)) {
            return preg_replace('#^(-?\d+\.[0-9]+?)0+$#','$1',$amount);
        }
        return $amount;
    }
    
    
    /**
     * @param $v1
     * @param $v2
     */
    private function calcParamFilter(&$v1,&$v2){
        if(!is_string($v1)) $v1 = (string)$v1;
        if(!is_string($v2)) $v2 = (string)$v2;
    }
}