<?php
/**
 * a list for configurations
 * @version 0.1
 * @author Gu Zhiji <gu_zhiji@163.com>
 * @copyright 2010-2011 InterBox Core 1.3, GuZhiji Studio
 * @package interbox.core1.common
 */
class ConfigInfo extends PropertyList {

    function __construct($c="") {
        if ($c != "")
            $this->ReadConfig($c);
    }

    public function ReadConfig($c) {
        $p = strpos($c, ":");
        while ($p > -1) {
            $name = substr($c, 0, $p);
            $c = substr($c, $p + 1);
            $p = strpos($c, "|");
            if ($p == -1)
                break;
            $type = intval(substr($c, 0, $p));
            $c = substr($c, $p + 1);
            $p = strpos($c, ";");
            if ($p > -1) {
                $value = substr($c, 0, $p);
                $c = substr($c, $p + 1);
                $p = strpos($c, ":");
            } else {
                $value = $c;
                $p = -1;
            }
            $this->SetValue($name, $value, $type);
        }
    }

    public function GetConfig() {
        $r = "";
        while (list($name, $value) = $this->GetEach()) {
            $r.=$name . ":" . $value[0] . "|" . $value[1] . ";";
        }
        return $r;
    }

    public function SetValue($key, $value, $type) {
        if (strpos($key, ":") > -1)
            break;
        if (strpos($key, ";") > -1)
            break;
        if (!is_int($type))
            break;
        parent::SetValue($key, $value, $type);
    }

}

?>
