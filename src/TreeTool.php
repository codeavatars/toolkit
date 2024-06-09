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


class TreeTool
{
    /**
     * 一维数组生成树
     * @param $list 一维数组
     * @param string $id 数据唯一编号
     * @param string $pid 父级编号
     * @param string $son 定义子键名称
     * @return array|mixed
     */
    public static function arrayToTree($list, $id = 'id', $pid = 'pid', $son = 'sub'):array
    {
        if(empty($list)) return [];
        
        list($tree, $map) = [[], []];
        foreach ($list as $item){
            $map[$item[$id]] = $item;
        }
        foreach ($list as $item){
            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        unset($map);
        return $tree;
    }
    
    /**
     * 一维数组生成树表
     *
     * @param array $list 一维数组
     * @param string $id 数据唯一编号
     * @param string $pid 父级编号
     * @param string $path
     * @param string $ppath
     * @return array
     */
    public static function arrayToTreeTable(array $list, $id = 'id', $pid = 'pid', $path = 'path', $ppath = ''):array
    {
        $tree = [];
        foreach (self::arrayToTree($list, $id, $pid) as $attr) {
            $attr[$path] = "{$ppath}-{$attr[$id]}";
            $attr['sub'] = isset($attr['sub']) ? $attr['sub'] : [];
            $attr['spt'] = substr_count($ppath, '-');
            $attr['spl'] = str_repeat("&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;", $attr['spt']);
            $sub = $attr['sub'];
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) $tree = array_merge($tree, self::arrayToTreeTable($sub, $id, $pid, $path, $attr[$path]));
        }
        return $tree;
    }
}