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


class FileTool
{
    //删除文件
    public static function delfile($file_path){
        if(is_file($file_path)){
            @unlink($file_path);
        }
    }
    
    //删除目录
    public static function deldir($tmp_path){
        if(is_dir($tmp_path)){
            if ($handle = opendir($tmp_path)) {
                while (($fdir = readdir($handle)) !== false) {
                    if($fdir != "." && $fdir != ".."){
                        $sub_path = $tmp_path.DIRECTORY_SEPARATOR.$fdir;
                        if(is_dir($sub_path)){
                            static::deldir($sub_path);
                            @rmdir($sub_path);
                        }else{
                            @unlink($sub_path);
                        }
                    }
                }
                closedir($handle);
            }
            @rmdir($tmp_path);
        }
    }
    
    /**
     * 格式化文件大小
     * @param $size 单位bit
     * @return string
     */
    public static function formatBytes($size): string
    {
        if (is_numeric($size)) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
            return round($size, 2) . ' ' . $units[$i];
        } else {
            return $size;
        }
    }
    
    /**
     * 获取文件绝对路径
     * @param string $name 文件路径
     * @param ?string $root 程序根路径
     * @return string
     */
    public static function syspath(string $name = '', ?string $root = null): string
    {
        if (is_null($root)) $root = app()->getRootPath();
        $attr = ['/' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR];
        return rtrim($root, '\\/') . DIRECTORY_SEPARATOR . ltrim(strtr($name, $attr), '\\/');
    }
}