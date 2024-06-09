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

use think\facade\Log;

/**
 * TP框架日志输出
 *
 * Class CaLogLib
 * @package lib
 */
class LogTool
{
    /**
     * 用于命令运行的代码输出
     *
     * @param $logs 日志内容
     * @param string $tag 日志标签
     */
    public static function echolog($logs, $tag=''){
        echo('++++++++++++++++++++++++++++++['.($tag??'').'--开始]++++++++++++++++++++++++++++++'.PHP_EOL);
        if (is_array($logs)) {
            foreach ($logs as $key=>$log) {
                echo('['.$key.']'.((is_object($log)||is_array($log))?json_encode($log):$log).PHP_EOL);
            }
        } else {
            echo($logs.PHP_EOL);
        }
        echo('==============================['.($tag??'').'--结束]==============================' . PHP_EOL);
    }

    //tp框架查看日志（/runtime/log/yyyymm/dd.log）
    //其他在同级目录（./log/yyyymm/dd.log）
    public static function writelog($logs, $tag='', $level = \think\Log::WARNING)
    {
        Log::record('++++++++++++++++++++++++++++++['.($tag??'').'--开始]++++++++++++++++++++++++++++++', $level);
        if (is_array($logs)) {
            foreach ($logs as $key=>$log) {
                Log::record('['.$key.']'.((is_object($log)||is_array($log))?json_encode($log):$log), $level);
            }
        } else {
            Log::record($logs, $level);
        }
        Log::record('==============================['.($tag??'').'--结束]==============================' . PHP_EOL, $level);
        Log::save();
    }
    
    /**
     * 输出异常数据到文件
     * @param Exception $exception
     * @return bool
     */
    public static function tracelog($info): void
    {
        $path = app()->getRuntimePath() . 'trace';
        if (!file_exists($path)) mkdir($path, 0755, true);
        if($info instanceof \Exception){
            $name = substr($info->getFile(), strlen(static::syspath()));
            //date('Ymd_His_')
            $file = $path . DIRECTORY_SEPARATOR . date('Ymd_') . strtr($name, ['/' => '.', '\\' => '.']);
            $class = get_class($info);
            return false !== file_put_contents($file,
                "[CODE] {$info->getCode()}" . PHP_EOL .
                "[INFO] {$info->getMessage()}" . PHP_EOL .
                "[FILE] {$class} in {$name} line {$info->getLine()}" . PHP_EOL .
                "[TIME] " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL .
                '[TRACE]' . PHP_EOL . $info->getTraceAsString()
                ,FILE_APPEND);
        }else if(is_object($info)){
            $file = $path . DIRECTORY_SEPARATOR . date('Ymd_') .'record.log';
            return false !== file_put_contents($file,
                "[TIME] " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL .
                "[TRACE] " . json_encode($info) . PHP_EOL
                ,FILE_APPEND);
        }else if(is_string($info)){
            $file = $path . DIRECTORY_SEPARATOR . date('Ymd_') .'record.log';
            return false !== file_put_contents($file,
                "[TIME] " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL .
                "[TRACE] {$info}" . PHP_EOL
                ,FILE_APPEND);
        }else if(is_array($info)){
            $file = $path . DIRECTORY_SEPARATOR . date('Ymd_') .'record.log';
            return false !== file_put_contents($file,
                "[TIME] " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL .
                "[TRACE] ". implode(',',$info) . PHP_EOL
                ,FILE_APPEND);
        }
        $file = $path . DIRECTORY_SEPARATOR . date('Ymd_') .'record.log';
        return false !== file_put_contents($file,
            "[TIME] " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL .
            "[TRACE] {$info}" . PHP_EOL
            ,FILE_APPEND);
    }
}