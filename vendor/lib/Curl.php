<?php

namespace Lib;

/**
 * Class Curl 基础的curl 请求类
 *
 * @method int|mixed getError() 获取错误编号
 * @method null|string getErrorInfo() 获取错误信息
 * @method null|mixed getBody() 获取请求结果
 * @method Curl setTimeout($time) 设置超时时间
 * @method Curl setIsAjax($isAjax) 设置是否为ajax请求
 * @method Curl setIsJson($isJson) 设置是否为json请求(header 添加json, 请求数组会转json)
 * @method Curl setReferer($referer) 在HTTP请求头中"Referer: "的内容
 * @package Lib
 */
class Curl
{
    /**
     * @var array 请求header
     */
    private $header = [];

    /**
     * @var int 超时时间
     */
    private $timeout = 5;

    /**
     * @var bool 是否AJAX 请求
     */
    private $isAjax = false;

    /**
     * @var bool 是否 json 请求
     */
    private $isJson = false;

    /**
     * @var null
     */
    private $referer = null;

    /**
     * @var bool 开启ssl
     */
    private $sslVerify = false;

    /**
     * @var string ssl cert 文件地址
     */
    private $sslCertFile = '';

    /**
     * @var string ssl key 文件地址
     */
    private $sslKeyFile = '';

    /**
     * @var curl
     */
    private $ch;

    /**
     * @var string|int|mixed 错误编号
     */
    private $error;

    /**
     * @var string|mixed 错误信息
     */
    private $errorInfo;

    /**
     * @var string|mixed 响应数据
     */
    private $body;

    /**
     * @var curl 句柄信息
     */
    private $info;

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
    private $requestData;

