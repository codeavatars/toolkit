<?php
/**
 * +----------------------------------------------------------------------
 * | @Author: codeavatar   @Year: 2022
 * +----------------------------------------------------------------------
 * | @Email: codeavatar@aliyun.com
 * +----------------------------------------------------------------------
 **/

namespace codeavatar\toolkit\entity;

/**
 * 
 * 多媒体文件客户端
 * 
 * @author codeavatar
 *
 */
class CurlEntity
{

    private $code = 200;
    private $msg = '';
    private $body = '';
    private $params = '';
    private $curlInfo = array();

    private $fileSuffix = array(
        "image/jpeg" => 'jpg', //+
        "text/plain" => 'text'
    );

    /*
     * @$header : 头部
     * */
    function __construct($header='', $body='', $curlInfo=null)
    {
        if (null != $curlInfo) {
            $this->curlInfo = $curlInfo;
            $this->code = $this->curlInfo['http_code'];
        }
        $this->msg = '';
        $this->params = $header;
        $this->body = $body;
    }

    /**
     *
     * @return text | bin
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     * @return text | bin
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     *
     * @return text | bin
     */
    public function getType()
    {
        $subject = $this->params;
        $pattern = '/Content\-Type:([^;]+)/';
        preg_match($pattern, $subject, $matches);
        if ($matches) {
            $type = $matches[1];
        } else {
            $type = 'application/download';
        }

        return str_replace(' ', '', $type);
    }

    /**
     *
     * @return text | bin
     */
    public function getContentLength()
    {
        $subject = $this->params;
        $pattern = '/Content-Length:\s*([^\n]+)/';
        preg_match($pattern, $subject, $matches);
        return (int)(isset($matches[1]) ? $matches[1] : '');
    }


    public function getFileSuffix($fileType)
    {
        $type = isset($this->fileSuffix[$fileType]) ? $this->fileSuffix[$fileType] : 'text/plain';
        if (!$type) {
            $type = 'json';
        }
        return $type;
    }

    /**
     *
     * @return text | bin
     */
    public function getBody()
    {
        //header('Content-type: image/jpeg');
        return $this->body;
    }

    /**
     * 获取参数
     * @return text | bin
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getCurlInfo()
    {
        /*
        以下为键名及描述
        url:网络地址。
        content_type:内容编码。
        http_code:HTTP状态码。
        header_size:header的大小。
        request_size:请求的大小。
        filetime:文件创建的时间。
        ssl_verify_result:SSL验证结果。
        redirect_count:跳转计数。
        total_time:总耗时。
        namelookup_time:DNS查询耗时。
        connect_time:等待连接耗时。
        pretransfer_time:传输前准备耗时。
        size_uplpad:上传数据的大小。
        size_download:下载数据的大小。
        speed_download:下载速度。
        speed_upload:上传速度。
        download_content_length:下载内容的长度。
        upload_content_length:上传内容的长度。
        starttransfer_time:开始传输的时间表。
        redirect_time:重定向耗时。
         */
        return $this->curlInfo;
    }

    public function printCurlInfo()
    {
        if (is_array($this->curlInfo)) {
            echo '++++++++++++++++++++++++++++++++++++++++++++<br/>';
            foreach ($this->curlInfo as $k => $v) {
                printf('|  %s  => %s  |<br/>', $k, (is_array($v) ? implode(',', $v) : $v));
            }
            echo '++++++++++++++++++++++++++++++++++++++++++++';
        }
    }

}
