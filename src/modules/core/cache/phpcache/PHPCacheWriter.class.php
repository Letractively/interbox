<?php

/**
 * a cache file writer implemented with php array 
 * and serialization features
 * 
 * The editor supports saving basic data types and 
 * arrays into a php cache file.
 * And it is also possible to set an expiry time for 
 * each value or even a whole group.
 * 
 * @version 0.7.20130116
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @license MIT License
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.cache.phpcache
 */
class PHPCacheWriter implements ICacheWriter {

    /**
     * group name
     * 
     * A group in this implementation is 
     * equal to a cache file. It also saves 
     * the load when reading them.
     * @see ICacheEditor::__construct()
     * @var string
     */
    private $group;

    /**
     * cache file location
     * @var string
     */
    private $cachefile;

    /**
     * group name should be the name of the cachefile
     * TODO further explanations
     * @global array $_cachedData
     * @param string $cachefile
     * @param string $groupname 
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

    public function SetValue($key, $value, $seconds = 0, $withversion = FALSE) {
        GLOBAL $_cachedData;
        //check key
        if (!preg_match('/^[a-z0-9_ ]+$/i', $key)) {
            throw new Exception('invalid key');
        }
        //assign value
        $_cachedData[$this->group]['keys'][$key]['value'] = $value;
        $cd = &$_cachedData[$this->group]['keys'][$key];
        if (is_array($value) || is_object($value)) {
            $cd['serialized'] = TRUE;
        } else if (isset($cd['serialized'])) {
            unset($cd['serialized']);
        }
        //update expiry time
        if ($seconds > 0)
            $cd['expire'] = time() + $seconds;
        else if ($seconds < 0 && isset($cd['expire']))
            unset($cd['expire']);
//        else if ($seconds == 0 && isset($cd['expire']) && $cd['expire'] < time())
//            throw new Exception('expired');
        //update version
        if ($withversion) {
            $cd['version'] = time();
        }
    }

    public function Remove($key) {
        GLOBAL $_cachedData;
        if (isset($_cachedData[$this->group]['keys'])) {
            $cd = &$_cachedData[$this->group]['keys'];
            if (isset($cd[$key]))
                unset($cd[$key]);
        }
    }

    public function RemoveAll() {
        GLOBAL $_cachedData;
        unset($_cachedData[$this->group]);
    }

    public function Save() {

        GLOBAL $_cachedData;

        if (!isset($_cachedData[$this->group])) {
            $r = @unlink($this->cachefile);
            if (!$r) {
                throw new Exception('cannot remove the cache file');
            }
            return;
        }

        $fp = fopen($this->cachefile, 'w');
        if (!$fp) {
            throw new Exception('cannot open cache file');
        }
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            throw new Exception('cannot lock cache file');
        }

        $varname = "\$_cachedData['{$this->group}']";
        $cd = &$_cachedData[$this->group];

        fwrite($fp, "<?php\n");

        if (isset($cd['expire']) && $cd['expire'] >= time()) {
            fwrite($fp, "{$varname}['expire']={$cd['expire']};\n");
        }
        if (isset($cd['keys'])) {
            foreach ($cd['keys'] as $key => &$value) {

                if (isset($value['value'])) {
                    //write the first part
                    fwrite($fp, "{$varname}['keys']['{$key}']['value']=");

                    if (isset($value['serialized'])) {
                        if (is_string($value['value'])) {
                            //already serialized, write it as a string
                            fwrite($fp, toScriptString($value['value'], TRUE) . ";\n");
                        } else {
                            //not yet, serialize it and write it as a string
                            fwrite($fp, toScriptString(serialize($value['value']), TRUE) . ";\n");
                        }
                        //mark it as serialized
                        fwrite($fp, "{$varname}['keys']['{$key}']['serialized']=TRUE;\n");
                    } else if (is_string($value['value'])) {
                        //write a string
                        fwrite($fp, toScriptString($value['value'], TRUE) . ";\n");
                    } else if (is_bool($value['value'])) {
                        //write true or false
                        fwrite($fp, ($value['value'] ? 'TRUE' : 'FALSE') . ";\n");
                    } else if (is_numeric($value['value'])) {
                        //write a number
                        fwrite($fp, $value['value'] . ";\n");
                    } else {
                        //serialize and write
                        fwrite($fp, toScriptString(serialize($value['value']), TRUE) . ";\n");
                        fwrite($fp, "{$varname}['keys']['{$key}']['serialized']=TRUE;\n");
                    }
                    if (isset($value['version'])) {
                        //append version
                        fwrite($fp, "{$varname}['keys']['{$key}']['version']={$value['version']};\n");
                    }
                    if (isset($value['expire'])) {
                        //append the expiry time
                        fwrite($fp, "{$varname}['keys']['{$key}']['expire']={$value['expire']};\n");
                    }
                }
            }
        }
        fwrite($fp, '?>');
        flock($fp, LOCK_UN);

        fclose($fp);
    }

}
