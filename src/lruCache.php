<?php

/**
 * lruCache
 *
 * @package
 * @version $id$
 * @copyright
 * @author Takumi Kanzaki <info@tknzk.com>
 * @license
 */
class lruCache
{
    const KEYMAP_KEYNAME = 'lrucache_keymap';

    /**
     * capacity
     *
     * @var int
     * @access private
     */
    private $capacity;

    /**
     * keys
     *
     * @var array
     * @access private
     */
    private $keys;

    /**
     * __construct
     *
     * @param int $capacity
     * @access public
     */
    public function __construct($capacity)
    {
        if (!function_exists('apc_store') || !ini_get('apc.enabled')) {
            throw new Exception('You must have APC installed and enabled to use lruCache class.');
        }
        $this->capacity = $capacity;
    }

    /**
     * get
     *
     * @param string $key
     * @access public
     * @return mixed|string|void
     */
    public function get($key)
    {
        $this->getKeyMap();

        if ($this->check($key) !== false) {
            $ret = $this->fetch($key);
            if (is_null($ret) === false) {
                $recent = $this->check($key);
                unset($this->keys[$recent]);
                array_unshift($this->keys, $key);
                $this->storeKeyMap();
                return $ret;
            }
        }

        return null;
    }

    /**
     * put
     *
     * @param string $key
     * @param mixed $value
     * @access public
     */
    public function put($key, $value)
    {
        $this->getKeyMap();

        if ($this->capacity <= count($this->keys)) {
            // remove oldest
            $oldest = array_pop($this->keys);
            $this->delete($oldest);
        }
        // set
        $ret = $this->store($key, $value);
        if ($ret) {
            array_unshift($this->keys, $key);
            $this->storeKeyMap();
        }
    }

    /**
     * remove
     *
     * @param string $key
     * @access public
     * @return void
     */
    public function remove($key)
    {
        $this->getKeyMap();

        if ($this->check($key) !== false) {
            $remove = $this->check($key);
            $ret = $this->delete($key);
            if ($ret) {
                unset($this->keys[$remove]);
                $this->storeKeyMap();
                return true;
            }
        }
        return false;
    }

    /**
     * dataDump
     *  for debug use
     *
     * @access public
     * @return void
     */
    public function dataDump()
    {
        $this->getKeyMap();

        $datas = [];
        foreach ($this->keys as $key) {
            $datas[$key] = $this->fetch($key);
        }
        return $datas;
    }

    /**
     * keyDump
     *  for debug use
     *
     * @access public
     * @return void
     */
    public function keyDump()
    {
        $this->getKeyMap();
        return $this->keys;
    }


    /**
     * fetch
     *
     * @param string $key
     * @access private
     * @return void
     */
    private function fetch($key)
    {
        $value = apc_fetch($key, $ret);
        if ($ret) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * store
     *
     * @param string $key
     * @param mixed $value
     * @access private
     * @return void
     */
    private function store($key, $value)
    {
        $ret = apc_store($key, $value);
        return $ret;
    }

    /**
     * delete
     *
     * @param string $key
     * @access private
     * @return void
     */
    private function delete($key)
    {
        $ret = apc_delete($key);
        return $ret;
    }

    /**
     * getKeyMap
     *
     * @access private
     */
    private function getKeyMap()
    {
        $map = apc_fetch(self::KEYMAP_KEYNAME, $ret);
        if ($ret === false) {
            $map = [];
        }
        $this->keys = $map;
    }

    /**
     * storeKeyMap
     *
     * @access private
     */
    private function storeKeyMap()
    {
        $map = $this->keys;
        apc_store(self::KEYMAP_KEYNAME, $this->keys);
    }

    /**
     * check
     *
     * @param string $key
     * @access private
     * @return void
     */
    private function check($key)
    {
        $this->getKeyMap();
        $flip_keys = array_flip($this->keys);
        if (isset($flip_keys[$key])) {
            return $flip_keys[$key];
        }
        return false;
    }

    /**
     * initApc
     *
     * @access public
     */
    public function initApc()
    {
        apc_clear_cache();
    }

}
