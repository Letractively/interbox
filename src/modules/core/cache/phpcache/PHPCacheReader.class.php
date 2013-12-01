<?php

/**
 * a cache file reader implemented with php array 
 * and serialization features
 * 
 * @version 0.8.20120220
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @license MIT License
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.cache.phpcache
 */
class PHPCacheReader implements ICacheReader {

    /**
     * group name
     * @see PHPCacheEditor::$group
     * @var string
     */
    private $group;

    /**
     * cache file location
     * @see PHPCacheEditor::$cachefile
     * @var string
     */
    private $cachefile;

    /**
     * name the function for refreshing the cache file;
     * null if the function is not set
     * @var callback
     */
    private $function = NULL;

    /**
     * 
     * @see PHPCacheEditor::__construct()
     * @global array $_cachedData
     * @param type $cachefile
     * @param type $groupname 
     */
    function __construct($cachefile, $groupname) {
        GLOBAL $_cachedData;
        $this->group = $groupname;
        $this->cachefile = $cachefile;
        if (is_file($cachefile))
            require($cachefile);
        if (!isset($_cachedData[$groupname]))
            $_cachedData[$groupname] = array();
    }

    public function SetRefreshFunction($function) {
        $this->function = $function;
    }

    private function GetRefreshedValue($key) {
        if (!empty($this->function)) {
            //refresh
            //eval($this->function);
            //return call_user_func($this->function);
            call_user_func($this->function, $key);// TODO refresh function has to re-write cache

            //reload
            $r = new PHPCacheReader($this->cachefile, $this->group);
            return $r->GetValue($key);
        }
        return NULL;
    }

    public function GetValue($key, $version = 0) {

        GLOBAL $_cachedData;

        //check group (done in the constructor)
//        if (!isset($_cachedData[$this->group]))
//            return NULL;
        $cd = &$_cachedData[$this->group];

        //check expiry time for the group
        if (isset($cd['expire']) && $cd['expire'] < time()) {
            return $this->GetRefreshedValue($key);
        }

        //check key
        if (!isset($cd['keys']))
            return $this->GetRefreshedValue($key); //return NULL;
        if (!isset($cd['keys'][$key]))
            return $this->GetRefreshedValue($key); //return NULL;
        $cd = &$cd['keys'][$key];

        //check version
        if (isset($cd['version']) &&
                $version > 0 &&
                $version > $cd['version']) {
            return $this->GetRefreshedValue($key);
        }

        //check expiry time for the key
        if (isset($cd['expire']) && $cd['expire'] < time()) {
            return $this->GetRefreshedValue($key);
        }

        //check value
        if (isset($cd['value'])) {
            if (isset($cd['serialized']) && is_string($cd['value'])) {
                return unserialize($cd['value']);
            } else {
                return $cd['value'];
            }
        }
        return NULL;
    }

    public function GetKeys() {

        GLOBAL $_cachedData;

        //check group
        if (!isset($_cachedData[$this->group]))
            return array();
        $cd = &$_cachedData[$this->group];

        //check expiry time for the group
        if (isset($cd['expire']) && $cd['expire'] < time()) {
            return array();
        }

        //check key
        if (!isset($cd['keys']))
            return array();

        //read all keys
        $keys = array();
        foreach ($cd['keys'] as $key => $value) {
            //check expiry time for the key
            if (isset($value['expire']) && $value['expire'] < time())
                continue;
            $keys[] = $key;
        }
        return $keys;
    }

}
