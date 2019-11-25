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
    public function testQuery()
    {
        $db  = $this->getDB();
        $one = (new Query($db))->from('ks_admin')
            ->where('id', '=', 1)
            ->where('id', '!=', 2)
            ->where('id', 'between', [0, 2])
            ->one();
        var_dump($one, $db->getSql());
        $this->assertArrayHasKey('username', $one);
    }

    /**
     * @throws \Exception
     */
    public function testInsert()
    {
        $db  = $this->getDB();
        $one = $db->insert('ks_admin', ['username' => 'test12434', 'email' => '64456']);
        var_dump($one, $db->getSql());
        $this->assertNotEmpty($one);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
        $db  = $this->getDB();
        $one = $db->update('ks_admin', 'id = :id',
            ['username' => 'test1243455', 'email' => '6445556'],
            [':id' => 8]
        );
        var_dump($one, $db->getSql());
        $this->assertEquals(1, $one);
    }

    /**
     * @throws \Exception
     */
    public function testDelete()
    {
        $db  = $this->getDB();
        $one = $db->delete('ks_admin', 'id = :id', [':id' => 8]);
        var_dump($one, $db->getSql());
        $this->assertEquals(1, $one);
    }

    /**
     * @return DB
     */
    private function getDB()
    {
        $main = include __DIR__ . '/../config/main.php';
        return DB::getInstance(Helper::getValue($main, 'db'));
    }
}