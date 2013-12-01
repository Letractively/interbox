<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
class GroupListReader extends DataList {

    private $owner = "";
    private $user = "";
    private $groupname = "";
    private $groupnameexact = FALSE;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "usr");
    }

    public function LoadGroup($gid) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        //only support single-page list,it is a single-page list when PageSize=0
        if ($this->GetPageSize() != 0 || $this->GetPageNumber() > 1) {

            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_group");
        $sql->AddField("*");
        $sql->AddEqual("grpID", $gid);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $this->AddItem($r);
            return TRUE;
        }
        return FALSE;
    }

    public function SetOwner($uid) {
        $this->owner = $uid;
    }

    public function SetName($name, $exact=FALSE) {
        $this->groupname = $name;
        $this->groupnameexact = $exact;
    }

    public function SetUser($uid) {
        $this->user = $uid;
    }

    public function LoadList($type=0) { //type:0=private,1=public,2=all
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT();
        if ($this->user != "") {
            $sql->SetTable("ibc1_usr" . $this->GetServiceName() . "_Group RIGHT JOIN IBC1_usr" . $this->GetServiceName() . "_GroupUser ON gpuGID=grpID");
            $sql->AddEqual("gpuUID", $this->user, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        } else {
            $sql->SetTable("ibc1_usr" . $this->GetServiceName() . "_group");
        }
        if ($this->owner != "") {
            $sql->AddEqual("grpOwner", $this->owner, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
            if ($type == 0 || $type == 1)
                $sql->AddEqual("grpType", $type, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        }
        if ($this->groupname != "") {
            if ($this->groupnameexact)
                $sql->AddEqual("grpName", $this->groupname, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
            else
                $sql->AddLike("grpName", $this->groupname, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        }
        $sql->AddField("COUNT(grpID) AS c");
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

    //open groups that are owned by the user
    public function OpenByOwner($uid, $type=0) { //type:0=private,1=public,2=all
        $this->SetOwner($uid);
        $this->LoadList($type);
    }

    public function OpenByName($name, $exact=FALSE) {
        $this->SetName($name, $exact);
        $this->LoadList(2);
    }

    //open groups that have been taken part in
    public function OpenByUser($uid, $type=0) {
        $this->SetUser($uid);
        $this->LoadList($type);
    }

    /*
      $sql[2]="CREATE TABLE IBC1_usr".$ServiceName."_GroupUser(";
      $sql[2].=" gpuID INT(10) NOT NULL AUTO_INCREMENT,";
      $sql[2].=" gpuUID VARCHAR(255) NOT NULL,";
      $sql[2].=" gpuGID INT(10) NOT NULL,";
      $sql[2].=" PRIMARY KEY (gpuID)";
      $sql[2].=") TYPE=MyISAM DEFAULT CHARSET=gb2312;";

      $sql[3]="CREATE TABLE IBC1_usr".$ServiceName."_Group(";
      $sql[3].=" grpID INT(10) NOT NULL AUTO_INCREMENT,";
      $sql[3].=" grpName VARCHAR(255) NOT NULL,";
      $sql[3].=" grpOwner VARCHAR(255) NOT NULL DEFAULT 'NULL'";
      $sql[3].=" grpType INT(2) NOT NULL DEFAULT 0,";
      $sql[3].=" PRIMARY KEY (grpID)";
      $sql[3].=") TYPE=MyISAM DEFAULT CHARSET=gb2312;";
     */
}

?>
