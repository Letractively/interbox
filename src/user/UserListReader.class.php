<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
class UserListReader extends DataList {

    private $groupid = 0;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "usr");
    }

    private function AddFields(&$sql) {
        $sql->AddField("usrUID AS UID");
        $sql->AddField("usrGrade AS Grade");
        $sql->AddField("usrPoints AS Points");
        $sql->AddField("usrIsOnline AS IsOnline");
        $sql->AddField("usrIsUserAdmin AS IsUserAdmin");
    }

    public function LoadUser($UID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        //只支持单页列表，PageSize=0时为单页列表
        //only support single-page list,it is a single-page list when PageSize=0
        if ($this->GetPageSize() != 0 || $this->GetPageNumber() > 1) {
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT();
        $sql->SetTable("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->JoinTable("ibc1_usr" . $this->GetServiceName() . "_GroupUser", "usrUID=gpuUID");
        $this->AddFields($sql);
        $sql->AddEqual("usrUID", $UID);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $this->AddItem($r);
            return TRUE;
        }
        return FALSE;
    }

    public function SetGroup($id) {
        $this->groupid = intval($id);
    }

    public function LoadList($online=0, $useradmin=0) {
        //0:not included
        //1:only
        //2:all
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT();
        $sql->AddField("COUNT(usrUID) AS c");
        $sql->SetTable("ibc1_usr" . $this->GetServiceName() . "_user");

        if ($this->groupid != 0) {
            $sql->JoinTable("ibc1_usr" . $this->GetServiceName() . "_GroupUser", "usrUID=gpuUID");
            $sql->AddEqual("gpuGID", $this->groupid, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        }

        if ($useradmin == 0)
            $sql->AddCondition("usrIsUserAdmin=0", IBC1_LOGICAL_AND);
        else if ($useradmin == 1)
            $sql->AddCondition("usrIsUserAdmin!=0", IBC1_LOGICAL_AND);

        $this->GetCounts1($sql);

        $sql->ClearFields();
        $this->AddFields($sql);

        $sql->SetLimit($this->GetPageSize(), $this->GetPageNumber());
//echo "<p>".$sql->GetSTMT()."</p>";
        $sql->Execute();

        $this->Clear();
        while ($r = $sql->Fetch(1)) {
            $this->AddItem($r);
        }
        $this->GetCounts2();
        $sql->CloseSTMT();
    }

    public function Open($GID) {
        $this->SetGroup($GID);
        $this->LoadList();
    }

    public function OpenOnlineList() {
        $this->LoadList(1, 2);
    }

    public function OpenUserAdminList() {
        $this->LoadList(2, 1);
    }

}

?>
