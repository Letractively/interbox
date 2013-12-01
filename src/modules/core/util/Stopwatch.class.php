<?php

/**
 * a simple stopwatch for timing
 * 
 * @version 0.2.20110111
 * @author Gu Zhiji <gu_zhiji@163.com>
 * @copyright 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.util
 */
class Stopwatch {

    private $starttime;

    function __construct() {
        $this->starttime = self::GetSec();
    }

    private static function GetSec() {
        $mtime = explode(" ", microtime());
        return floatval($mtime[1]) + floatval($mtime[0]);
    }

    public function elapsedMillis() {
        $now = self::GetSec();
        return round(($now - $this->starttime) * 1000, 5);
    }

    public function elapsedSecs() {
        $now = self::GetSec();
        return intval($now - $this->starttime);
    }

    public function reset() {
        $this->starttime = self::GetSec();
    }

}
