<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
class ThemeListReader extends DataList {

    private $id = 0;
    private $name = "";

    //private $pl=NULL;
    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "pag");
    }

    public function SetID($id) {
        $this->id = intval($id);
        //$this->pl->SetValue("thmID",$id);
    }

    public function SetName($name) {
        $this->name = $name;
        //$this->pl->SetValue("thmName",$name,IBC1_DATATYPE_PURETEXT);
    }

    public function LoadList() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddField("COUNT(thmID) AS c");
        if ($this->id > 0)
            $sql->AddEqual("thmID", $this->id, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        if ($this->name != "")
            $sql->AddEqual("thmName", $this->name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $this->GetCounts1($sql);

        $sql->ClearFields();
        $this->AddFields($sql);
        $sql->SetLimit($this->GetPageSize(), $this->GetPageNumber());

        $sql->Execute();

        $this->Clear();
        while ($r = $sql->Fetch(1)) {
            $this->AddItem($r);
        }
        $this->GetCounts2();
        $sql->CloseSTMT();
        return TRUE;
    }

    private function AddFields(&$sql) {
        $sql->AddField("thmID", "ID");
        $sql->AddField("thmName", "Name");
        $sql->AddField("thmPreview", "Preview");
        $sql->AddField("thmSetting", "Setting");
    }

}

?>
