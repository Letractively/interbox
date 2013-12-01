<?php
/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.catalog
 */
class ContentListReader extends DataList
{
	protected $list_sql=NULL;

	function __construct(DBConnProvider $Conns,$ServiceName,ErrorList $EL=NULL)
	{
		parent::__construct($EL);
		$this->OpenService($Conns,$ServiceName);
		$this->GetError()->SetSource(__CLASS__);
	}
	public function OpenService(DBConnProvider $Conns,$ServiceName)
	{
		parent::OpenService($Conns,$ServiceName,"clg");
		$conn=$this->GetDBConn();
		$this->list_sql=$conn->CreateSelectSTMT("ibc1_clg".$this->GetServiceName()."_content");
	}
	private function AddFields(&$sql)
	{

		$sql->AddField("cntID AS ID");
		$sql->AddField("cntName AS Name");
		$sql->AddField("cntCatalogID AS CatalogID");
		//$sql->AddField("cntCatalogName AS CatalogName");
		$sql->AddField("cntAuthor AS Author");
		$sql->AddField("cntKeywords AS Keywords");
		$sql->AddField("DATE_FORMAT(cntTimeCreated,\"%Y-%m-%d %H:%i:%s\") AS TimeCreated");
		$sql->AddField("DATE_FORMAT(cntTimeUpdated,\"%Y-%m-%d %H:%i:%s\") AS TimeUpdated");
		$sql->AddField("DATE_FORMAT(cntTimeVisited,\"%Y-%m-%d %H:%i:%s\") AS TimeVisited");
		$sql->AddField("cntUID AS UID");
		$sql->AddField("cntVisitCount AS VisitCount");
		$sql->AddField("cntVisitGrade AS VisitGrade");
		$sql->AddField("cntAdminGrade AS AdminGrade");
		$sql->AddField("cntPointValue AS PointValue");

	}
	public function LoadContent($ID)//AddToList()
	{
		if(!$this->IsServiceOpen())
		{
			$this->GetError()->AddItem(1,"service has not been opened");
			return FALSE;
		}
		//只支持单页列表，PageSize=0时为单页列表
		//only support single-page list,it is a single-page list when PageSize=0
		if($this->GetPageSize()!=0||$this->GetPageNumber()>1) return FALSE;

		$conn=$this->GetDBConn();
		$sql=$conn->CreateSelectSTMT("ibc1_clg".$this->GetServiceName()."_content");
		$this->AddFields($sql);
		$sql->AddEqual("cntID",$ID,IBC1_DATATYPE_INTEGER);
		$sql->Execute();
		$r=$sql->Fetch(1);
		$sql->CloseSTMT();
		if($r)
		{
			$this->AddItem($r);
			return TRUE;
		}
		return FALSE;

	}
	public function SetName($name,$exact=FALSE)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		if($exact)
		$sql->AddEqual("cntName",$name,IBC1_DATATYPE_PURETEXT,IBC1_LOGICAL_AND);
		else
		$sql->AddLike("cntName",$name,IBC1_DATATYPE_PURETEXT,IBC1_LOGICAL_AND);
	}
	public function SetCatalog($ID)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		if($ID!=0)
		$sql->AddEqual("cntCatalogID",$ID,IBC1_DATATYPE_INTEGER,IBC1_LOGICAL_AND);//or/and
	}
	public function SetKeywords($KeyText)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		$wl=new WordList($KeyText);
		while($item=$wl->GetEach())
		{
			if($item!="")
			$sql->AddLike("cntKeywords",$item,IBC1_DATATYPE_PURETEXT,IBC1_LOGICAL_AND);
		}

	}
	public function SetAdminGrade($g)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		if($g>0)
		$sql->AddEqual("cntAdminGrade",$g,IBC1_DATATYPE_INTEGER,IBC1_LOGICAL_AND);
	}
	public function SetVisitGrade($g)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		if($g>0)
		$sql->AddEqual("cntVisitGrade",$g,IBC1_DATATYPE_INTEGER,IBC1_LOGICAL_AND);
	}
	public function SetAdmin($UID)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		if($UID!="")
		$sql->AddEqual("cntUID",$UID,IBC1_DATATYPE_PURETEXT,IBC1_LOGICAL_AND);
	}
	public function OrderBy($fieldname,$order=IBC1_ORDER_ASC)
	{
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		if($order!=IBC1_ORDER_ASC) $order=IBC1_ORDER_DESC;
		switch($fieldname)
		{
			case "name":
				$sql->OrderBy("cntName",$order);
				break;
			case "ordinal":
				$sql->OrderBy("cntOrdinal",$order);
				break;
			case "author":
				$sql->OrderBy("cntAuthor",$order);
				break;
			case "ctime":
				$sql->OrderBy("cntTimeCreated",$order);
				break;
			case "utime":
				$sql->OrderBy("cntTimeUpdated",$order);
				break;
			case "vtime":
				$sql->OrderBy("cntTimeVisited",$order);
				break;
			case "point":
				$sql->OrderBy("cntPointValue",$order);
				break;
			
			default:
				return FALSE;
		}
		return TRUE;
	}
	public function LoadList()
	{
		if(!$this->IsServiceOpen())
		{
			$this->GetError()->AddItem(1,"service has not been opened");
			return FALSE;
		}
		if($this->list_sql===NULL) return FALSE;
		$sql=$this->list_sql;
		$conn=$this->GetDBConn();
		$sql->ClearFields();
		$sql->AddField("COUNT(cntID) AS c");
		$this->GetCounts1($sql);
		$sql->ClearFields();
		$this->AddFields($sql);
		$sql->SetLimit($this->GetPageSize(),$this->GetPageNumber());

		$sql->Execute();

		$this->Clear();
		while($r=$sql->Fetch(1))
		{
			$this->AddItem($r);
		}
		$this->GetCounts2();
		$sql->CloseSTMT();
		return TRUE;
	}

	public function OpenCatalog($ID)
	{
		$this->SetCatalog($ID);
		$this->LoadList();
	}

	public function OpenWithKey($KeyText)
	{
		$this->SetKeywords($KeyText);
		$this->LoadList();
	}
	public function OpenWithAdmin($UID)
	{
		$this->SetAdmin($UID);
		$this->LoadList();
	}

}
?>