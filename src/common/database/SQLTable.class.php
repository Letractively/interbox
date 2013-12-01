<?php

/**
 *
 * @version 0.7.20110316
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
class SQLTable implements FieldDefListInterface {

    public $table;
    public $mode;
    public $charset;
    protected $fieldlist;

    function __construct($m, $t="") {
        $this->fieldlist = new SQLFieldDefList();
        $this->SetMode($m);
        $this->SetTable($t);
    }

    public function SetCharset($charset, $collate) {
        $this->charset = " DEFAULT CHARACTER SET $charset COLLATE $collate";
    }

    public function SetMode($m) {
        $this->mode = strtolower($m);
        /*
          if ($m == "create") {
          $this->mode = "create";
          return TRUE;
          } else if ($m == "drop") {
          $this->mode = "drop";
          return TRUE;
          } else if ($m == "addcolumn") {
          $this->mode = "addcolumn";
          return TRUE;
          } else if ($m == "dropcolumn") {
          $this->mode = "dropcolumn";
          return TRUE;
          }
          return FALSE;
         */
    }

    public function SetTable($t) {
        $this->table = $t;
    }

    public function AddField($fieldname, $fieldtype=IBC1_DATATYPE_INTEGER, $length=1, $isnull=TRUE, $default=NULL, $ispkey=FALSE, $keyname="", $autoincrement=FALSE) {
        $this->fieldlist->AddField($fieldname, $fieldtype, $length, $isnull, $default, $ispkey, $keyname, $autoincrement);
    }

    public function ClearFields() {
        $this->fieldlist->Clear();
    }

    public function FieldCount() {
        return $this->fieldlist->Count();
    }

    public function GetSQL() {
        if ($this->mode == "create" || $this->mode == "addcolumn" || $this->mode == "dropcolumn") {
            $tc = $this->fieldlist->Count();
            if ($tc == 0)
                return NULL;
            $sql = "";
            $c = 0;
            $k = array();
            $this->fieldlist->MoveFirst();
            while (list($fieldname, $value) = $this->fieldlist->GetEach()) {
                $c++;
                if ($this->mode == "addcolumn")
                    $sql.=" ADD COLUMN ";
                else if ($this->mode == "dropcolumn")
                    $sql.=" DROP COLUMN ";
                $sql.=$fieldname . " ";
                if ($this->mode != "dropcolumn") {
                    $f = $value[0];
//fieldtype,length
                    switch ($f[0]) {
                        case IBC1_DATATYPE_INTEGER:
                            $sql.="INT";
                            if ($f[1] > 0)
                                $sql.="(" . intval($f[1]) . ")";
                            break;
                        case IBC1_DATATYPE_DECIMAL:
                            $sql.="DOUBLE";
                            break;
                        case IBC1_DATATYPE_DATETIME:
                            $sql.="TIMESTAMP(14)";
                            break;
                        case IBC1_DATATYPE_TIME:
                            $sql.="TIME";
                            break;
                        case IBC1_DATATYPE_DATE:
                            $sql.="DATE";
                            break;
                        case IBC1_DATATYPE_PURETEXT:
                            $sql.="VARCHAR";
                            if ($f[1] > 0)
                                $sql.="(" . intval($f[1]) . ")";
                            break;
                        case IBC1_DATATYPE_URL:
                        case IBC1_DATATYPE_WORDLIST:
                            $sql.="VARCHAR(256)";
                            break;
                        case IBC1_DATATYPE_EMAIL:
                        case IBC1_DATATYPE_PWD:
                            $sql.="VARCHAR(64)";
                            break;
                        case IBC1_DATATYPE_RICHTEXT:
                        case IBC1_DATATYPE_TEMPLATE:
                            $sql.="TEXT";
                            break;
                        case IBC1_DATATYPE_BINARY:
                            $sql.="MEDIUMBLOB";
                            break;
                        default:
                            return NULL;
                    }
//isnull
                    if ($f[2])
                        $sql.=" NULL";
                    else
                        $sql.=" NOT NULL";
//default value
                    if ($f[3] !== NULL) {
                        $sql.=" DEFAULT ";
                        if (is_int($f[3])) {
                            $sql.=intval($f[3]);
                        } else if (is_real($f[3])) {
                            $sql.=doubleval($f[3]);
                        } else if (is_string($f[3])) {
                            $sql.="\"" . strval($f[3]) . "\"";
                        } else if ($f[3] instanceof DataFormatter) {
                            $sql.=$f[3]->GetSQLValue();
                        }
                    }
//isprimarykey(ispkey)
                    if ($f[4])
                        $sql.=" PRIMARY KEY";
//keyname
                    if ($f[5] != "") {
                        $k[$f[5]][] = $fieldname;
                    }
//autoincrement
                    if ($f[0] == IBC1_DATATYPE_INTEGER && $f[6]) {
                        $sql.=" AUTO_INCREMENT";
                    }
                }
                if ($c < $tc)
                    $sql.=",";
            }
        }
        if ($this->mode == "create") {
            foreach ($k as $kname => $kitems) {
                $sql.=",KEY $kname (";
                foreach ($kitems as $kitem) {
                    $sql.=$kitem . ",";
                }
                $sql = substr($sql, 0, -1) . ")";
            }
            // DEFAULT CHARSET=latin1|gb2312|utf8
            //DEFAULT CHARACTER SET gb2312 COLLATE gb2312_chinese_ci
            $sql = "CREATE TABLE " . $this->table . " (" . $sql . ") TYPE=MyISAM";
            if (!is_null($this->charset))
                $sql.=$this->charset;
        } else if ($this->mode == "addcolumn" || $this->mode == "dropcolumn") {
            $sql = "ALTER TABLE " . $this->table . $sql;
        } else if ($this->mode == "drop") {
            $sql = "DROP TABLE " . $this->table;
        } else if ($this->mode == "optimize") {
            $sql = "OPTIMIZE TABLE " . $this->table;
        } else {
            return NULL;
        }
        return $sql;
    }

}

?>
