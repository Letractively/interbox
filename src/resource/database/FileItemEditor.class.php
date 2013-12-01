<?php

/**
 *
 * @version 0.1
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.resource.database
 */
class FileItemEditor extends DataItem {

    private $id = 0;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        parent::OpenService($Conns, $ServiceName, "res");
        $this->GetError()->SetSource(__CLASS__);
    }

    public function Open($id) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->id = $id;
    }

    public function GetID() {
        return $this->id;
    }

    public function SetName($name) {
        $this->SetValue("filName", $name, IBC1_DATATYPE_PURETEXT);
    }

    public function SaveInfo() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }

        if ($this->Count() == 0) {
            $this->GetError()->AddItem(1, "no fields have not been set");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateUpdateSTMT("ibc1_res" . $this->GetServiceName() . "_file");
        $sql->AddEqual("filID", $this->id);
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $sql->AddValue($key, $item[0], $item[1]);
        }

        $r = $sql->Execute();
        $sql->CloseSTMT();
        if (!$r) {
            return FALSE;
        }
        return TRUE;
    }

    public function Delete() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }

        $conn = $this->GetDBConn();

        $sql = $conn->CreateDeleteSTMT("ibc1_res" . $this->GetServiceName() . "_file");
        $sql->AddEqual("filID", $this->id);
        $r = $sql->Execute();
        $sql->CloseSTMT();
        if (!$r) {

            return FALSE;
        }
        return TRUE;
    }

}

?>