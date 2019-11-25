<?php

namespace app\controllers;

class IndexController
{
    public function actionIndex()
    {
        success(['key' => getRandomStr(32)]);
    }

    public function actionTest()
    {
    	success([
    		'header' => $_SERVER,
    		'post' => $_POST,
    	]);
    }
}