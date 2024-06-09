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

/**
 * 
 * think-queue队列中，不可使用thinkphp Log输出日志（其他插件未知）
 * 否则报错，原因未知。
 *
 * 注意：http 与 https 协议的区别
 * 
 * 虚拟请求
 * @author codeavatar
 *
 */
class CurlTool
{
    const version = '1.0.0';

    // 表单提交字符集编码
    private $postCharset = "UTF-8"; //"GBK"
    private $fileCharset = "UTF-8";
    private $curlConnectTimeout = 5;//连接超时时间（默认为0）
    private $curlTimeout = 20;//传输超时时间（默认为0）

    private $process=false;

    public function __construct($process=false)
    {
        if (!function_exists('curl_init')) {
            die('请开启php_curl插件');
        }
        $this->process = $process;
    }

    /**
     * 设置参数
     * @param null $postCharset
     * @param null $fileCharset
     * @param null $curlTimeout
     */
    public function setCurlProperty($postCharset = "UTF-8", $fileCharset = "UTF-8", $curlTimeout = 20, $curlConnectTimeout = 5)
    {
        if (!empty($postCharset)) {
            $this->postCharset = $postCharset;
        }
        if (!empty($fileCharset)) {
            $this->fileCharset = $fileCharset;
        }
        if (is_numeric($curlTimeout)) {
            $this->curlTimeout = $curlTimeout;
        }
        if (is_numeric($curlConnectTimeout)) {
            $this->curlConnectTimeout = $curlConnectTimeout;
        }
    }

