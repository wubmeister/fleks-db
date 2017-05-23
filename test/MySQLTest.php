<?php

use PHPUnit\Framework\TestCase;

use Fleks\Db\MySQL;

class MySQLTest extends TestCase
{
    protected $db;

    protected function setUp()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
    }

    public function testPdo() {
        $pdo = $this->db->getPdo();
        $this->assertInstanceOf('PDO', $pdo);
    }

    public function testQuoteIdentifier()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');

        $this->assertEquals('`test`', $this->db->quoteIdentifier('test'));
        $this->assertEquals('`foo`.`bar`', $this->db->quoteIdentifier('foo.bar'));
        $this->assertEquals('`foo`.*', $this->db->quoteIdentifier('foo.*'));
    }

    public function testPrepare()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');

        $stmt = $this->db->prepare('SELECT * FROM `tests` WHERE id = :id', [ ':id' => 1 ]);
        $this->assertInstanceOf('PDOStatement', $stmt);

        $result = $stmt->execute();
        $this->assertTrue($result);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertInternalType('array', $row);
        $this->assertEquals(1, $row['id']);
        $this->assertEquals('First test', $row['name']);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertFalse($row);

    }

    /**
     * @expectedException Exception
     */
    public function testExecuteThrowsException()
    {
        // $this->expectException('Exception');
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');
        $stmt = $this->db->execute('SELECT * FROM `tests` WHERE GROUP', [ ':id' => 1 ]);
    }

    public function testExecute()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');

        $stmt = $this->db->execute('SELECT * FROM `tests` WHERE id = :id', [ ':id' => 1 ]);
        $this->assertInstanceOf('PDOStatement', $stmt);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertInternalType('array', $row);
        $this->assertEquals(1, $row['id']);
        $this->assertEquals('First test', $row['name']);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertFalse($row);
    }

    public function testFetchAll()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');

        $result = $this->db->fetchAll('SELECT * FROM `tests`');
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('First test', $result[0]['name']);
    }

    public function testFetchRow()
    {
        $this->db = new MySQL([ 'dbname' => 'framewub_test' ], 'framewub', 'fr4m3wu8');

        $result = $this->db->fetchRow('SELECT * FROM `tests` WHERE id = :id', [ ':id' => 1 ]);
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('First test', $result['name']);
    }
}
