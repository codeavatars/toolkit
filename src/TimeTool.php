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


class TimeTool
{
    private $begintime;
    private $endtime;
    
    public function start(){
        $this->begintime = self::microtime_float();
    }
    
    public function stop(){
        $this->endtime = self::microtime_float();
    }
    
    public function result(){
        return ($this->endtime - $this->begintime);
    }
    
    public function outprint($prefix='--'){
        $seconds_diff = $this->result();
        LogTool::writelog("【{$prefix}】运行耗时{$seconds_diff}秒");
    }
    
    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    //====================【属性】====================
    
    /**
     * @return mixed
     */
    public function getBegintime()
    {
        return $this->begintime;
    }
    
    /**
     * @param mixed $begintime
     */
    public function setBegintime($begintime): void
    {
        $this->begintime = $begintime;
    }
    
    /**
     * @return mixed
     */
    public function getEndtime()
    {
        return $this->endtime;
    }
    
    /**
     * @param mixed $endtime
     */
    public function setEndtime($endtime): void
    {
        $this->endtime = $endtime;
    }
}