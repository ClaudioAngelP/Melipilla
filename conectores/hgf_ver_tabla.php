<?php 

  $tabla=$_GET['tabla'];

  $sybase = @mssql_connect('orden', 'consulta', 'consulta');
  
  @mssql_select_db('BD_ENTI_CORPORATIVA', $sybase);
  
  //require_once('hgf_sqlserver.php');
  
  mssql_query("SET ROWCOUNT 100");
  mssql_query("SET DATEFORMAT dmy");

	if(!isset($_GET['campo'])) {
  
  		$datos = mssql_query("SELECT * FROM dbo.$tabla");
  	
  	} else {
  		
		$campo=$_GET['campo'];  		
  		
  		$datos = mssql_query("SELECT DISTINCT $campo FROM dbo.$tabla");  		
  		
  	}
  
  $c=0;
  
 while($r = mssql_fetch_object($datos)) {

		print_r($r);
		
		print('<br><br>');

	}
	
?>