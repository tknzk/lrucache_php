<?php


require_once dirname(__FILE__) .'/../../src/lruCache.php';

class lruCacheTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $cache = new lruCache(10);
        $cache->initApc();
    }

    protected function _after()
    {
        $cache = new lruCache(10);
        $cache->initApc();
    }

    public function testStartEmpty()
    {
        $size   = 10;
        $cache  = new lruCache($size);
        $key    = 'key';

        $this->assertNull($cache->get($key));
    }

    public function testGet()
    {
        $size   = 10;
        $cache  = new lruCache($size);
        $key1   = 'key1';
        $value1 = 'value of key1';
        $cache->put($key1, $value1);

        $this->assertEquals($cache->get($key1), $value1);
    }

    public function testMultpleGet()
    {
        $size   = 10;
        $cache  = new lruCache($size);
        $key1   = 'key1';
        $value1 = 'value of key1';
        $key2   = 'key2';
        $value2 = 'value of key2';

        $cache->put($key1, $value1);
        $cache->put($key2, $value2);

        $this->assertEquals($cache->get($key1), $value1);
        $this->assertEquals($cache->get($key2), $value2);
    }

    public function testRemove()
    {
        $size   = 3;
        $cache  = new lruCache($size);

        $key1   = 'key1';
        $value1 = 'value of key1';
        $key2   = 'key2';
        $value2 = 'value of key2';
        $key3   = 'key3';
        $value3 = 'value of key3';

        $cache->put($key1, $value1);
        $cache->put($key2, $value2);
        $cache->put($key3, $value3);

        $result = $cache->remove($key2);
        $this->assertTrue($result);

        $result = $cache->get($key2);
        $this->assertNull($result);

        $this->assertEquals($cache->get($key1), $value1);
        $this->assertEquals($cache->get($key3), $value3);
    }

    public function testOverCapacity()
    {
        $size   = 10;
        $cache  = new lruCache($size);

        for ($i = 1; $i <= 15; $i++) {
            ${"key".$i}    = sprintf('key%s', $i);
            ${"value".$i}  = sprintf('value of key%s', $i);
            $cache->put(${"key".$i}, ${"value".$i});
        }

        $this->assertNull($cache->get($key1));
        $this->assertEquals($cache->get($key10), $value10);

    }

    public function testMassivePut()
    {
        $size   = 10000;
        $cache  = new lruCache($size);

        $value = 'some value';
        for ($i = 1; $i <= $size; $i++) {
            ${"key".$i}    = sprintf('key%s', $i);
            ${"value".$i}  = sprintf('value of key%s', $i);
            $cache->put(${"key".$i}, ${"value".$i});
        }

        $this->assertEquals($cache->get('key1'), $value1);
        $this->assertEquals($cache->get('key200'), $value200);

    }

    public function testEmptyRemove()
    {
        $size   = 3;
        $cache  = new lruCache($size);

        $key1   = 'key1';
        $value1 = 'value of key1';

        $result = $cache->remove($key1);
        $this->assertFalse($result);
    }

    public function testUpdateExistsKey()
    {
        $size   = 3;
        $cache  = new lruCache($size);

        $key1   = 'key1';
        $value1 = 'value of key1';
        $key2   = 'key2';
        $value2 = 'value of key2';
        $key3   = 'key3';
        $value3 = 'value of key3';

        $update = 'update value of key2';

        $cache->put($key1, $value1);
        $cache->put($key2, $value2);
        $cache->put($key3, $value3);

        $this->assertEquals($cache->get($key1), $value1);
        $this->assertEquals($cache->get($key2), $value2);
        $this->assertEquals($cache->get($key3), $value3);

        $cache->put($key2, $update);

        $this->assertEquals($cache->get($key2), $update);
        $this->assertNotEquals($cache->get($key2), $value2);
    }

    public function testLeastRecently()
    {
        $size   = 3;
        $cache  = new lruCache($size);

        $key1   = 'key1';
        $value1 = 'value of key1';
        $key2   = 'key2';
        $value2 = 'value of key2';
        $key3   = 'key3';
        $value3 = 'value of key3';
        $key4   = 'key4';
        $value4 = 'value of key4';

        $cache->put($key1, $value1);
        $cache->put($key2, $value2);
        $cache->put($key3, $value3);

        $cache->get($key1);
        $cache->get($key2);
        $cache->get($key2);
        $cache->get($key3);

        $cache->put($key4, $value4);

        $result = $cache->remove($key2);
        $this->assertTrue($result);

        $result = $cache->get($key1);
        $this->assertNull($result);

        $this->assertEquals($cache->get($key4), $value4);
        $this->assertEquals($cache->get($key3), $value3);
    }


}
