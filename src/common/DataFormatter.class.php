<?php

/**
 * three basic categories
 * 1.NUMBER
 * 2.STRING
 * 3.EXPRESSION
 * types of numbers
 * 1.INTEGER
 * 2.DECIMAL
 * types of strings
 * 1.datetime
 * 2.date
 * 3.time
 * 4.puretext
 * 5.richtext
 * 6.url
 * 7.email
 * 8.pwd
 * @version 0.7.20110327
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common
 */
class DataFormatter {

    private $data = NULL;
    private $type = -1;
    private $error = FALSE;
    private $isstr = FALSE;

    function __construct($data=NULL, $type=IBC1_DATATYPE_INTEGER) {
        if ($data !== NULL) {
            $this->SetData($data, $type);
        }
    }

    /**
     * set data with its designated type for a DataFormatter object
     * and validate and convert them
     * @param mixed $data
     * @param int $type
     * Code for types is declared as constants like IBC1_DATATYPE_[type name].
     * It's optional and the default type is integer (IBC1_DATATYPE_INTEGER=0).
     */
    public function SetData($data, $type=IBC1_DATATYPE_INTEGER) {
        $this->type = $type;
        $this->error = FALSE;
        switch ($type) {
            case IBC1_DATATYPE_INTEGER:
                $this->data = intval($data);
                $this->isstr = FALSE;
                break;
            case IBC1_DATATYPE_DECIMAL:
                $this->data = doubleval($data);
                $this->isstr = FALSE;
                break;
            case IBC1_DATATYPE_DATETIME:
                $this->data = date("Y-m-d H:i:s", strtotime($data));
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_DATE:
                $this->data = date("Y-m-d", strtotime($data));
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_TIME:
                $this->data = date("H:i:s", strtotime($data));
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_PURETEXT:
                $this->data = htmlspecialchars(strval($data));
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_RICHTEXT:
                //if($error)
                //$data="";
                //else
                $this->isstr = TRUE;
                $this->data = self::FormatRichText($data);
                break;
            case IBC1_DATATYPE_URL:
                $this->error = !self::ValidateURL($data);
                $this->data = $this->error ? "" : $data;
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_EMAIL:
                $this->error = !self::ValidateEMail($data);
                $this->data = $this->error ? "" : $data;
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_PWD:
                $this->error = !self::ValidatePWD($data);
                $this->data = $this->error ? "" : $data;
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_TEMPLATE:
                $this->data = str_replace("\"", "\\\"", $data);
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_WORDLIST:
                LoadIBC1Class("WordList");
                $wl = new WordList($data);
                $this->data = $wl->GetWords();
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_BINARY:
                $this->data = $data;
                $this->isstr = TRUE;
                break;
            case IBC1_DATATYPE_EXPRESSION:
                $this->data = $data;
                //$this->data=processSQLExpression($data);
                $this->isstr = FALSE;
                break;
            default:
                $this->data = NULL;
                $this->error = TRUE;
                $this->isstr = FALSE;
                $this->type = -1;
        }
    }

    /**
     * gets data or data type or both if there is no error;
     * if errors are raised when data are set, this method returns NULL.
     * @param int $m
     * IBC1_VALUEMODE_VALUEONLY=0
     * IBC1_VALUEMODE_TYPEONLY=1
     * IBC1_VALUEMODE_ALL=2
     */
    public function GetData($m=IBC1_VALUEMODE_VALUEONLY) {
        if ($this->error)
            return NULL;
        switch ($m) {
            case IBC1_VALUEMODE_VALUEONLY:
                return $this->data;
            case IBC1_VALUEMODE_TYPEONLY:
                return $this->type;
            default:
                return array($this->data, $this->type);
        }
    }

    /**
     * get data as a string value in an SQL statement
     * @param bool $vague
     * @param string $quotes
     */
    public function GetSQLValue($vague=FALSE, $quotes="\"") {
        if ($this->error)
            return NULL;
        if (!$this->isstr)
            return $this->data;
        //convert
        $t = str_replace("\"", "\\\"", $this->data);
        if ($vague)
            return "$quotes%$t%$quotes";
        return $quotes . $t . $quotes;
    }

    public function GetType() {
        return $this->type;
    }

    public function IsString() {
        return $this->isstr;
    }

    public function HasError() {
        return $this->error;
    }

    public static function ValidateURL($url) {
        return (!!eregi("^(\w+):\/\/([^/:]+)(:\d*)?([^# ]*)$", $url));
    }

    public static function ValidateEMail($email) {
        return(!!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$", $email));
    }

    public static function ValidateUID($uid) {
        ireturn(!!eregi("^[0-9a-z_]{3,256}$", $uid));
    }

    public static function ValidatePWD($pwd) {
        return (!!eregi("^[0-9a-z]{6,}$", $pwd));
    }

    public static function ValidateFieldName($fieldname) {
        return (!!eregi("^[a-z_][0-9a-z_]{0,63}$", $fieldname));
    }

    public static function ValidateTableName($tablename) {
        return (!!eregi("^[a-z_][0-9a-z_]{0,63}$", $tablename));
    }

    public static function ValidateServiceName($fieldname) {
        return (!!eregi("^[0-9a-z_]{0,32}$", $fieldname));
    }

//TODO: format rich text
    public static function FormatRichText($html) {
        $html = eregi_replace("< *script.*< */ *script *>", "", strval($html));
        $t = array();
        $p = "onclick|ondblclick|onmousemove|onmouseover|onmouseout|onmousedown|onmouseup|onload|onunload|onblur|onresize|onkeypress|onkeydown|onkeyup|ondragstart|onfocusout|onrowsdelete|onrowsinserted|onrowexit|onrowenter|oncontextmenu";
        eregi("(<[^<>]*>)", $html, $t);
        $c = count($t);
        for ($i = 1; $i < $c; $i++) {
            $tt = eregi_replace("($p) *= *\"[^\"]*\"", "", $t[$i]);
            $tt = eregi_replace("($p) *= *'[^']*'", "", $tt);
            $tt = eregi_replace("($p) *= *[^\"\'>]+ ", "", $tt);
            $tt = eregi_replace("($p) *= *[^\"\'>]+>", ">", $tt);
            $html = str_replace($t[$i], $tt, $html);
        }
        return $html;
    }

}

?>