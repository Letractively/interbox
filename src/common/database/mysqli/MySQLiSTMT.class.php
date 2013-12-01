<?php

/**
 * a sql statement for MySQL via mysqli
 * @version 0.4.20110327
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database.mysqli
 */
class MySQLiSTMT extends DBSQLSTMT {

    public function AddParam($type, $value, $category=NULL) {
        if (is_int($type)) {
            switch (intval($type)) {
                case IBC1_DATATYPE_INTEGER:
                    $type = "i";
                    break;
                case IBC1_DATATYPE_DECIMAL:
                    $type = "d";
                    break;
                case IBC1_DATATYPE_BINARY:
                    $type = "b";
                    break;
                default:
                    $type = "s";
            }
        }
        parent::AddParam($type, $value, $category);
    }

    protected function _prepareSTMT() {
        $stmt = mysqli_prepare($this->connObj, $this->sql);
        if (!$stmt) {
            throw new Exception("unexpected SQL error:$this->sql<br />" . mysqli_error($this->connObj), 4);
            //return FALSE;
        }
        $this->stmtObj = &$stmt;
        //return TRUE;
    }

    protected function _bindParams() {
        //TODO: see if the check is necessary
        if ($this->ParamCount() == 0)
            return;
        $types = "";
        $values[0] = &$this->stmtObj;
        $values[1] = NULL; //preserve place for $types
        if ($this->paramCategorized) {
            //TODO: categories need to be in sequence
            foreach ($this->paramArr as $category) {
                foreach ($category as $value) {
                    $types.=$value[0];
                    $values[] = &$value[1];
                }
            }
        } else {
            foreach ($this->paramArr as $value) {
                $types.=$value[0];
                $values[] = &$value[1];
            }
        }
        $values[1] = $types;
        if (!call_user_func_array("mysqli_stmt_bind_param", $values)) {
            //TODO: var_dump($this->paramArr);
            var_dump($this->paramArr);
            throw new Exception("SQL error:[" . mysqli_stmt_param_count($this->stmtObj) . "," . count($this->paramArr) . "]$this->sql<br />" . mysqli_stmt_error($this->stmtObj), 4);
        }
    }

    protected function _bindResults() {
        if ($this->autobound) {
            $p = array();
            $data = mysqli_stmt_result_metadata($this->stmtObj);
            if ($data) {
                $p[0] = &$this->stmtObj;
                while ($field = mysqli_fetch_field($data)) {
                    $p[] = &$this->resultArr[$field->name];
                }
                if (!call_user_func_array("mysqli_stmt_bind_result", $p)) {
                    throw new Exception("binding error:" . mysqli_stmt_error($this->stmtObj), 4);
                }
            }
        } else {
            $this->resultArr[0] = &$this->stmtObj;
            if (!call_user_func_array("mysqli_stmt_bind_result", $this->resultArr)) {
                throw new Exception(4, "binding error:" . mysqli_stmt_error($this->stmtObj), 4);
            }
        }
    }

    public function Execute() {
        if (!$this->connObj) {
            throw new Exception("database unconnected", 4);
        }
        //if (!$this->_prepareSTMT()) return FALSE;
        $this->_prepareSTMT();
        //if (!$this->_bindParams()) return FALSE;
        $this->_bindParams();
        if (!mysqli_stmt_execute($this->stmtObj)) {
            throw new Exception(mysqli_stmt_error($this->stmtObj), 4);
        }
        $this->_bindResults();
    }

    public function CloseSTMT() {
        @mysqli_stmt_free_result($this->stmtObj);
        @mysqli_stmt_close($this->stmtObj);
        $this->stmtObj = NULL;
    }

}

?>
