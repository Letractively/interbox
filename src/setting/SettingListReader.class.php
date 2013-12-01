<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.setting
 */
class SettingListReader extends DataList {

    private $_MatchValue;
    private $_Name = "";
    private $_NameExact = TRUE;
    private $_MatchService = "";
    private $_MatchTable = "";
    private $_MatchField = "";
    private $_MatchType = -1;
    private $_ValueType = -1;
    private $_ValueLength = 0;
    private $_TimeIncluded = FALSE;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "set");

        $c = $this->GetDBConn();
        $sql = $c->CreateSelectSTMT("ibc1_dataservice_setting");
        $sql->AddField("*");
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);

        $sql->Execute();
        if ($r = $sql->Fetch(1)) {
            $this->_MatchService = $r->MatchService;
            $this->_MatchTable = $r->MatchTable;
            $this->_MatchField = $r->MatchField;
            $this->_MatchType = $r->MatchType;
            $this->_ValueType = $r->ValueType;
            $this->_ValueLength = $r->ValueLength;
            if ($r->TimeIncluded == 0)
                $this->_TimeIncluded = FALSE;
            else
                $this->_TimeIncluded = TRUE;
        }
        $sql->CloseSTMT();
    }

    public function CloseService() {
        parent::CloseService();
        $this->_MatchService = "";
        $this->_MatchTable = "";
        $this->_MatchField = "";
        $this->_MatchType = -1;
        $this->_ValueType = -1;
        $this->_ValueLength = 0;
        $this->_TimeIncluded = FALSE;
    }

    public function SetMatchValue($m) {
        $this->_MatchValue = $m;
    }

    public function SetSettingName($name, $exact=TRUE) {
        $this->_Name = $name;
        $this->_NameExact = $exact;
    }

    public function LoadList() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
        if ($this->_MatchValue != "")
            $sql->AddEqual("setMatchValue", $this->_MatchValue, $this->_MatchType, IBC1_LOGICAL_AND);
        if ($this->_Name != "") {
            if ($this->_NameExact)
                $sql->AddEqual("setName", $this->_Name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
            else
                $sql->AddLike("setName", $this->_Name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        }

        $sql->AddField("COUNT(setID)");

        $this->GetCounts1($sql);

        $sql->ClearFields();
        $sql->AddField("*");
        $sql->SetLimit($this->GetPageSize(), $this->GetPageNumber());

        $sql->Execute();

        $this->Clear();

        while ($r = $sql->Fetch(1)) {
            $this->AddItem($r);
        }

        $this->GetCounts2();
        $sql->CloseSTMT();
    }

}

?>