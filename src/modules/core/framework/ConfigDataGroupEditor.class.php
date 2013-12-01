<?php

LoadIBC1Class('AbstractDataGroupEditor', 'framework');

/**
 * A tool for programmatically editing configurations.
 * 
 * @version 0.1.20130110
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
class ConfigDataGroupEditor extends AbstractDataGroupEditor {

    function __construct($group = NULL) {

        if (empty($group))
            $group = 'conf_main'; //default group
        else
            $group = 'conf_' . $group;

        // load a writer
        LoadIBC1Class('ICacheWriter', 'cache');
        LoadIBC1Class('PHPCacheWriter', 'cache.phpcache');
        $this->editor = new PHPCacheWriter("conf/$group.conf.php", $group);
    }

}
