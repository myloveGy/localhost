<?php

namespace app\controllers;

class IndexController
{
    public function actionIndex()
    {
    	$i = @file_get_contents('./test.log');
    	if ($i <= 0) {
    		file_put_contents('./test.log', ++$i);
    		sleep(30);
    	}
    	
        success(['key' => getRandomStr(32), 'number' => $i]);
    }

    public function actionTest()
    {
    	success([
    		'header' => $_SERVER,
    		'post' => $_POST,
    	]);
    }
}