<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.catalog
 */
class ContentItemEditor extends DataItem {

    protected $IsNew = TRUE;
    protected $ID = 0;
    protected $AdminGrade = -1;
    protected $VisitGrade = -1;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "clg");
    }

    public function Create() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->ID = 0;
        $this->IsNew = TRUE;
        return TRUE;
    }

    public function Open($ID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
        $sql->AddField("cntID");
        $sql->AddField("cntAdminGrade");
        $sql->AddField("cntVisitGrade");
        $sql->AddField("cntUID");
        //$sql->AddField("");
        $sql->AddEqual("cntID", $ID);
        $sql->Execute();
        $row = $sql->Fetch(1);
        $sql->CloseSTMT();
        if (!$row) {
            $this->GetError()->AddItem(1, "not exists");
            return FALSE;
        }
        $this->ID = $row->cntID;
        $this->AdminGrade = $row->cntAdminGrade;
        $this->VisitGrade = $row->cntVisitGrade;
        $this->IsNew = FALSE;
        return TRUE;
    }

    public function Save($CatalogID=0) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $essential = 2;

        if ($this->IsNew) {
            if ($CatalogID == 0) {
                $this->GetError()->AddItem(1, "|no parent catalog");
                return FALSE;
            }
            if (!$this->CatalogExists($CatalogID)) {
                $this->GetError()->AddItem(1, "该目录不存在");
                return FALSE;
            }


            /*
              check
              private $Name;
              private $Author;
              private $Keywords;

              private $PointValue;
              private $VisitCount;

              private $UID;
              private $VisitGrade;
              private $AdminGrade;
             */
            $this->SetValue("cntCatalogID", $CatalogID, IBC1_DATATYPE_INTEGER);

            if ($this->Count() < $essential) {
                $this->GetError()->AddItem(1, "some fields have been changed");
                return FALSE;
            }
            $conn = $this->GetDBConn();
            $sql = $conn->CreateInsertSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
            $this->MoveFirst();
            while (list($key, $item) = $this->GetEach()) {
                $sql->AddValue($key, $item[0], $item[1]);
            }
            $sql->AddValue("cntTimeCreated", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);

            $r = $sql->Execute();

            if ($r == FALSE) {
                $sql->CloseSTMT();
                $this->GetError()->AddItem(2, "数据库操作出错");
                return FALSE;
            }
            $this->ID = $sql->GetLastInsertID();
            $sql->CloseSTMT();
            return TRUE;
        } else {
            /*
              check

              $this->SetName($this->Name);
              $this->SetAuthor($this->Author);
              $this->SetKeywords($this->Keywords);
             */
            if ($this->Count() == 0) {
                $this->GetError()->AddItem(1, "no fields have not been set");
                return FALSE;
            }

            $conn = $this->GetDBConn();
            $sql = $conn->CreateUpdateSTMT();
            $sql->SetTable("ibc1_clg" . $this->GetServiceName() . "_content");
            $this->MoveFirst();
            while (list($key, $item) = $this->GetEach()) {
                $sql->AddValue($key, $item[0], $item[1]);
            }
            $sql->AddValue("cntTimeUpdated", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
            $sql->AddEqual("cntID", $this->ID);
            $r = $sql->Execute();
            $sql->CloseSTMT();
            if ($r == FALSE) {
                $this->GetError()->AddItem(2, "数据库操作出错");
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
        if ($id == 0)
            $id = $this->ID;
        $conn = $this->GetDBConn();
        if (!$this->checkContent($id, $conn)) {
            $this->GetError()->AddItem(1, "no access");
            return FALSE;
        }
        $sql = $conn->CreateDeleteSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
        $sql->AddEqual("cntID", $id);
        $r = $sql->Execute();
        $sql->CloseSTMT();
        if (!$r) {

            return FALSE;
        }
        return TRUE;
    }

    private function checkContent($id, &$conn) {
        $sql = $conn->CreateSelectSTMT("ibc1_clg" . $this->GetServiceName() . "_content");
        $sql->AddField("cntAdminGrade");
        $sql->AddField("cntUID");
        $sql->AddEqual("cntID", $id);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if (!$r) {
            $this->GetError()->AddItem(1, "content does not exist");
            return FALSE;
        }
        return TRUE;
    }

    public function GetID() {
        return $this->ID;
    }

    public function SetName($name) {
        $this->SetValue("cntName", $name, IBC1_DATATYPE_PURETEXT);
    }

    public function SetAuthor($author) {
        $this->SetValue("cntAuthor", $author, IBC1_DATATYPE_PURETEXT);
    }

    public function SetKeywords($keywords) {
        $this->SetValue("cntKeywords", $keywords, IBC1_DATATYPE_WORDLIST);
    }

    public function SetPointValue($n) {
        $this->SetValue("cntPointValue", $n);
    }

    public function SetOrdinal($n) {
        $this->SetValue("cntOrdinal", $n);
    }

    public function SetUID($uid) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->SetValue("cntUID", $uid, IBC1_DATATYPE_PURETEXT);
    }

    /*
      未登录	即“游客”	Grade=0
      普通	对规定范围内所有人可见，只有发布者才能改	VisitGrade>=0,AdminGrade=-1
      隐私	只有发布者才可见可改	VisitGrade=AdminGrade=-1

     */

    public function SetVisitGrade($g) {
        $g = intval($g);
        if ($g < 0)
            $g = -1;
        if ($g != 0) {
            if ($g <= $this->AdminGrade) {
                $this->SetValue("cntVisitGrade", $g);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function SetAdminGrade($g) {
        $g = intval($g);
        if ($g < 0)
            $g = -1;
        if ($g != 0) {
            if ($g >= $this->VisitGrade) {
                $this->SetValue("cntAdminGrade", $g);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function CatalogExists($CatalogID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
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

    public function MoveTo($CatalogID) {
        if ($this->IsNew) {
            return FALSE;
        }
        $CatalogID = intval($CatalogID);
        if ($this->CatalogExists($CatalogID)) {
            $this->SetValue("cntCatalogID", $CatalogID);
            return TRUE;
        }
        return FALSE;
    }

    public function AddVisitCount() {
        $this->SetValue("cntVisitCount", "cntVisitCount+1", IBC1_DATATYPE_EXPRESSION);
    }

    public function ClearVisitCount() {
        $this->SetValue("cntVisitCount", 0);
    }

}

?>
