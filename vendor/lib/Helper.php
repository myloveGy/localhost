<?php

namespace lib;

class Helper
{
	/**
	 * @var int json默认配置 JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	 */
	const JSON_OPTIONS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

	/**
	 * json encode 处理中文处理
	 * 
	 * @param  mixed $mixed  转义数据
	 * @param  int $options 转义配置
	 *
	 * @return string 返回json字符串
	 */
	public static function encode($mixed, $options = self::JSON_OPTIONS) 
	{
		return json_encode($mixed, $options);
	}

	/**
	 * json decode 解析json字符串
	 * 
	 * @param  string $json json字符串
	 * @param  bool $assoc  默认转成数组
	 * 
	 * @return bool|array   数组或者false
	 */
	public static function decode($json, $assoc = true)
	{
		return json_decode($json, $assoc);
	}

	/**
     * 获取IP地址
     * 
     * @return string 返回字符串
     */
    public static function getIpAddress()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $strIpAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $strIpAddress = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $strIpAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $strIpAddress = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $strIpAddress = getenv('HTTP_CLIENT_IP');
            } else {
                $strIpAddress = getenv('REMOTE_ADDR') ? getenv('REMOTE_ADDR') : '';
            }
        }

        return $strIpAddress;
    }
}