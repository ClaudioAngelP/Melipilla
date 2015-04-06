<?php 

	require_once('InterSystems.php');

	function TestConnection() {
		return '1';	
	}
	
	$soap=new InterSystems();
	
	$soap->addFunction('TestConnection');
	
	TestConnection test;	
	
	$soap->TestConnection(test);


?>