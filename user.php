<?php
// @author jack krawczyk -- @jackk
// last updated: Mar 3, 2012

// User profile class definition
// This class is the predominant object upon which floathope is built.
// There are a few methods that still need to have validation implemented.
class user 
{
	public $firstName;
	public $lastName;
	public $email;
	public $userID;
	public $guiltySites;
	public $charityList;
	
	// billing info
	public $addressLine1;
	public $addressLine2;
	public $billingCity;
	public $billingState;
	public $billingZip;
	public $billingCountry;
	
	public function __construct($userid)
	{
		$this->firstName = "";
		$this->lastName = "";
		$this->userID = $userid;
		$this->guiltySites = array();
		$this->charityList = array();
		
	}
	
	public function setFirstName($fn)
	{
		$this->firstName = $fn;
	}
	
	public function setLastName($ln)
	{
		$this->lastName = $ln;
	}
	
	public function setEmail($inputEmail)
	{
		$this->email = $inputEmail;
	}
	
	public function addGuiltySite($site)
	{
		$this->guiltySites[count($this->guiltySites)] = $site;
	}
	
	public function getGuiltySiteByIndex($inputIndex)
	{
		if(count($this->guiltySites) < $inputIndex)
		{
			$indexItem = $this->guiltySites[$inputIndex];
			return $indexItem;
		}
		else
		{
			// handle incorrect input index
		}
	}
	
	public function addCharity($charity)
	{
		$this->charityList[count($this->charityList)] = $charity;
	}
	
	public function getCharityByIndex($inputIndex)
	{
		if(count($this->charityList) < $inputIndex)
		{
			$indexItem = $this->charityList[$inputIndex];
			return $indexItem;
		}
		else
		{
			// handle incorrect input index
		}
	}
	
	public function getUserInfo($inputUser)
	{
		$this->firstName = $inputUser->firstName;
		$this->lastName = $inputUser->lastName;
		$this->userID = $inputUser->userID;
		
		return $this;
	}
	
	public function getName()
	{
		return $this->firstName . " " .$this->lastName;
	}
	
	// return all user data into JSON for communication across PHP & Chrome Extension
	public function printUserJSON()
	{
		$jsonArray = array('userid' => $this->userID,
			'name' => $this->getName(),
			'guiltySites' => $this->guiltySites,
			'charityList' => $this->charityList);
		
		$jsonToReturn = json_encode($jsonArray);
		
		return $jsonToReturn;
	}
}


?>