<?php
/**
 *@name InterBox Core For PHP
 *@version Model 1.3, Program 0.6 
*@author GuZhiji Studio

 ServiceList.class.php
 */
class ServiceList
{
	private $_ConnObj=NULL;
	private $_ErrorList=NULL;


	//private $_list;
	private $_PageSize=0;
	private $_PageNumber=0;
	private $_PageCount=0;
	private $_ItemCount=0;
	private $_TotalCount=0;
	private $_Item=array();



	function __construct($Conn,$EL=NULL)
	{
		if($EL==NULL)
		$this->_ErrorList=new ErrorList();
		else
		$this->_ErrorList=$EL;
		$this->OpenConn($Conn);
		$this->GetError()->SetSource(__CLASS__);
		//$this->_list=new ItemList();
	}
	public function GetDBConn()
	{
		return $this->_ConnObj->GetConn();
	}
	public function GetError()
	{
		return $this->_ErrorList;
	}


	public function OpenConn($Conn)
	{
		if($this->_ConnObj!=NULL)
		$this->CloseService();
		$this->_ConnObj=$Conn;
		/*
		if($Conn instanceof MySQLConn)
		$this->_ConnObj=$Conn;
		else if($Conn instanceof DBConnProvider)
		$this->_ConnObj=$Conn->GetConn();
		*/
		$this->Clear();
		$this->_PageSize=0;
		$this->_PageNumber=0;
	}
	public function CloseConn()
	{
		$this->_ConnObj=NULL;
		$this->Clear();
		$this->_PageSize=0;
		$this->_PageNumber=0;
	}
	public function GetPageSize()
	{
		return $this->_PageSize;
	}
	public function SetPageSize($s)
	{
		$s=intval($s);
		if($s<1) $s=0;
		$this->_PageSize=$s;
	}
	public function GetPageNumber()
	{
		return $this->_PageNumber;
	}
	public function SetPageNumber($n)
	{
		$n=intval($n);
		if($n<1) $n=1;
		$this->_PageNumber=$n;
	}
	public function GetPageCount()
	{
		return $this->_PageCount;
	}
	public function Count()
	{
		return $this->_ItemCount;
	}
	public function GetTotalCount()
	{
		return $this->_TotalCount;
	}
	public function GetItem($index)
	{
		return $this->_Item[$index];
	}
	public function GetEach()
	{
		return each($this->_Item);
	}
	protected function AddItem($item)
	{
		$this->_Item[]=$item;
		$this->_ItemCount++;
		$this->_TotalCount++;
	}
	protected function Clear()
	{
		$this->_PageCount=0;
		$this->_TotalCount=0;
		$this->_ItemCount=0;
		$this->_Item=array();
	}
	protected function GetCounts1($sql)
	{
		$conn=$this->GetDBConn();
		$this->_TotalCount=intval($this->_TotalCount);
		$this->_PageSize=intval($this->_PageSize);
		if($this->_PageSize>0)
		{
			$sql->Execute($sql);
			$a=$sql->Fetch(0);
			$this->_TotalCount=intval($a[0]);
			$b=$this->_TotalCount/$this->_PageSize;
			if($b>intval($b)) $b=1+intval($b);
			$this->_PageCount=$b;
		}
		else
		{
			$this->_PageCount=0;
			$this->_TotalCount=0;
		}
	}
	protected function GetCounts2()
	{
		if($this->_PageSize<1&&$this->_ItemCount>0)
		{
			$this->_TotalCount=$this->_ItemCount;
			$this->_PageCount=1;
		}
	}

}