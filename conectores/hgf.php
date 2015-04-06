<?php 

	function cargar_paciente($pac_id, $tipo=0) {
		
	GLOBAL $sybase;

	if($tipo==0) {
		$q="SELECT * FROM pacientes WHERE pac_id=$pac_id";
	
		$chk=cargar_registro($q);
	} elseif($tipo==1) {
		$q="SELECT * FROM pacientes WHERE pac_rut='$pac_id'";
	
		$chk=cargar_registro($q);		
	} elseif($tipo==2) {
		$q="SELECT * FROM pacientes WHERE pac_ficha='$pac_id'";
	
		$chk=cargar_registro($q);		
	}

	if(!$chk) {
	
	  if($tipo==0) {

	  	$datos_pac = @mssql_query("SELECT * FROM dbo.PAC_Paciente WHERE PAC_PAC_Numero=$pac_id", $sybase);
	  	$datos_car = @mssql_query("SELECT * FROM dbo.PAC_Carpeta WHERE PAC_PAC_Numero=$pac_id", $sybase);

	  } elseif($tipo==1) {

		$pac_id=trim($pac_id);

		$tam=strlen($pac_id);
		$offset=10-$tam;
		
		$pac_id=str_repeat('0',$offset).''.$pac_id;

	  	$datos_pac = mssql_query("SELECT * FROM dbo.PAC_Paciente WHERE PAC_PAC_Rut='$pac_id'", $sybase);

    	if(mssql_num_rows($datos_pac)==0) { return -1; }
	  	$row=mssql_fetch_object($datos_pac);
	  	$pac_id=$row->PAC_PAC_Numero*1;

	  	$datos_pac = @mssql_query("SELECT * FROM dbo.PAC_Paciente WHERE PAC_PAC_Numero=$pac_id", $sybase);
	  	$datos_car = @mssql_query("SELECT * FROM dbo.PAC_Carpeta WHERE PAC_PAC_Numero=$pac_id", $sybase);

	  } elseif($tipo==2) {

	  	$datos_car = @mssql_query("SELECT * FROM dbo.PAC_Carpeta WHERE PAC_CAR_NumerFicha=$pac_id", $sybase);	  	

	  	if(mssql_num_rows($datos_car)==0) { return -1; }
	  	$row=mssql_fetch_object($datos_car);
	  	$pac_id=$row->PAC_PAC_Numero*1;

	  	$datos_pac = @mssql_query("SELECT * FROM dbo.PAC_Paciente WHERE PAC_PAC_Numero=$pac_id", $sybase);
	  	$datos_car = @mssql_query("SELECT * FROM dbo.PAC_Carpeta WHERE PAC_PAC_Numero=$pac_id", $sybase);

	  }

		if(!$datos_pac OR !$datos_car) {
			return -1;	
		}
	  
	  if($row = mssql_fetch_object($datos_pac)) {
	  		  
		    if($row->PAC_PAC_Sexo=='M') $sexo='0'; 
		    elseif($row->PAC_PAC_Sexo=='F') $sexo='1';
		    else $sexo='2';
		    
		    $comuna=cargar_registro("
		    	SELECT * FROM comunas WHERE ciud_cod_local=".($row->PAC_PAC_ComunHabit*1)."
		    ");
		    
		    // print("CIUDAD: ".$row->PAC_PAC_ComunHabit."=".($comuna['ciud_id']*1)." <BR><BR>");
		    
		    $ciudad=$comuna['ciud_id']*1;
		    
		    $rpac=explode('-',trim($row->PAC_PAC_Rut));
	
			 $rut_pac=($rpac[0]*1).'-'.trim($rpac[1]);	

			 if($r2=mssql_fetch_object($datos_car)) {
			 	$ficha=(trim($r2->PAC_CAR_NumerFicha));	
			 } else {
			 	$ficha='';	
			 }
			 
			 switch(strtoupper(trim($row->PAC_PAC_Prevision))) {
			 	case 'F': 
			 		if($row->PAC_PAC_TipoBenef=='A') $prev=1;
			 		elseif($row->PAC_PAC_TipoBenef=='B') $prev=2;
			 		elseif($row->PAC_PAC_TipoBenef=='C') $prev=3;
			 		else $prev=4;			 		
			 		break;
			 	case 'I': $prev=5; break;	
			 	case 'P': $prev=6; break;	
			 	case 'FL': $prev=7; break;	
			 	case 'C': $prev=9; break;	
			 	case 'A': $prev=10; break;
			 	default: $prev=8; break;	
			 }
		    	    
			 $q="INSERT INTO pacientes VALUES (
		    ".pg_escape_string($row->PAC_PAC_Numero).",
		    '".pg_escape_string($rut_pac)."',
		    '".pg_escape_string($row->PAC_PAC_Nombre)."',
		    '".pg_escape_string($row->PAC_PAC_ApellPater)."',
		    '".pg_escape_string($row->PAC_PAC_ApellMater)."',
		    '".pg_escape_string($row->PAC_PAC_FechaNacim)."',
		    ".$sexo.", $prev,
		    '".pg_escape_string($row->PAC_PAC_PoblaHabit)."',
		    -1, -1,
		    '".pg_escape_string($row->PAC_PAC_DireccionGralHabit)."',
		    $ciudad, 0,
		    0, 
		    '".pg_escape_string($row->PAC_PAC_Fono)."',
		    '', '', '', '', 
		    '".pg_escape_string($ficha)."');
		    ";
		    
		    //print($q.'<br><br>');		    	    
		    	    
		    pg_query($q);
		    
		    $id=$row->PAC_PAC_Numero;
		    
		    return $id;
		    
		  } else {
		  
		    return -1;
		  
		  }
	  
	  } else {
	  
			return $chk['pac_id']*1;	  
	  	
	  }
  	
	}

  $sybase=false;


	while(!$sybase) {
  		$sybase = @mssql_connect('orden', 'consulta', 'consulta');
  		if(!$sybase) {
  			echo "Error de Conexi&oacute;n a SYBASE. Reintentando en 5 minutos.<br><br>";
  			sleep(300);	
  		}
	}

	echo "Conexi&oacute;n SYBASE establecida.<br><br>";
  
  @mssql_select_db( 'BD_ENTI_CORPORATIVA', $sybase );
  
  //mssql_query("SET ROWCOUNT 10");
  mssql_query("SET DATEFORMAT dmy");

?>