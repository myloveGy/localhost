<?php

namespace Tests;

use jinxing\framework\db\DB;
use PHPUnit\Framework\TestCase;
use jinxing\framework\db\Query;
use jinxing\framework\lib\Helper;

class TestQuery extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testOne()
    {
        $query = $this->getQuery();
        $one   = $query->from('ks_admin')
            ->select('id,username,email')
            ->where('id', '=', 1)
            ->where('id', '!=', 2)
            ->where('id', 'between', [0, 2])
            ->one();
        var_dump($one, $query->getSql());
        $this->assertArrayHasKey('username', $one);
    }

    /**
     * @throws \Exception
     */
    public function testAll()
    {
        $query = $this->getQuery();
        $one   = $query->from('ks_admin')
            ->select('id,username,email')
            ->where('id', 'in', [0, 1, 2])
            ->all();
        var_dump($one, $query->getSql());
        $this->assertNotEmpty($one);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
        $query = $this->getQuery();
        $one   = $query->from('ks_admin')
            ->where('id', '>', 2)
//            ->where(['or', ['id' => 5, 'email' => '789']])
            ->limit(2)
            ->update(['updated_at' => time()]);
        var_dump($one, $query->getSql());
        $this->assertEquals(2, $one);
    }

    /**
     * @throws \Exception
     */
    public function testDelete()
    {
        $query = $this->getQuery();
        $one   = $query->from('ks_admin')
            ->where('id', '>', 2)
            ->limit(1)
            ->delete();
        var_dump($one, $query->getSql());
        $this->assertEquals(1, $one);
    }

    /**
     * @throws \Exception
     */
    public function testCount()
    {
        $query = $this->getQuery();
        $one   = $query->from('ks_admin')
            ->where('id', '>', 2)
            ->sum('id');
        var_dump($one, $query->getSql());
//        $this->assertEquals(1, $one);
    }

    /**
     * @return Query
     */
    private function getQuery()
    {
        $main = include __DIR__ . '/../config/main.php';
        return new Query(DB::getInstance(Helper::getValue($main, 'db')));
    }
}