    /**
     * 仅限application/json格式
     *
     * @param $url
     * @param null $postFields
     * @param string $method
     * @param bool $hasHeader
     * @return CurlEntity
     * @throws \Exception
     */
    public function curl_json($url, $postFields = null, $method = 'POST', $hasHeader = false) : CurlEntity
    {
        $ch = curl_init();
        try{
            curl_setopt($ch, CURLOPT_HEADER, $hasHeader);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $ssl = preg_match('/^https:\/\//i', $url) ? TRUE : FALSE;
            if ($ssl) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
            }
            if ('POST' == $method) {
                curl_setopt($ch, CURLOPT_URL, $url);
                $jsonFields = (is_array($postFields) ? json_encode($postFields) : $postFields);
                curl_setopt($ch, CURLOPT_POST, ('POST' == $method));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . ($jsonFields ? strlen($jsonFields) : 0)));
                // 把post的变量加上
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonFields);
            } else {
                if (isset($postFields) && is_array($postFields) && 0 < count($postFields)) {
                    $params = '';
                    foreach ($postFields as $key => $value) {
                        $params = $params . $key . '=' . $value . '&';
                    }
                    if (preg_match('/\?[\d\D]+/', $url)) {//matched ?c
                        $params = '&' . $params;
                    } else if (preg_match('/\?$/', $url)) {//matched ?$
                        $params = $params;
                    } else {
                        $params = '?' . $params;
                    }
                    $params = preg_replace('/&$/', '', $params);
                    $url = $url . $params;
                }
                curl_setopt($ch, CURLOPT_URL, $url);
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectTimeout);//连接超时时间
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);//传输超时时间
            $reponse = curl_exec($ch);

            $header = $body = '';
            $curlInfo = null;
            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch), 0);
            } else {
//            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlInfo = curl_getinfo($ch); //获取curl请求数组
                $httpStatusCode = $curlInfo['http_code'];
                if (200 !== $httpStatusCode) {
                    throw new \Exception($reponse, $httpStatusCode);
                } else {
                    if ($hasHeader) {
                        //分离头部
                        //list($header, $body) = explode("\r\n\r\n", $output, 2);
                        $datas = explode("\r\n\r\n", $reponse, 2);
                        $header = $datas[0];
                        $body = $datas[1];
                    } else {
                        $body = $reponse;
                    }
                }
            }
        } finally {
            curl_close($ch);
        }
        return new CurlEntity($header, $body, $curlInfo);
    }

    /**
     * 仅限multipart/form-data或x-www-form-urlencoded格式
     *
     * @param $url
     * @param null $postFields
     * @param string $method
     * @param bool $hasHeader
     * @return mixed
     */
    public function curl($url, $postFields = null, $method = 'POST', $hasHeader = false): CurlEntity
    {
        $ch = curl_init();

        try{
            if ('POST' === $method) {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, $hasHeader);
                curl_setopt($ch, CURLOPT_FAILONERROR, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在

                $postBodyString = "";
                $encodeArray = array();
                $postMultipart = false;

                if (is_array($postFields) && 0 < count($postFields)) {

                    foreach ($postFields as $k => $v) {
                        if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                        {
                            $postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
                            $encodeArray[$k] = $this->characet($v, $this->postCharset);
                        } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                        {
                            $postMultipart = true;
                            $encodeArray[$k] = new \CURLFile(substr($v, 1));
                        }
                    }
                    unset ($k, $v);
                    curl_setopt($ch, CURLOPT_POST, true);
                    if ($postMultipart) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
                    } else {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
                    }
                }

                if ($postMultipart) {
                    $headers = array('content-type: multipart/form-data;charset=' . $this->postCharset . ';boundary=' . $this->getMillisecond());
                } else {
                    $headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            } else {
                $p = '';
                if (is_array($postFields) && 0 < count($postFields)) {
                    foreach ($postFields as $key => $value) {
                        $p = $p . $key . '=' . $value . '&';
                    }
                    if (preg_match('/\?[\d\D]+/', $url)) {//matched ?c
                        $p = '&' . $p;
                    } else if (preg_match('/\?$/', $url)) {//matched ?$
                        $p = $p;
                    } else {
                        $p = '?' . $p;
                    }
                    $p = preg_replace('/&$/', '', $p);
                }
                $url = $url . $p;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, $hasHeader);
                curl_setopt($ch, CURLOPT_FAILONERROR, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
//            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectTimeout);//连接超时时间
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);//传输超时时间
            $reponse = curl_exec($ch);

            $header = $body = '';
            $curlInfo = null;
            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch), 0);
            } else {
//            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlInfo = curl_getinfo($ch); //获取curl请求数组
                $httpStatusCode = $curlInfo['http_code'];
                if (200 !== $httpStatusCode) {
                    throw new \Exception($reponse, $httpStatusCode);
                } else {
                    if ($hasHeader) {
                        //分离头部
                        //list($header, $body) = explode("\r\n\r\n", $output, 2);
                        $datas = explode("\r\n\r\n", $reponse, 2);
                        $header = $datas[0];
                        $body = $datas[1];
                    } else {
                        $body = $reponse;
                    }
                }
            }
        }finally{
            curl_close($ch);
        }
        return new CurlEntity($header, $body, $curlInfo);
    }

    /**
     * 输出内容
     * @param $fwCurlModel
     */
    public function curlOutput($fwCurlModel)
    {
        $fileType = $fwCurlModel->getType();

        if ($fileType == 'text/plain') {
            //出错，返回 json
            echo $fwCurlModel->getBody();
        } else {
            $type = $fwCurlModel->getFileSuffix($fileType);

            //返回 文件流
            header("Content-type: " . $fileType); //类型
            header("Accept-Ranges: bytes");//告诉客户端浏览器返回的文件大小是按照字节进行计算的
            header("Accept-Length: " . $fwCurlModel->getContentLength());//文件大小
            header("Content-Length: " . $fwCurlModel->getContentLength());//文件大小
            header('Content-Disposition: attachment; filename="' . time() . '.' . $type . '"'); //文件名
            echo $fwCurlModel->getBody();
        }
    }

    /**
     * 【内置方法】
     */

    /**
     * 内置日志输出
     *
     * @param $logs
     * @param string $tag
     * @param string $level
     */
    private function log($logs, $tag='', $level = \think\Log::WARNING){
        if($this->process){ LogTool::echolog($logs, $tag); } else { LogTool::writelog($logs, $tag,$level); }
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    private function characet($data, $targetCharset)
    {

        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
//                $data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    private function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
}