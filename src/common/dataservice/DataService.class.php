<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.dataservice
 */
abstract class DataService {

    private $_DBConns = NULL;
    private $_ErrorList = NULL;
    private $_ServiceName = "";
    private $_IsSOpen = FALSE;

    function __construct(ErrorList &$EL=NULL) {
        if ($EL == NULL)
            $this->_ErrorList = new ErrorList();
        else
            $this->_ErrorList = &$EL;
    }

    public function OpenService(DBConnProvider &$Conns, $ServiceName, $ServiceType="") {
        if ($this->_DBConns != NULL)
            $this->CloseService();
        $this->_DBConns = &$Conns;

        $conn = &$this->GetDBConn();
        $sql = &$conn->CreateSelectSTMT("ibc1_dataservice");
        $sql->AddField("ServiceName");
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        if ($ServiceType != "") {
            $sql->AddEqual("ServiceType", $ServiceType, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        }
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            $this->_ServiceName = $ServiceName;
            $this->_IsSOpen = TRUE;
            return TRUE;
        }
        $this->_ServiceName = "";
        $this->_IsSOpen = FALSE;
        return FALSE;
    }

    public function CloseService() {
        /*
          if($this->_DBConns!=NULL)
          $this->_DBConns->CloseAll();
         */
        $this->_ServiceName = "";
        $this->_DBConns = NULL;
        $this->_IsSOpen = FALSE;
        //$this->_ErrorList=NULL;
    }

    public function IsServiceOpen() {
        return $this->_IsSOpen;
    }

    public function GetServiceName() {
        return $this->_ServiceName;
    }

    public function GetDBConns() {
        return $this->_DBConns;
    }

    public function GetDBConn() {
        return $this->_DBConns->GetConn();
    }

    public function GetError() {
        return $this->_ErrorList;
    }

}

?>
