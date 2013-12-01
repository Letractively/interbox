<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.catalog
 */
class CatalogItemEditor extends DataItem {

    protected $IsNew = TRUE;
    protected $ID = 0;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "clg");
    }

    public function Create() {
        $this->IsNew = TRUE;
        $this->ID = 0;
    }

    public function Open($ID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->IsNew = TRUE;
        $this->ID = intval($ID);
    }

    public function Save($ParentID=-1) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $essential = 2;

        if ($this->IsNew) {//create a new catalog
            $ParentID = intval($ParentID);
            if ($ParentID < 0) {
                $this->GetError()->AddItem(1, "parent catalog is not set");
                return FALSE;
            }
            $conn = $this->GetDBConn();
            $this->SetValue("clgParentID", $ParentID);
            if ($ParentID > 0) {//if the catalog is not at the top level
                if (!$this->checkCatalog($ParentID, $conn)) {
                    $this->GetError()->AddItem(1, "no access");
                    return FALSE;
                }
            }
            if ($this->Count() < $essential) {//if there is not enough information
                $this->GetError()->AddItem(1, "some fields have not been set");
                return FALSE;
            }

            $sql = $conn->CreateInsertSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
            $this->MoveFirst();
            while (list($key, $item) = $this->GetEach()) {
                $sql->AddValue($key, $item[0], $item[1]);
            }
            $r = $sql->Execute();
            if ($r == FALSE) {
                $sql->CloseSTMT();
                $this->GetError()->AddItem(1, "fail to execute");
                return FALSE;
            }
            $this->ID = $sql->GetLastInsertID();
            $sql->CloseSTMT();
            return TRUE;
        } else if ($this->ID > 0) {//modify a catalog
            /*
              check
              $this->SetName($this->Name);
             */
            if ($this->Count() == 0) {
                $this->GetError()->AddItem(1, "no fields have not been set");
                return FALSE;
            }
            $conn = $this->GetDBConn();
            $sql = $conn->CreateUpdateSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
            $this->MoveFirst();
            while (list($key, $item) = $this->GetEach()) {
                $sql->AddValue($key, $item[0], $item[1]);
            }
            $sql->AddEqual("clgID", $this->ID);
            $r = $sql->Execute();
            $sql->CloseSTMT();
            if ($r == FALSE) {
                $this->GetError()->AddItem(1, "fail to execute");
                return FALSE;
            }
            return TRUE;
        }
    }

    public function Delete($id=0) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        if ($id == 0) {
            $id = $this->ID;
        } else if (!$this->checkCatalog($id, $conn)) {
            $this->GetError()->AddItem(1, "no access");
            return FALSE;
        }
        $withcontent = FALSE;
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
        $sql->AddField("cntID");
        $sql->AddEqual("cntCatalogID", $id);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $withcontent = TRUE;
        } else {
            $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
            $sql->AddField("clgID");
            $sql->AddEqual("clgParentID", $id);
            $sql->Execute();
            $r = $sql->Fetch(1);
            $sql->CloseSTMT();
            if ($r) {
                $withcontent = TRUE;
            }
        }
        if ($withcontent) {
            $this->GetError()->AddItem(1, "catalog with content is not allowed to be deleted");
            return FALSE;
        }
        $sql = $conn->CreateDeleteSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
        $sql->AddEqual("clgID", $id);
        $r = $sql->Execute();
        $sql->CloseSTMT();
        if (!$r) {
            $this->GetError()->AddItem(1, "fail to delete");
            return FALSE;
        }
        return TRUE;
    }

    public function Exists($CatalogID) {
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
        $sql->AddField("clgID");
        $sql->AddEqual("clgID", $CatalogID);
        $sql->Execute();
        $row = $sql->Fetch(1);
        $e = FALSE;
        if ($row)
            $e = TRUE;
        $sql->CloseSTMT();
        return $e;
    }

    public function SetName($name) {
        $this->SetValue("clgName", $name, IBC1_DATATYPE_PURETEXT);
    }

    public function SetOrdinal($n) {
        $this->SetValue("clgOrdinal", $n);
    }

    public function SetUID($uid) {
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_admin");
        $sql->AddField("admUID");
        $sql->AddEqual("admUID", $uid, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $sql->AddEqual("admCatalogID", $this->ID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        $sql->Execute();
        $sql->CloseSTMT();
        if ($r = $sql->Fetch(1)) {
            $this->SetValue("clgUID", $r->admUID);
            return TRUE;
        }
        return FALSE;
    }

    /*
     * 上层目录的访问/管理权是否限制下层？
     * 1.不限制：有权管理上层目录，但有时对下层没有权（只能删除，不可管理细节）
     * 2.限制：如果上层目录不完全公开，下层必须和上层的权限设置相同；
     *         如果上层目录完全公开，下层可以增添管理组或拥有者的设置
     * */

    public function SetAdminGroup($g) {
        $this->SetValue("clgAdminGID", $g);
    }

    public function SetVisitGroup($g) {
        $this->SetValue("clgVisitGID", $g);
    }

    public function MoveTo($ParentID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $ParentID = intval($ParentID);
        $conn = $this->GetDBConn();
        if (!$this->checkCatalog($ParentID, $conn)) {
            $this->GetError()->AddItem(1, "no access");
            return FALSE;
        }
        if ($this->Exists($ParentID)) {
            $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
            $sql->AddField("clgID");
            $sql->AddEqual("clgParentID", $this->ID);
            $sql->Execute();
            $r = $sql->Fetch(1);
            $sql->CloseSTMT();
            while ($r) {
                if ($r->clgID == $ParentID) {
                    $this->GetError()->AddItem(1, "cannot move to sub catalogs");
                    return FALSE;
                }
                $sql->ClearConditions();
                $sql->AddEqual("clgParentID", $r->clgID);
                $sql->Execute();
                $r = $sql->Fetch(1);
                $sql->CloseSTMT();
            }
            $this->SetValue("clgParentID", $ParentID);
            return TRUE;
        }
        $this->GetError()->AddItem(1, "the catalog does not exist");
        return FALSE;
    }

    private function checkCatalog($id, &$conn) {
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_catalog");
        $sql->AddField("clgGID");
        $sql->AddField("clgUID");
        $sql->AddEqual("clgID", $id);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        $gid = 0;
        $clgUID = "";
        if ($r) {
            $gid = $r->clgGID;
            $clgUID = $r->clgUID;
        } else {
            $this->GetError()->AddItem(1, "not exist");
            return FALSE;
        }
        return TRUE;
    }

    public function AddAdmin($UID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_admin");
        $sql->AddField("admID");
        $sql->AddEqual("admCatalogID", $this->ID);
        $sql->AddEqual("admUID", $UID, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $sql->Execute($SQL->GetSTMT());
        $row = $sql->Fetch(1);
        $e = FALSE;
        if ($row)
            $e = TRUE;
        $sql->CloseSTMT();
        if (!$e) {
            $sql = $conn->CreateInsertSTMT("ibc1_clg" . $this->GetServiceName() . "_admin");
            $sql->AddValue("admCatalogID", $this->ID);
            $sql->AddValue("admUID", $UID, IBC1_DATATYPE_PURETEXT);
            $r = $sql->Execute();
            $sql->CloseSTMT();
            if ($r == FALSE)
                return FALSE;
            return TRUE;
        }
        return FALSE;
    }

    public function RemoveAdmin($UID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateDeleteSTMT("ibc1_clg" . $this->GetServiceName() . "_admin");
        $sql->AddEqual("admUID", $UID, IBC1_DATATYPE_PURETEXT);
        $sql->AddEqual("admCatalogID", $this->ID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        $r = $sql->Execute();
        $sql->CloseSTMT();
        if ($r == FALSE)
            return FALSE;
        return TRUE;
    }

    public function GetAdminList() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_admin");
        $sql->AddField("admUID");
        $sql->AddEqual("admCatalogID", $this->ID);
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
