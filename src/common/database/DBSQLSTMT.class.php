<?php

/**
 * an abstract sql statement
 * @version 0.4.20110327
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
abstract class DBSQLSTMT {

    protected $errorlist;
    protected $connObj = NULL;
    protected $stmtObj = NULL;
    /**
     * sql string containing '?' where puts a parameter.
     * @var string
     */
    public $sql;
    /**
     * an array of bound result variables
     * @var array
     */
    protected $resultArr = array();
    /**
     * an array of parameters corresponding to '?' in the SQL string
     * @var array
     */
    protected $paramArr = array();
    /**
     * If result variables are not bound manully and this property is TRUE,
     * they will be generated automatically after execution.
     * @var bool
     */
    protected $autobound = TRUE;
    /**
     * TODO: paramcategorized desc
     * @var bool
     */
    protected $paramCategorized = FALSE;

    /**
     * the constructor
     * @param string $conn
     * @param string $sql
     */
    function __construct(&$conn, $sql=NULL) {
        $this->connObj = &$conn;
        $this->sql = $sql;
        $this->errorlist = new ErrorList(__CLASS__);
    }

    /**
     * add a parameter with information of type and value
     * @param string $type
     * @param mixed $var
     * @param string $category
     * TODO: category desc
     */
    public function AddParam($type, $value, $category=NULL) {
        if ($category) {
            $this->paramCategorized = TRUE;
            $this->paramArr[$category][] = array($type, $value);
        } else if (!$this->paramCategorized)
            $this->paramArr[] = array($type, $value);
        else
            throw new Exception("require a category name because parameters are categorized");
    }

    /**
     * remove all parameters
     * @param string $category
     */
    public function ClearParams($category=NULL) {
        if ($category) {
            unset($this->paramArr[$category]);
        } else {
            $this->paramArr = array();
            $this->paramCategorized = FALSE;
        }
    }

    /**
     * get the number of parameters
     * @param string $category
     */
    public function ParamCount($category=NULL) {
        if ($category) {
            return count($this->paramArr[$category]);
        } else if ($this->paramCategorized) {
            $c = 0;
            foreach ($this->paramArr as $category) {
                $c+=count($category);
            }
            return $c;
        } else {
            return count($this->paramArr);
        }
    }

    /**
     * bind a variable to a field in a select statement
     *
     * It is optional but should be involked before Execute();
     * Sequence depends on the SQL statement.
     *
     * @param mixed $var
     */
    public function BindResult(&$var) {
        if ($this->autobound && count($this->resultArr) == 0) {
            $this->autobound = FALSE;
            $this->resultArr[0] = NULL; //preserve place for the atrribute, stmtObj
        }
        $this->resultArr[] = &$var;
    }

    /**
     * remove all bound results
     */
    public function ClearBoundResults() {
        $this->resultArr = array();
        $this->autobound = TRUE;
    }

    /**
     * execute the sql statement
     * (add parameters using AddParam() before invoking this one)
     * @abstract
     * @return bool
     */
    abstract public function Execute();

    /**
     * set free all resources used in an sql execution,
     * except for the SQL string, added parameters and bound variables
     */
    abstract public function CloseSTMT();

    /**
     * fetch a row from the result
     * @param int $t
     * <table>
     * <tr><th>0</th><td>(default)return an array with field names as keys</td></tr>
     * <tr><th>1</th><td>return an object with field names as attributes</td></tr>
     * <tr><th>2</th><td>return an array with numbers as keys</td></tr>
     * <tr><th>3</th><td>return an array with both field names and numbers as keys</td></tr>
     * </table>
     * @return mixed
     * if nothing is fetched then return FALSE;<br />
     * if result is manually bound, return TRUE when succeeding;<br />
     * if it is automatically bound to a buit-in attribute, <br />
     * return an object when the parameter $t is 1 or an array when it is not 1.
     */
    public function Fetch($t=0) {
        if ($this->stmtObj == NULL)
            return FALSE;
        if (!mysqli_stmt_fetch($this->stmtObj))
            return FALSE;
        if (!$this->autobound)
            return TRUE;
        switch ($t) {
            case 1:
                $r = new stdClass;
                foreach ($this->resultArr as $key => $value) {
                    $r->$key = $value;
                }
                return $r;
            case 2:
                $r = array();
                $i = 0;
                foreach ($this->resultArr as $key => $value) {
                    $r[$i] = $value;
                    $i++;
                }
                return $r;
            case 3:
                $r = array();
                $i = 0;
                foreach ($this->resultArr as $key => $value) {
                    $r[$key] = $value;
                    $r[$i] = $value;
                    $i++;
                }
                return $r;
            default:
                return $this->resultArr;
        }
    }

    public function GetSTMT() {
        return $this->stmtObj;
    }

}

?>