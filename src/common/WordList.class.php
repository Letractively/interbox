<?php
/**
 *@name InterBox Core For PHP
 *@version Model 1.3, Program 0.6
 *@author GuZhiji Studio
 */
class WordList extends ItemList
{
	function __construct($w="")
	{
		if($w!="") $this->SetWords($w);
	}
	public function SetWords($w){
		$this->Clear();
		$w = str_replace("%"," ",$w);
		//ASCII
		$t[] = chr(10);
		$t[] = chr(13);
		$t[] = " ";
		$t[] = ",";
		$t[] = ".";
		$t[] = "?";
		$t[] = "!";
		$t[] = ":";
		$t[] = ";";
		$t[] = "<";
		$t[] = ">";
		$t[] = "[";
		$t[] = "]";
		$t[] = "{";
		$t[] = "}";
		$t[] = "'";
		$t[] = "\"";
		$t[] = "|";

		//GBK
		$t[] = "¡¡";
		$t[] = "£¬";
		$t[] = "¡£";
		$t[] = "£¿";
		$t[] = "£¡";
		$t[] = "£º";
		$t[] = "£»";
		$t[] = "¡¶";
		$t[] = "¡·";
		$t[] = "¡µ";
		$t[] = "¡´";
		$t[] = "¡®";
		$t[] = "¡¯";
		$t[] = "¡°";
		$t[] = "¡±";

		foreach($t as $item)
		{
			$w=str_replace($item,"%",$w);
		}
		$wa=explode("%",$w);
		foreach($wa as $item)
		{
			if($item!="") $this->AddItem($item);
		}

	}
	public function GetWords()
	{
		$w="";
		while($item=$this->GetEach())
		{
			$w.=$item." ";
		}
		return substr($w,0,-1);
	}
	public function HasWord($w,$casesensitive=FALSE)
	{
		$this->MoveFirst();
		if($casesensitive)
		{
			while($item=$this->GetEach())
			{
				if($w==$item) return TRUE;
			}
		}
		else
		{
			while($item=$this->GetEach())
			{
				if(strtolower($w)==strtolower($item)) return TRUE;
			}
		}
		return FALSE;
	}
}

?>