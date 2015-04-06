<?php 

  $sqlserver=false;
  
  
  while(!$sqlserver) {
  	$sqlserver = mssql_connect('sqlserver', 'sa', 'fricke');
  		if(!$sqlserver) {
  			echo "Error de Conexi&oacute;n a SQLSERVER. Reintentando en 5 minutos.<br><br>";
  			sleep(300);	
  		}
  }

	echo "Conexi&oacute;n SQLSERVER establecida.<br><br>";
  
  @mssql_select_db('BD_SOME', $sqlserver);

?>