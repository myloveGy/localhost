<?php

namespace Tests;

use jinxing\framework\lib\Pdo;
use PHPUnit\Framework\TestCase;
use jinxing\framework\lib\Helper;

class TestPdo extends TestCase
{

    public function testUser()
    {
        $main = include __DIR__ . '/../config/main.php';
        $pdo  = Pdo::getInstance(Helper::getValue($main, 'db'));
        $one  = $pdo->update('ks_admin', [
            ['id', '>', 0],
            ['id', '<=', 2],
            ['id', '=', 1],
        ], ['updated_at' => time()]);
        var_dump($one, $pdo->getLastSql());
    }
}