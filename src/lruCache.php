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
    const KEYMAP_KEYNAME = 'keymap';

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
     * datas
     *
     * @var array
     * @access private
     */
    private $datas;


    /**
     * __construct
     *
     * @param mixed $capacity
     * @access public
     * @return void
     */
    public function __construct($capacity)
    {
        $this->capacity = $capacity;
        $this->keys     = [];
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


    private function fetch($key)
    {
        $value = apc_fetch($key, $ret);
        if ($ret) {
            return $value;
        } else {
            return null;
        }
    }

    private function store($key, $value)
    {
        $ret = apc_store($key, $value);
        return $ret;
    }

    private function delete($key)
    {
        $ret = apc_delete($key);
        return $ret;
    }

    private function getKeyMap()
    {
        $map = apc_fetch(self::KEYMAP_KEYNAME, $ret);
        if ($ret === false) {
            $map = [];
        }
        $this->keys = $map;
    }

    private function storeKeyMap()
    {
        $map = $this->keys;
        apc_store(self::KEYMAP_KEYNAME, $this->keys);
    }

    private function check($key)
    {
        $this->getKeyMap();
        $flip_keys = array_flip($this->keys);
        if (isset($flip_keys[$key])) {
            return $flip_keys[$key];
        }
        return false;
    }

    public function initApc()
    {
        apc_clear_cache();
    }

}
