<?php
require_once dirname(__FILE__) .'/../src/lruCache.php';

//$size   = 10;
//$cache  = new lruCache($size);
//
//for ($i = 1; $i <= 15; $i++) {
//    ${"key".$i}    = sprintf('key%s', $i);
//    ${"value".$i}  = sprintf('value of key%s', $i);
//    $cache->put(${"key".$i}, ${"value".$i});
//}
//var_dump($cache->keyDump());
//var_dump($cache->dataDump());
//exit;

//$this->assertNull($cache->get($key1));
//$this->assertEquals($cache->get($key10), $value10);

$size = 3;
$cache = new lruCache(3);
$cache->initApc();

$key = 'key';
//var_dump($cache->get($key));


$key1   = 'key1';
$value1 = 'value of key1';
$key2   = 'key2';
$value2 = 'value of key2';
$key3   = 'key3';
$value3 = 'value of key3';
$key4   = 'key4';
$value4 = 'value of key4';
$update = 'update of value';

$cache->put($key1, $value1);
$cache->put($key2, $value2);
$cache->put($key3, $value3);
//var_dump($cache->keyDump());
//var_dump($cache->dataDump());

$cache->get($key1);
echo "key1\n";
var_dump($cache->keyDump());

$cache->get($key2);
echo "key2\n";
var_dump($cache->keyDump());

$cache->get($key2);
echo "key2\n";
var_dump($cache->keyDump());

$cache->get($key3);
echo "key3\n";
var_dump($cache->keyDump());

$cache->put($key4, $value4);
echo "key4\n";
var_dump($cache->keyDump());
var_dump($cache->dataDump());

$result = $cache->remove($key2);

var_dump($cache->get($key4));
$cache->put($key4, $update);
var_dump($cache->get($key4));