    /**
     * @var array 默认配置项
     */
    private $options = [
        CURLOPT_USERAGENT      => 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)',   // 用户访问代理 User-Agent
        CURLOPT_HEADER         => 0,
        CURLOPT_FOLLOWLOCATION => 0, // 跟踪301
        CURLOPT_RETURNTRANSFER => 1, // 返回结果
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, // 默认使用IPV4
    ];

    /**
     * @var array curl 额外参数，优先级最高
     */
    private $curlOptions = [];

    /**
     * 允许在初始化的时候设置属性信息
     *
     * Curl constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $attribute => $value) {
            // 特殊属性不允许设置
            if (in_array($attribute, [
                'ch', 'error', 'errorInfo',      // curl 相关
                'body', 'info',                  // 响应相关
                'url', 'method', 'requestData',  // 请求相关
            ])) {
                continue;
            }

            // 存在的属性，设置
            if (isset($this->$attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    /**
     * 发送get 请求
     *
     * @param string $url    请求地址
     * @param array  $params 请求参数
     *
     * @return $this
     */
    public function get($url, $params = [])
    {
        // 拼接请求参数
        if (!empty($params)) {
            $params = is_array($params) ? http_build_query($params) : $params;
            $url    .= $params;
        }

        return $this->exec($url);
    }

    /**
     * 发送post 请求
     *
     * @param string       $url  请求地址
     * @param array|string $data 请求数据
     *
     * @return $this
     */
    public function post($url, $data)
    {
        return $this->exec($url, 'POST', $data);
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $url 请求地址
     *
     * @return $this
     */
    public function delete($url)
    {
        return $this->exec($url, 'DELETE');
    }

    /**
     * 发送PUT 请求
     *
     * @param string $url  请求地址
     * @param array  $data 请求数据
     *
     * @return $this
     */
    public function put($url, $data)
    {
        return $this->exec($url, 'PUT', $data);
    }

    /**
     * 重试次数 $this->get()->retry(2)
     *
     * @param integer $num 重试次数
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

                // 重新发送请求
                $this->exec($this->url, $this->method, $this->requestData);

                if (!$this->getError()) {
                    break;
                }

                $num--;
            }
        }

        return $this;
    }

    public function multi($urls)
    {
        $mh   = curl_multi_init();
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

    /**
     * 设置请求头信息
     *
     * @param array $headers 设置的信息
     *
     * @return Curl
     */
    public function setHeader(array $headers)
    {
        $this->header += $headers;
        return $this;
    }

    /**
     * 设置选项
     *
     * @param string|array $options 设置项
     * @param null|mixed   $value
     *
     * @return Curl
     */
    public function setOptions($options, $value = null)
    {
        if (!is_array($options)) {
            $options = [$options => $value];
        }

        // 设置选项
        foreach ($options as $option => $value) {
            $this->curlOptions[$option] = $value;
        }

        return $this;
    }

    /**
     * 设置SSL文件
     *
     * @param string $certFile 证书文件
     * @param string $keyFile  秘钥文件
     *
     * @return $this
     */
    public function setSSLFile($certFile, $keyFile)
    {
        $this->sslVerify = true;
        if (is_file($certFile)) {
            $this->sslCertFile = $certFile;
        }

        if (is_file($keyFile)) {
            $this->sslKeyFile = $keyFile;
        }

        return $this;
    }

    /**
     * 获取curl info 信息
     *
     * @param null $key 获取的字段信息
     *
     * @return mixed|null
     */
    public function getInfo($key = null)
    {
        if ($key !== null) {
            return isset($this->info[$key]) ? $this->info : null;
        }

        return $this->info;
    }

    /**
     * 获取状态码
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->getInfo('http_code');
    }

    /**
     * 获取请求时间
     *
     * @param string $timeKey
     *
     * @return mixed
     */
    public function getRequestTime($timeKey = 'total_time')
    {
        return $this->getInfo($timeKey);
    }

    /**
     * 运行方法
     *
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        // 指定设置方法
        if (in_array($name, ['setIsAjax', 'setTimeout', 'setReferer', 'setIsJson'], true)) {
            $attribute        = ucfirst(ltrim($name, 'set'));
            $this->$attribute = $arguments[0];
            return $this;
        } else if (in_array($name, ['getError', 'getErrorInfo', 'getBody'])) {
            // 获取指定数据信息
            $attribute = ucfirst(ltrim($name, 'get'));
            return $this->$attribute;
        }

        throw new \RuntimeException('Curl not has method: ' . $name);
    }

    /**
     * 设置默认选项
     *
     * @param resource $ch  设置的curl
     * @param string   $url 请求地址
     */
    private function defaultOptions(&$ch, $url)
    {
        // 设置 referer
        if ($this->referer) {
            $this->options[CURLOPT_REFERER] = $this->referer;
        }

        $this->options[CURLOPT_URL]     = $url;           // 设置访问的url地址
        $this->options[CURLOPT_TIMEOUT] = $this->timeout; // 设置超时

        // Https 关闭 ssl 验证
        if (substr($url, 0, 5) == 'https') {
            $this->options[CURLOPT_SSL_VERIFYPEER] = false;
            $this->options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        // 设置ajax
        if ($this->isAjax) {
            $this->header += ['X-Requested-With: XMLHttpRequest', 'X-Prototype-Version:1.5.0'];
        }

        // 设置 json 请求
        if ($this->isJson) {
            $this->header += ['Content-Type: application/json'];
        }

        // 设置证书 使用证书：cert 与 key 分别属于两个.pem文件
        if ($this->sslVerify && $this->sslCertFile && $this->sslKeyFile) {
            // 还原
            $this->sslVerify = false;

            // 默认格式为PEM，可以注释
            $this->options[CURLOPT_SSLCERTTYPE] = 'PEM';
            $this->options[CURLOPT_SSLCERT]     = $this->sslCertFile;

            // 默认格式为PEM，可以注释
            $this->options[CURLOPT_SSLKEYTYPE] = 'PEM';
            $this->options[CURLOPT_SSLKEY]     = $this->sslKeyFile;
        }

        // 设置HTTP header 信息
        if ($this->header) {
            $this->options[CURLOPT_HTTPHEADER] = $this->header;
        }

        // 一次性设置
        curl_setopt_array($ch, $this->options);
    }

    /**
     * 发送请求信息
     *
     * @param string       $url    请求地址
     * @param string       $method 请求方法
     * @param string|array $data   请求数据
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

        // 存在数据
        if ($data) {
            // 数组的话、需要转为字符串
            if (is_array($data)) {
                $postFields = $this->isJson ? json_encode($data, 320) : http_build_query($data);
            } else {
                $postFields = $data;
            }

            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
        }

        // 存在curlOptions
        if ($this->curlOptions) {
            curl_setopt_array($this->ch, $this->curlOptions);
        }

        // 赋值
        $this->url         = $url;
        $this->method      = $method;
        $this->requestData = $data;
        $this->body        = curl_exec($this->ch);
        $this->error       = curl_errno($this->ch);
        $this->info        = curl_getinfo($this->ch);
        $this->errorInfo   = curl_error($this->ch);

        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }

        return $this;
    }
}