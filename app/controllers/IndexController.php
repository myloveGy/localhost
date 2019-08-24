<?php

namespace app\controllers;

class IndexController
{
    public function actionIndex()
    {
        success(['key' => getRandomStr(32)]);
    }
}