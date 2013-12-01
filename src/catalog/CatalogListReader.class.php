<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.catalog
 */
class CatalogListReader extends DataList {

    protected $list_sql = NULL;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        if (parent::OpenService($Conns, $ServiceName, "clg")) {
            $conn = $this->GetDBConn();
            $this->list_sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
            return TRUE;
        }
        else
            return FALSE;
    }

    public function LoadCatalog($ID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        //only support single-page list,it is a single-page list when PageSize=0
        if ($this->GetPageSize() != 0 || $this->GetPageNumber() > 1) {
            $this->GetError()->AddItem(1, "only support single-page list");
            return FALSE;
        }

        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
        $this->AddFields($sql);
        $sql->AddEqual("clgID", $ID, IBC1_DATATYPE_INTEGER);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $rr = FALSE;
        $sql->CloseSTMT();
        if (!$r) {
            $this->GetError()->AddItem(1, "not exist:$ID");
            return FALSE;
        }
        $this->AddItem($r);
        return $r;
    }

    private function AddFields(&$sql) {
        $sql->AddField("clgID", "ID");
        $sql->AddField("clgName", "Name");
        $sql->AddField("clgOrdinal", "Ordinal");
        $sql->AddField("clgUID", "UID");
        $sql->AddField("clgParentID", "ParentID");
        $sql->AddField("clgGID", "GID");
        $sql->AddField("clgAdminGrade", "AdminGrade");
    }

    public function LoadIDPath($ID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($this->GetPageSize() != 0 || $this->GetPageNumber() > 1) {
            $this->GetError()->AddItem(1, "only support single-page list");
            return FALSE;
        }
        if ($r = $this->LoadCatalog($ID))
            return FALSE;
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
        $this->AddFields($sql);

        while ($r->ParentID > 0) {
            $sql->ClearConditions();
            $sql->AddEqual("clgID", $r->ParentID);
            $sql->Execute();
            $r = $sql->Fetch(1);
            $sql->CloseSTMT();
            if (!$r) {

                return FALSE;
            }
            $this->AddItem($r);
        }
        return TRUE;
    }

    public function SetName($name, $exact=FALSE) {
        if ($this->list_sql === NULL)
            return FALSE;
        $sql = $this->list_sql;
        if ($name != "") {
            if ($exact)
                $sql->AddEqual("clgName", $name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
            else
                $sql->AddLike("clgName", $name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        }
    }

    public function SetParentCatalog($ID) {
        if ($this->list_sql === NULL)
            return FALSE;
        $sql = $this->list_sql;
        if ($ID >= 0) {
            $sql->AddEqual("clgParentID", $ID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        }
    }

    public function SetAdminGroup($gid) {
        if ($this->list_sql === NULL)
            return FALSE;
        $sql = $this->list_sql;
        if ($gid > 0) {
            $sql->AddEqual("clgAdminGID", $gid, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        }
    }

    public function SetVisitGroup($gid) {
        if ($this->list_sql === NULL)
            return FALSE;
        $sql = $this->list_sql;
        if ($gid > 0) {
            $sql->AddEqual("clgVisitGID", $gid, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        }
    }

    public function OrderBy($fieldname) {
        if ($this->list_sql === NULL)
            return FALSE;
        $sql = $this->list_sql;
        if ($order != IBC1_ORDER_ASC)
            $order = IBC1_ORDER_DESC;
        switch ($fieldname) {
            case "name":
                $sql->OrderBy("clgName", $order);
                break;
            case "ordinal":
                $sql->OrderBy("clgOrdinal", $order);
                break;

            default:
                return FALSE;
        }
        return TRUE;
    }

    public function LoadList() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($this->list_sql === NULL)
            return FALSE;
        $sql = $this->list_sql;
        $conn = $this->GetDBConn();
        $sql->ClearFields();
        $sql->AddField("COUNT(clgID)");
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

    //the following is for convenience
    public function OpenSubCatalog($ID) {
        $this->SetParentCatalog($ID);
        $this->LoadList();
    }

    public function GetAdminList($CatalogID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_admin");
        $sql->AddField("admUID");
        $sql->AddEqual("admCatalogID", $CatalogID);
        $l = new ItemList();
        $sql->Execute();
        while ($r = $sql->Fetch(1)) {
            $l->AddItem($r->admUID);
        }
        $sql->CloseSTMT();
        return $l;
    }

}

?>