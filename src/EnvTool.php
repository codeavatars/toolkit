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


class EnvTool
{
    //Windows系统
    public static function isWin(){
        return PATH_SEPARATOR === ';';
    }
    
    /**
     * 检测禁用函数
     * @param $methodName 函数名
     * @return bool
     */
    public static function isDisabledMethod($methodName): bool
    {
        $disable_functions = ini_get('disable_functions');
        $disabled_methods = explode(',', $disable_functions);
        if(is_array($disabled_methods)){
            return in_array(trim($methodName), $disabled_methods);
        }
        return false;
    }
}