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
        $this->datas    = [];
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
        if (isset($this->datas[$key])) {

            // change access key map
            $recent = array_search($key, $this->keys);
            unset($this->keys[$recent]);
            array_unshift($this->keys, $key);

            // return value
            return $this->datas[$key];

        } else {
            return null;
        }
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
        if ($this->capacity <= count($this->keys)) {
            // remove oldest
            $oldest = array_pop($this->keys);
            unset($this->datas[$oldest]);
        }
        // set
        $this->datas[$key] = $value;
        array_push($this->keys, $key);
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
        if (isset($this->datas[$key])) {
            unset($this->datas[$key]);
            $remove = array_search($key, $this->keys);
            unset($this->keys[$remove]);
            return true;
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
        return $this->datas;
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
        return $this->keys;
    }

}
