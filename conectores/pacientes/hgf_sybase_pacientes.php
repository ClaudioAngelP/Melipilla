<?php

  if(isset($q)) $rut=$q; else $rut=$_GET['q'];
  if(!isset($tipo)) $tipo=3;

	ob_start();
	require_once('conectores/hgf.php');
	ob_end_clean();

	$pac_id=-1;
  
	if($tipo==3) {

	  $datos_car = @mssql_query("SELECT * FROM dbo.PAC_Carpeta WHERE PAC_CAR_NumerFicha=$rut", $sybase);
	  	
	  if(mssql_num_rows($datos_car) > 0) {
		  if($tmp=mssql_fetch_object($datos_car)) {
		  	$pac_id=$tmp->PAC_PAC_Numero;
		  } else {
		  	$pac_id=-1;	
		  }
	  } else {
	  	$pac_id=-1;	
	  }

	} elseif($tipo==0) {

	  $trut=explode('-',$rut);
	  $rut=sprintf('%08d',$trut[0]).'-'.strtoupper($trut[1]);
	  
	  $datos_pac = @mssql_query("SELECT * FROM dbo.PAC_Paciente WHERE PAC_PAC_Rut='$rut'", $sybase);
	  
	  if(mssql_num_rows($datos_pac) > 0) {
		  if($tmp=mssql_fetch_object($datos_pac)) {
		  	$pac_id=$tmp->PAC_PAC_Numero;
		  } else {
		  	$pac_id=-1;	
		  }
	  } else {
	  	$pac_id=-1;	
	  }
	  		
	} else {
		
		$id=$rut;
			
	}
	

	if($pac_id!=-1)
		$pac_id=cargar_paciente($pac_id);
	      
?>