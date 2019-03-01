<?php

use NinjaMutex\Lock\MemcacheLock;
use NinjaMutex\Mutex;

class MutexTest extends TestCase
{
    public function testMemcacheLockOK()
    {
        $this->markTestIncomplete('Missing Implementation');
        $memcache = new Memcache();
        $memcache->connect('127.0.0.1', 11211);
        $lock = new MemcacheLock();
    }
}