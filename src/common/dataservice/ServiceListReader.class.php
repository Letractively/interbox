<?php
/**
 *@name InterBox Core For PHP
 *@version Model 1.3, Program 0.6 
*@author GuZhiji Studio

 ServiceListReader.class.php
 */
class ServiceListReader extends ServiceList
{

	public function LoadService($ServiceName)
	{
		//只支持单页列表，PageSize=0时为单页列表
		if($this->GetPageSize()!=0||$this->GetPageNumber()>1) return FALSE;
		$conn=$this->GetDBConn();
		$sql=$conn->CreateSelectSTMT("ibc1_dataservice");
		$sql->AddField("*");
		$sql->AddEqual("ServiceName",$ServiceName,IBC1_DATATYPE_PURETEXT);

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
	public function LoadList()
	{
		$conn=$this->GetDBConn();
		$sql=$conn->CreateSelectSTMT("ibc1_dataservice");
		$sql->AddField("COUNT(ServiceName) AS c");

		$this->GetCounts1($sql);

		$sql->ClearFields();
		$sql->AddField("*");
		$sql->SetLimit($this->GetPageSize(),$this->GetPageNumber());

		$sql->Execute();

		$this->Clear();
		while($r=$sql->Fetch(1))
		{
			$this->AddItem($r);
		}
		$this->GetCounts2();
		$sql->CloseSTMT();
	}
	public function OpenByType($t)
	{
		$conn=$this->GetDBConn();
		$sql=$conn->CreateSelectSTMT();
		$sql->SetTable("ibc1_dataservice");
		$sql->AddField("COUNT(ServiceName) AS c");
		$sql->AddEqual("ServiceType",$t,IBC1_DATATYPE_PURETEXT);
		$this->GetCounts1($sql);

		$sql->ClearFields();
		$sql->AddField("*");
		$sql->SetLimit($this->GetPageSize(),$this->GetPageNumber());

		$sql->Execute();

		$this->Clear();
		while($r=$sql->Fetch(1))
		{
			$this->AddItem($r);
		}
		$this->GetCounts2();
		$sql->CloseSTMT();
	}
}
?>
