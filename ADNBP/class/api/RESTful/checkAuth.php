<?php
$msgerror ='';

// Auth Process
if(!strlen($this->getHeader('X-Cloudservice-Id'))) $msgerror.="(Missing X-Cloudservice-Id Header)";
else {
	$token = $this->getConf("CloudServiceToken-".$this->getHeader('X-Cloudservice-Id'));
	if(!strlen($token)) $msgerror.="(X-Cloudservice-Id doesn't exist)";
}

if(!strlen($this->getHeader('X-Cloudservice-Date'))) $msgerror.="(Missing X-Cloudservice-Date Header)";
else {
	$date = $this->getHeader('X-Cloudservice-Date');
	// Check control if Date is too old (more than 10 min for example.) PENDING
}

if(!strlen($this->getHeader('X-Cloudservice-Signature'))) $msgerror.="(Missing X-Cloudservice-Signature Header)";
else if(!strlen($msgerror)){
	$signature = $this->getHeader('X-Cloudservice-Signature');
	$signaureCreate = strtoupper(sha1($this->getHeader('X-Cloudservice-Id').$date.$token));
	if($signature != $signaureCreate) {
		if(!(strlen($this->getHeader('X-Cloudservice-Mastersignature')) 
		   && strlen($this->getConf("adminPassword"))
		   && $this->checkPassword($this->getHeader('X-Cloudservice-Mastersignature'), $this->getConf("adminPassword")))
		)
		$msgerror.="(Signature doesnt match)";
	}
}	
?>