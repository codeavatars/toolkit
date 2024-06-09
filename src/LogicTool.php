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


class LogicTool
{
    /**
     * 获取方法无限参数
     * @return array
     */
    public static function getArgs(): array
    {
        if(func_num_args() > 1){
            return func_get_args();
        }
        return [];
    }
}