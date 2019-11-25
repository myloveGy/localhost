<?php

namespace lib;

class Helper
{

	/**
	 * json encode 处理中文处理
	 * 
	 * @param  mixed $mixed  转义数据
	 *
	 * @return string 返回json字符串
	 */
	public static function encode($mixed) 
	{
		return json_encode($mixed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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

       /**
     * 通过指定字符串拆分数组，然后各个元素首字母，最后拼接
     *
     * @example $strName = 'yii_user_log',$and = '_', return YiiUserLog
     * @param string $strName 字符串
     * @param string $and 拆分的字符串(默认'_')
     * @return string
     */
    public static function strToUpperWords($strName, $and = '_')
    {
        $strReturn = ucwords(str_replace($and, ' ', $strName));
        return str_replace(' ', '', $strReturn);
    }
}