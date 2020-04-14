<?php

namespace app\controllers;

class IndexController
{
	public function actionIndex()
	{
		$number = intval(getValue($_GET, 'number', 1)) - 1;
		$i = @file_get_contents('./test.log');
		if ($i <= $number) {
			file_put_contents('./test.log', ++$i);
			sleep(15);
		}

		success([
			'key'    => getRandomStr(32),
			'retry'  => isset($i) ? $i : 1,
			'number' => $number,
			'method' => getValue($_SERVER, 'REQUEST_METHOD')
		]);
	}

	public function actionTest()
	{
		success([
			'header' => $_SERVER,
			'post' => $_POST,
		]);
	}

	public function actionXml()
	{
		exit(static::arrayToXml([
			'name' => 'jinxing.liu',
			'date' => date('Y-m-d H:i:s') 
		]));
	}

	/**
     * array转xml
     *
     * @param array $arr
     *
     * @return string
     */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<{$key}>{$val}</{$key}>";
            } else {
                $xml .= "<{$key}><![CDATA[{$val}]]></{$key}>";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}
