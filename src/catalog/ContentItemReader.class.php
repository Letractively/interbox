<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.catalog
 */
class ContentItemReader extends DataItem {

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "clg");
    }

    public function Open($ID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();

        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
        $sql->JoinTable("ibc1_clg" . $this->GetServiceName() . "_catalog", "cntCatalogID=clgID");
        $sql->AddField("cntID");
        $sql->AddField("cntName");
        $sql->AddField("cntCatalogID");
        $sql->AddField("clgName AS CatalogName");
        $sql->AddField("cntAuthor");
        $sql->AddField("cntKeywords");
        $sql->AddField("DATE_FORMAT(cntTimeCreated,\"%Y-%m-%d %H:%i:%s\")", "TimeCreated");
        $sql->AddField("DATE_FORMAT(cntTimeUpdated,\"%Y-%m-%d %H:%i:%s\")", "TimeUpdated");
        $sql->AddField("DATE_FORMAT(cntTimeVisited,\"%Y-%m-%d %H:%i:%s\")", "TimeVisited");
        $sql->AddField("cntUID");
        $sql->AddField("cntVisitCount");
        $sql->AddField("cntVisitGrade");
        $sql->AddField("cntAdminGrade");
        $sql->AddField("cntPointValue");
        $sql->AddEqual("cntID", $ID);
        $r = $sql->Execute();
        $row = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($row) {
            $this->SetValue("ID", $row->cntID, IBC1_DATATYPE_INTEGER);
            $this->SetValue("Name", $row->cntName, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("CatalogID", $row->cntCatalogID, IBC1_DATATYPE_INTEGER);
            $this->SetValue("CatalogName", $row->CatalogName, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("Author", $row->cntAuthor, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("Keywords", $row->cntKeywords, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("TimeCreated", $row->TimeCreated, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("TimeUpdated", $row->TimeUpdated, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("TimeVisited", $row->TimeVisited, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("UID", $row->cntUID, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("VisitCount", $row->cntVisitCount, IBC1_DATATYPE_INTEGER);
            $this->SetValue("VisitGrade", $row->cntVisitGrade, IBC1_DATATYPE_INTEGER);
            $this->SetValue("AdminGrade", $row->cntAdminGrade, IBC1_DATATYPE_INTEGER);
            $this->SetValue("PointValue", $row->cntPointValue, IBC1_DATATYPE_INTEGER);
            return TRUE;
        }
        return FALSE;
    }

    public function GetID() {
        return $this->GetValue("ID");
    }

    public function GetName() {
        return $this->GetValue("Name");
    }

    public function GetCatalogID() {
        return $this->GetValue("CatalogID");
    }

    public function GetCatalogName() {
        return $this->GetValue("CatalogName");
    }

    public function GetAuthor() {
        return $this->GetValue("Author");
    }

    public function GetKeywords() {
        return $this->GetValue("Keywords");
    }

    public function GetTimeCreated() {
        return $this->GetValue("TimeCreated");
    }

    public function GetTimeUpdated() {
        return $this->GetValue("TimeUpdated");
    }

    public function GetTimeVisited() {
        return $this->GetValue("TimeVisited");
    }

    public function GetUID() {
        return $this->GetValue("UID");
    }

    public function GetVisitCount() {
        return $this->GetValue("VisitCount");
    }

    public function GetVisitGrade() {
        return $this->GetValue("VisitGrade");
    }

    public function GetAdminGrade() {
        return $this->GetValue("AdminGrade");
    }

    public function GetPointValue() {
        return $this->GetValue("PointValue");
    }

    public function AddVisitCount() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateUpdateSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
        $sql->AddValue("cntTimeVisited", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
        $sql->AddValue("cntVisitCount", "cntVisitCount+1", IBC1_DATATYPE_EXPRESSION);
        $sql->AddEqual("cntID", $this->GetValue("ID"));
        $r = $sql->Execute();
        $sql->CloseSTMT();
        return $r;
    }

}

?>
