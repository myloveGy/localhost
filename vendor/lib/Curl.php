<?php

namespace Lib;

/**
 * Class Curl 基础的curl 请求类
 *
 * @package Lib
 */
class Curl
{
    /**
     * @var curl
     */
    private $ch;

    /**
     * @var int 超时时间
     */
    private $timeout = 5;

    /**
     * @var bool 是否AJAX 请求
     */
    private $isAjax = false;

    /**
     * @var null
     */
    private $referer = null;

    /**
     * @var string|int|mixed 错误
     */
    private $curlError;

    /**
     * @var string|mixed 错误信息
     */
    private $errorInfo;

    /**
     * @var string|mixed 响应数据
     */
    private $body;

    /**
     * @var
     */
    private $header;

    /**
     * @var array 请求header
     */
    private $httpHeader = [];

    /**
     * @var string 请求地址
     */
    private $url;

    /**
     * @var string 请求方法
     */
    private $method;

    /**
     * @var array|mixed 请求数据
     */
    private $postData;

    /**
     * Verify SSL Cert.
     * @ignore
     */
    private $sslVerify   = false;
    private $sslCertFile = '';
    private $sslKeyFile  = '';

    /**
     * 发送请求信息
     *
     * @param string $url    请求地址
     * @param string $method 请求方法
     * @param string $data   请求数据
     *
     * @return $this
     */
    private function exec($url, $method = 'GET', $data = '')
    {
        if (!$url) {
            throw new \RuntimeException('CURL url is null:' . __FILE__);
        }

        $this->ch = curl_init();
        $this->defaultOptions($this->ch, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            $postFields = is_array($data) ? http_build_query($data) : $data;
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
        }

        if ($this->httpHeader) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpHeader);
        }

        $this->body      = curl_exec($this->ch);
        $this->curlError = curl_errno($this->ch);
        $this->header    = curl_getinfo($this->ch);
        $this->errorInfo = curl_error($this->ch);

        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }

        $this->url      = $url;
        $this->method   = $method;
        $this->postData = $data;

        return $this;
    }

    /**
     * 重试次数 $this->get()->retry(2)
     *
     * @param $num
     *
     * @return $this
     */
    public function retry($num)
    {
        if ($this->getError()) {
            while (true) {
                if (!$num) {
                    break;
                }

                $this->exec($this->url, $this->method, $this->post_data);

                if (!$this->getError()) {
                    break;
                }
                $num--;
            }
        }

        return $this;
    }

    public function setSSLFile($cert_file, $key_file)
    {

        $this->ssl_verifypeer = true;
        if (is_file($cert_file)) {
            $this->ssl_cert_file = $cert_file;
        }

        if (is_file($key_file)) {
            $this->ssl_key_file = $key_file;
        }
    }

    //网页内容抓取
    public function get($url)
    {
        return $this->exec($url);
    }

    //监控统计
    private function log($url)
    {
    }

    public function debug()
    {
        return [
            'URL'    => $this->url,
            '状态'     => $this->getStatusCode(), // http_code
            '请求耗时'   => $this->getRequestTime(), //request_time
            '错误码'    => $this->getError(), //errno
            'Header' => $this->getHeader(), //errno
        ];
    }

    // curl Post数据
    public function post($url, $data)
    {
        return $this->exec($url, 'POST', $data);
    }

    // REST DELETE
    public function delete($url)
    {
        return $this->exec($url, 'DELETE');
    }

    // REST PUT
    public function put($url, $data)
    {
        return $this->exec($url, 'PUT', $data);
    }

    public function multi($urls)
    {
        $mh = curl_multi_init();

        $conn = $contents = [];

        // 初始化  
        foreach ($urls as $i => $url) {
            $conn[$i] = curl_init($url);
            $this->defaultOptions($conn[$i], $url);
            curl_multi_add_handle($mh, $conn[$i]);
        }

        // 执行  
        do {
            curl_multi_exec($mh, $active);
        } while ($active);

        foreach ($urls as $i => $url) {
            $contents[$i] = curl_multi_getcontent($conn[$i]);
            curl_multi_remove_handle($mh, $conn[$i]);
            curl_close($conn[$i]);
        }

        // 结束清理 
        curl_multi_close($mh);
        return $contents;
    }


    private function defaultOptions(&$ch, $url)
    {
        $userAgent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';

        if ($this->referer = null) {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer); //设置 referer
        }

        curl_setopt($ch, CURLOPT_URL, $url); //设置访问的url地址
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent); //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0); //跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回结果
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if ($this->is_ajax) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "X-Prototype-Version:1.5.0"));
        }

        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        if ($this->ssl_verifypeer && $this->ssl_cert_file && $this->ssl_key_file) {
            //还原
            $this->ssl_verifypeer = false;
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //默认格式为PEM，可以注释
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->ssl_cert_file);
            //默认格式为PEM，可以注释
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->ssl_key_file);
        }
    }

    public function getError()
    {
        return $this->curl_error;
    }

    public function getErrorInfo()
    {
        return $this->curl_error;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setHeader($headers)
    {
        $this->http_header = $headers;
    }

    public function getHeader($key = null)
    {
        if ($key !== null) {
            return isset($this->header[$key]) ? $this->header : null;
        } else {
            return $this->header;
        }
    }

    public function getStatusCode()
    {
        return $this->header['http_code'];
    }

    /**
     * namelookup_time：DNS 解析域名的时间
     * connect_time：连接时间,从开始到建立TCP连接完成所用时间,包括前边DNS解析时间，如果需要单纯的得到连接时间，用这个time_connect时间减去前边time_namelookup时间。
     * pretransfer_time：从开始到准备传输的时间。
     * time_commect：client和server端建立TCP 连接的时间 里面包括DNS解析的时间
     * starttransfer_time：从client发出请求；到web的server 响应第一个字节的时间 包括前面的2个时间
     * redirect_time：重定向时间，包括到最后一次传输前的几次重定向的DNS解析，连接，预传输，传输时间。
     * total_time：总时间
     *
     * @param $time_key
     *
     * @return mixed
     */
    public function getRequestTime($time_key = 'total_time')
    {
        return $this->header[$time_key];
    }

    public function setReferer($url)
    {
        $this->referer = $url;
        return $this;
    }

    public function setAjax()
    {
        $this->is_ajax = true;
        return $this;
    }

    public function setTimeout($second = 5)
    {
        $this->timeout = $second;
        return $this;
    }

    public function setLog($is_log)
    {
        $this->is_log = $is_log;
        return $this;
    }
}