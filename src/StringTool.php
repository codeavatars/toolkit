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


class StringTool extends think\helper\Str
{
    
    
    public function getImagesByText($str){
        if(empty($str)) return '';
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$str,$match);
        return $match[1];
    }
    
    /**
     * 获取安全文本（尾部省略）
     * @param $text
     * @param $percent
     * @param string $symbol
     * @return string
     */
    public static function getLastSafeText($text, $percent, $symbol = '.')
    {
        $length = intval(mb_strlen($text) * $percent);
        if($length>0){
            return mb_substr($text, 0, -$length) . $symbol;
        }
        return $text.$symbol;
    }

    /**
     * 获取安全文本（中部省略）00
     * @param $text
     * @param $startIndex
     * @param $length
     * @param string $symbol
     * @return mixed
     */
    public static function getSafeText($text, $startIndex, $length, $symbol = '*')
    {
        if ($startIndex == 0 && $length == 0) {
            if (mb_strlen($text) > 2) {
                //strlen(), substr()， str_split()该方法不支持汉字（GBK为2字节，UTF-8为3字节）
                $txts = $this->splitChinese($text);
                return implode('', array($txts[0], '**', $txts[count($txts) - 1]));
            } else if (mb_strlen($text) == 2) {
                $txts = $this->splitChinese($text);
                return implode('', array($txts[0], '**', $txts[1]));
            } else if (mb_strlen($text) < 2) {
                return $symbol . $symbol;
            }
        } else if (mb_strlen($text) > ($startIndex + $length + 1)) {
            $searchStr = mb_substr($text, $startIndex, $length);
            $replaceStr = '';
            for ($i = 0; $i < mb_strlen($searchStr); $i++) {
                $replaceStr .= $symbol;
            }
            return str_replace($searchStr, $replaceStr, $text);
        }
        return $text;
    }

    /**
     * 检测字符串是否为空
     * @param $value
     * @return bool
     */
    public static function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 字符串截取
     * @param $str
     * @param int $start
     * @param int $length
     * @param string $charset 默认utf-8编码(支持utf-8，gb2312,gbk,big5四种编码)
     * @param bool $suffix
     * @return string
     */
    public static function getSubstr($str, $start = 0, $length = 15, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
            if ($suffix & $slice != $str) return $slice . "…";
            return $slice;
        } elseif (function_exists('iconv_substr')) {
            return iconv_substr($str, $start, $length, $charset);
        }
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix && $slice != $str) return $slice . "…";
        return $slice;
    }

    /**
     * 获取随机字符串
     *
     * @param int $len 字符串长度
     * @return bool|string 默认为32位的md5字符串
     */
    public static function getRandomStr($len = 0)
    {
        if (!is_numeric($len)) {
            $len = 0; //提高容错
        }

        $rndstr = md5(microtime()) . '' . md5(round(99999999));
        if ($len != 0) {
            if ($len > 64) {
                $rndstr = '';
                $loop = ($len / 32) + 1;
                for ($i = 0; $i < $loop; $i++) {
                    $rndstr .= md5(microtime() . '' . md5(round(99999999)));
                }
            }
            return substr($rndstr, 0, $len);
        }
        return md5($rndstr);
    }

    /**
     * 读取随机数
     *
     * @param $length
     * @return int
     */
    public static function getRandomNum($len = 6)
    {
        return self::random($len,1);
    }

    //唯一标识(不含前缀17位数字)
    public static function getUniqueTimestamp($prefix='SN', $millisecond_lenght = 7)
    {
        list($millisecond,$second) = explode(" ",microtime());
        return ($prefix . ($second.substr($millisecond,2,$millisecond_lenght)));
    }

    //唯一标识(不含前缀19位数字)
    public static function getUniqueSN($prefix='')
    {
        return ($prefix . date("ymdHis", time()) . (substr(explode(" ",microtime())[0],2,7)));
    }

    //唯一标识(不含前缀32位字符)
    public static function getUniqueStr($prefix='SN'){
        return ($prefix.md5($this->getUniqueSN()));
    }

    //获取毫秒
    public static function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * 分割汉字成数组
     * @param $chinese
     * @return array[]|false|string[]
     */
    public static function splitChinese($chinese)
    {
        return preg_split('/(?<!^)(?!$)/u', $chinese);
    }
    
    /**
     * 获取随机字符串编码
     * @param integer $size 编码长度
     * @param integer $type 编码类型(1纯数字,2纯字母,3数字字母)
     * @param string $prefix 编码前缀
     * @return string
     */
    public static function random(int $size = 10, int $type = 1, string $prefix = ''): string
    {
        $numbs = '0123456789';
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        if ($type === 1) $chars = $numbs;
        if ($type === 3) $chars = "{$numbs}{$chars}";
        $code = $prefix . $chars[rand(1, strlen($chars) - 1)];
        while (strlen($code) < $size) $code .= $chars[rand(0, strlen($chars) - 1)];
        return $code;
    }
    
    /**
     * 唯一日期编码
     * @param integer $size 编码长度
     * @param string $prefix 编码前缀
     * @return string
     */
    public static function uniqidDate(int $size = 16, string $prefix = ''): string
    {
        if ($size < 14) $size = 14;
        $code = $prefix . date('Ymd') . (date('H') + date('i')) . date('s');
        while (strlen($code) < $size) $code .= rand(0, 9);
        return $code;
    }
    
    /**
     * 唯一数字编码
     * @param integer $size 编码长度
     * @param string $prefix 编码前缀
     * @return string
     */
    public static function uniqidNumber(int $size = 12, string $prefix = ''): string
    {
        $time = strval(time());
        if ($size < 10) $size = 10;
        $code = $prefix . (intval($time[0]) + intval($time[1])) . substr($time, 2) . rand(0, 9);
        while (strlen($code) < $size) $code .= rand(0, 9);
        return $code;
    }
    
    /**
     * 文本转为UTF8编码
     * @param string $content
     * @return string
     */
    public static function text2utf8(string $content): string
    {
        return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, [
            'ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5',
        ]));
    }
    
    /**
     * 数据解密处理
     * @param mixed $data 加密数据
     * @param string $skey 安全密钥
     * @return string
     */
    public static function encrypt($data, string $skey): string
    {
        $iv = static::random(16, 3);
        $value = openssl_encrypt(serialize($data), 'AES-256-CBC', $skey, 0, $iv);
        return static::enSafe64(json_encode(['iv' => $iv, 'value' => $value]));
    }
    
    /**
     * 数据加密处理
     * @param string $data 解密数据
     * @param string $skey 安全密钥
     * @return mixed
     */
    public static function decrypt(string $data, string $skey)
    {
        $attr = json_decode(static::deSafe64($data), true);
        return unserialize(openssl_decrypt($attr['value'], 'AES-256-CBC', $skey, 0, $attr['iv']));
    }
    
    /**
     * Base64Url 安全编码
     * @param string $text 待加密文本
     * @return string
     */
    public static function enSafe64(string $text): string
    {
        return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
    }
    
    /**
     * Base64Url 安全解码
     * @param string $text 待解密文本
     * @return string
     */
    public static function deSafe64(string $text): string
    {
        return base64_decode(str_pad(strtr($text, '-_', '+/'), strlen($text) % 4, '='));
    }
    
    /**
     * 压缩数据对象
     * @param mixed $data
     * @return string
     */
    public static function enzip($data): string
    {
        return static::enSafe64(gzcompress(serialize($data)));
    }
    
    /**
     * 解压数据对象
     * @param string $string
     * @return mixed
     */
    public static function dezip(string $string)
    {
        return unserialize(gzuncompress(static::deSafe64($string)));
    }
}