<?php

/**
 * a standard interface for modifying a cache file
 * 
 * @version 0.2.20120220
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @license MIT License
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.cache
 */
interface ICacheWriter {

    /**
     * set a value to a key with an optional length of its lifetime
     * 
     * @param string $key
     * @param mixed $value
     * @param int $seconds 
     * optional, length of lifetime, in seconds
     * <ul>
     * <li>&gt;0 - update the expiry time</li>
     * <li>=0 - remain the expiry time while changing the value</li>
     * <li>&lt;0 - remove the expiry time</li>
     * </ul>
     * @param bool $withversion
     * optional, true means saving with a timestamp as a version number
     */
    public function SetValue($key, $value, $seconds = 0, $withversion = FALSE);

    /**
     * remove a key and its value
     * 
     * @param string $key 
     */
    public function Remove($key);

    /**
     * remove all keys and values
     */
    public function RemoveAll();

    /**
     * save all changes or remove the cache file if all are removed
     */
    public function Save();
}
