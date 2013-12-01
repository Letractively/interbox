<?php

/**
 * the main library for InterBox Core 1
 * @version 0.6
 * @author Gu Zhiji <gu_zhiji@163.com>
 * @copyright 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core
 */
/*
 * Version Regulations:
 * OVERALL:
 * ProjectVersion.ModelVersion.CodeRevision
 * CLASS:
 * ClassVersion.CodeRevision.Date
 */
/*
  function __autoload($class_name)
  {
  require_once($class_name.".php");
  }
 */

define("IBC1_DATATYPE_INTEGER", 0);
define("IBC1_DATATYPE_DECIMAL", 1);
define("IBC1_DATATYPE_PURETEXT", 2); //ensure puretext the first string-type
define("IBC1_DATATYPE_RICHTEXT", 3);
define("IBC1_DATATYPE_TEMPLATE", 4);
define("IBC1_DATATYPE_DATETIME", 5);
define("IBC1_DATATYPE_DATE", 6);
define("IBC1_DATATYPE_TIME", 7);
define("IBC1_DATATYPE_URL", 8);
define("IBC1_DATATYPE_EMAIL", 9);
define("IBC1_DATATYPE_PWD", 10);
define("IBC1_DATATYPE_WORDLIST", 11);
define("IBC1_DATATYPE_BINARY", 12);
define("IBC1_DATATYPE_EXPRESSION", 13);

define("IBC1_LOGICAL_AND", 0);
define("IBC1_LOGICAL_OR", 1);

define("IBC1_ORDER_ASC", 0);
define("IBC1_ORDER_DESC", 1);

define("IBC1_VALUEMODE_VALUEONLY", 0);
define("IBC1_VALUEMODE_TYPEONLY", 1);
define("IBC1_VALUEMODE_ALL", 2);

define("IBC1_TEMPLATETYPE_PANEL", 0);
define("IBC1_TEMPLATETYPE_LIST", 1);

define("IBC1_DEFAULT_DBSOFT", "mysqli");

function LoadIBC1File($filename, $package="") {
    $path = str_replace("\\", "/", dirname(__FILE__));
    if (substr($path, -1) != "/")
        $path.="/";
    if ($package != "")
        $path.=str_replace(".", "/", $package) . "/";
    $path.=$filename;
    require_once($path);
}

function LoadIBC1Class($classname, $package="") {
    LoadIBC1File($classname . ".class.php", $package);
}

function LoadIBC1Lib($classname, $package="") {
    LoadIBC1File($classname . ".lib.php", $package);
}

function PageRedirect($page) {
    $page = str_replace("\\", "/", $page);
    $url = $_SERVER["SCRIPT_NAME"];
    $url = substr($url, 0, strrpos($url, "/"));
    while (substr($page, 0, 3) == "../") {
        if (!strrpos($url, "/"))
            break;
        $url = substr($url, 0, strrpos($url, "/"));
        $page = substr($page, 3, strlen($page) - 3);
    }
    if ($url == "")
        $url = "/";
    if ($page != "")
        if (substr($page, 0, 1) != "/")
            $page = "/" . $page;
    $url = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $url . $page;
    header("Location: " . $url);
    exit();
}

function GetQueryString($key) {
    if (!isset($_GET[$key]))
        return "";
    return $_GET[$key];
}

function GetPostData($key) {
    if (!isset($_POST[$key]))
        return "";
    return $_POST[$key];
}

/* -------------------- */

function StrGET($name) {
    if (array_key_exists($name, $_GET))
        return $_GET[$name];
    return "";
}

function IntGET($name) {
    if (array_key_exists($name, $_GET))
        return intval($_GET[$name]);
    return 0;
}

function StrPOST($name) {
    if (array_key_exists($name, $_POST))
        return $_POST[$name];
    return "";
}

function IntPOST($name) {
    if (array_key_exists($name, $_POST))
        return intval($_POST[$name]);
    return 0;
}

function StrSESSION($name) {
    if (array_key_exists($name, $_SESSION))
        return $_SESSION[$name];
    return "";
}

function IntSESSION($name) {
    if (array_key_exists($name, $_SESSION))
        return intval($_SESSION[$name]);
    return 0;
}

/* -------------------- */

LoadIBC1Class("DataService", "common.dataservice");
LoadIBC1Class("DataFormatter", "common");
LoadIBC1Class("PropertyList", "common");
LoadIBC1Class("ItemList", "common");
LoadIBC1Class("DataList", "common.dataservice");
LoadIBC1Class("DataItem", "common.dataservice");
LoadIBC1Class("WordList", "common");
LoadIBC1Class("ErrorList", "common");
LoadIBC1Class("DBConnProvider", "common.database");
LoadIBC1Class("DBConn", "common.database");
LoadIBC1Class("DBSQLSTMT", "common.database");
LoadIBC1Class("SQLCondition", "common.database");
LoadIBC1Class("SQLFieldValList", "common.database");
LoadIBC1Class("SQLFieldExpList", "common.database");
LoadIBC1Class("SQLFieldDefList", "common.database");
LoadIBC1Class("ConditionInterface", "common.database");
LoadIBC1Class("FieldValListInterface", "common.database");
LoadIBC1Class("FieldExpListInterface", "common.database");
LoadIBC1Class("FieldDefListInterface", "common.database");
LoadIBC1Class("SQLDelete", "common.database");
LoadIBC1Class("SQLInsert", "common.database");
LoadIBC1Class("SQLSelect", "common.database");
LoadIBC1Class("SQLUpdate", "common.database");
LoadIBC1Class("SQLTable", "common.database");
?>
