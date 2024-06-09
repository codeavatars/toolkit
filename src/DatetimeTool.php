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


class DatetimeTool
{
    private $diffstr;
    private $formatstr = 'Y-m-d H:i:s';
    
    //将秒数转换成 小时：分钟：秒的格式
    public function secondsToHis($seconds){
        if ($seconds>3600){
            $hours = intval($seconds/3600);
            $time = $hours.":".gmstrftime('%M:%S', $seconds);
        }else{
            $time = gmstrftime('%H:%M:%S', $seconds);
        }
        return $time;
    }
    
    //+-----------------------------------------------------
    //+ 读取方法
    //+-----------------------------------------------------
    
    //时间字符串
    public function getNewDatetimeStr($begintime = '', $seconds = 0)
    {
        if (empty($begintime))
            $begintime = date($this->formatstr);
            
            $timestr = strtotime($begintime);
            if ($seconds == 0 && (!empty($this->diffstr))) {
                return  date($this->formatstr, strtotime($this->diffstr, $timestr));
            }
            return date($this->formatstr, $timestr + $seconds);
    }
    
    //日期字符串
    public function getNewDatestr($begindate = '', $days = 0)
    {
        if (empty($begindate))
            $begindate = date($this->formatstr);
            
            $timestr = strtotime($begindate);
            if (false !== strpos($this->formatstr, ' ')) {
                $this->formatstr = explode(' ', $this->formatstr)[0];
            }
            
            if ($days == 0 && (!empty($this->diffstr))) {
                return date($this->formatstr, strtotime($this->diffstr, $timestr));
            }
            return date($this->formatstr, $timestr + ($days * 24 * 3600));
    }
    
    //差天
    public function getDiffDays(){
        return floor($this->diffstr/(24*3600));
    }
    
    //差秒
    public function getDiffSeconds(){
        return $this->diffstr;
    }
    
    //闰年
    public function isLeapYear($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return (date('L', strtotime($datetimestr)) == 1);
    }
    
    //年份（yyyy)
    public function getYear($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date('Y', strtotime($datetimestr));
    }
    
    //月份
    public function getMonth($datetimestr = '', $isPrefixZero = true)
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date($isPrefixZero ? 'm' : 'n', strtotime($datetimestr));
    }
    
    //年中的月份
    public function getCurrentYearMonths(){
        $january = date('Y').'-01';
        $months = [];
        for($i=0;$i<12;$i++){
            array_push($months,date('Y-m', strtotime("+{$i} months", strtotime($january))));
        }
        return $months;
    }
    
    //月中第N天
    public function getMonthDay($datetimestr = '', $isPrefixZero = true)
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date(($isPrefixZero) ? 'd' : 'j', strtotime($datetimestr));
    }
    
    //月共N天
    public function getMonthDays($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date('t', strtotime($datetimestr));
    }
    
    //月中的日期
    public function getCurrentMonthDates(){
        $monthdayDiff = $this->getMonthDay(null,false)-1;
        $monthOneday = date('Y-m-d', strtotime("-{$monthdayDiff} days"));
        $maxdayNum = $this->getMonthDays();
        $monthdays = [];
        for($i=0;$i<$maxdayNum;$i++){
            array_push($monthdays,date('Y-m-d', strtotime("+{$i} days", strtotime($monthOneday))));
        }
        return $monthdays;
    }
    
    //本周中周一日期
    public function getCurrentMonthFirstdayDate(){
        $monthdayDiff = $this->getMonthDay(null,false)-1;
        $monthOneday = date('Y-m-d', strtotime("-{$monthdayDiff} days"));
        return $monthOneday;
    }
    
    //周中第N天
    public function getWeekDay($datetimestr = '', $isFirstMonday = true)
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        //N是（1-7），w是(0-6)
        $day = date(($isFirstMonday) ? 'N' : 'w', strtotime($datetimestr));
        return (($isFirstMonday)?$day:$day+1);
    }
    
    //周中的日期
    public function getCurrentWeekDates(){
        $weekdayDiff = $this->getWeekDay(null)-1;
        $monday = date('Y-m-d', strtotime("-{$weekdayDiff} days"));
        $weekdays = [];
        for($i=0;$i<7;$i++){
            array_push($weekdays,date('Y-m-d', strtotime("+{$i} days", strtotime($monday))));
        }
        return $weekdays;
    }
    
    //本周中周一日期
    public function getCurrentWeekFirstdayDate(){
        $weekdayDiff = $this->getWeekDay(null)-1;
        $monday = date('Y-m-d', strtotime("-{$weekdayDiff} days"));
        return $monday;
    }
    
    //年中第N天
    public function getYearDay($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date('z', strtotime($datetimestr))+1;
    }
    
    //24小时
    public function getHour($datetimestr = '', $isPrefixZero = true)
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date($isPrefixZero ? 'H' : 'G', strtotime($datetimestr));
    }
    
    //分钟
    public function getMinite($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date('i', strtotime($datetimestr));
    }
    
    //秒
    public function getSeconds($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date('s', strtotime($datetimestr));
    }
    
    //微秒
    public function getUSeconds($datetimestr = '')
    {
        if (empty($datetimestr)) $datetimestr = date($this->formatstr);
        return date('u', strtotime($datetimestr));
    }
    
    //时区
    public function getTimeArea()
    {
        return date('e');
    }
    
    //周文本
    public function getWeekDayText($weekday, $isFirstMonday = true)
    {
        $weekdays = ($isFirstMonday) ? ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'] : ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        return $weekdays[$weekday-1];
    }
    
    //+-----------------------------------------------------
    //+ 设置方法
    //+-----------------------------------------------------
    
    //设置日期差(Y-m-d)
    public function setDiffDatestr($datestr1, $datestr2){
        $this->diffstr = (strtotime($datestr1) - strtotime($datestr2));
        return $this;
    }
    
    //设置符号
    public function setSymbol($isPlus = true)
    {
        $this->diffstr = ($isPlus ? '+' : '-');
        return $this;
    }
    
    //加年
    public function setYear($years)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $years years";
        }
        return $this;
    }
    
    //加月
    public function setMonth($months)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $months months";
        }
        return $this;
    }
    
    //加周
    public function setWeek($weeks)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $weeks weeks";
        }
        return $this;
    }
    
    //加天
    public function setDay($days)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $days days";
        }
        return $this;
    }
    
    //加小时
    public function setHour($hours)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $hours hours";
        }
        return $this;
    }
    
    //加分
    public function setMinute($minutes)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $minutes minutes";
        }
        return $this;
    }
    
    //加秒
    public function setSecond($seconds)
    {
        if (self::isHasPrefix()) {
            $this->diffstr .= " $seconds seconds";
        }
        return $this;
    }
    
    //+-----------------------------------------------------
    //+ 属性方法
    //+-----------------------------------------------------
    
    //设置时间差符
    public function setDiffstr($diffstr)
    {
        $this->diffstr = $diffstr;
        return $this;
    }
    
    //设置时间格式
    public function setFormatstr($formatstr = 'Y-m-d H:i:s')
    {
        $this->formatstr = empty($formatstr) ? 'Y-m-d H:i:s' : $formatstr;
        return $this;
    }
    
    //重置设置
    public function setReset(){
        $this->setDiffstr('')->setFormatstr('Y-m-d H:i:s');
    }
    
    //+-----------------------------------------------------
    //+ 私有方法
    //+-----------------------------------------------------
    
    //前缀
    private function isHasPrefix()
    {
        return str_starts_with('+', $this->diffstr) || str_starts_with('-', $this->diffstr);
    }
}