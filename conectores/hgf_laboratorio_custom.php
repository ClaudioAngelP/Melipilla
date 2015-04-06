<?php 

	chdir(dirname(__FILE__));

  require_once('../config.php');
  require_once('sigh.php');
  require_once('hgf.php');

	set_time_limit(0);

   $sqlserver=false;
  
  	if($sqlserver = mssql_connect('laboratorio', 'consulta', 'hgf2011')) {
  	//$sqlserver = mssql_connect('trolin', 'hgf', 'asd123');

	echo "Conexi&oacute;n SQLSERVER LABORATORIO establecida.<br><br>";
	
	} else {
	
	die("Imposible conectar a SQLSERVER LABORATORIO.");	

	}
  
  mssql_select_db('Fricke', $sqlserver);
  //mssql_select_db('baklaboratorio', $sqlserver);
  
  if(isset($_GET['fecha'])) {
  	
  	$fecx=explode('/',$_GET['fecha']);
  	$fecha=$_GET['fecha'];
  	$fec=$fecx[1].'/'.$fecx[0].'/'.$fecx[2];
  	$fechax=$fecha;

  	if(isset($_GET['fecha2'])) {

	  	$fecx=explode('/',$_GET['fecha2']);
  		$fecha2=$_GET['fecha2'];
  		$fec2=$fecx[1].'/'.$fecx[0].'/'.$fecx[2];

  	}
  	
  } else {
  	
  	$fec='01/18/2011';
  	$fecha='18/01/2011';
  	$fechax=date('d/m/Y h:i:s');
  	$fec2='04/18/2011';
  	
  }	  	
  
  if(!isset($fec2)) {
  $examenes = mssql_query("SELECT
  									FATURAID,
  									FATURA.PACIENTEID,
  									PACIENTE.DOCUMENTO,
  									NOME1, NOME2, NOME3,
  									DATA,
  									FATURA.PROCID,
  									DESCRICAO,
  									CODIGO
  									FROM FATURA 
  									JOIN PROCEDIMENTOS ON FATURA.PROCID=PROCEDIMENTOS.PROCID
  									JOIN PACIENTE ON FATURA.PACIENTEID=PACIENTE.PACIENTEID
  									WHERE DATA = '$fec'
  									");
  	} else {
  $examenes = mssql_query("SELECT
  									FATURAID,
  									FATURA.PACIENTEID,
  									PACIENTE.DOCUMENTO,
  									NOME1, NOME2, NOME3,
  									DATA,
  									FATURA.PROCID,
  									DESCRICAO,
  									CODIGO
  									FROM FATURA 
  									JOIN PROCEDIMENTOS ON FATURA.PROCID=PROCEDIMENTOS.PROCID
  									JOIN PACIENTE ON FATURA.PACIENTEID=PACIENTE.PACIENTEID
  									WHERE DATA >= '$fec' AND DATA <= '$fec2'
  									");  		
  	}
  									
  	print("\n\nCONSULTA OK... (".mssql_num_rows($examenes)." REGISTROS)\n\n");

	$c=0;
	$num=mssql_num_rows($examenes);
  
	$nuevos=0; $repetidos=0; $sinrut=0;  
  
  while($reg=mssql_fetch_object($examenes)) {
    		
			$c++;
			
			if(($c % 100)==0) print(number_format($c*100/$num,2,'.',',')."% ... ");
			
			flush();    		

  			$tipo=5; // ID SISTEMA LABORATORIO
  			$id=$reg->FATURAID*1;
  			
  			$chk=cargar_registro("SELECT * FROM prestacion WHERE
				porigen_id=$tipo AND porigen_num=$id");

			if($chk) {
				$repetidos++; continue;
			}
    		
  			list($fecha)=explode(' ',$reg->DATA);
  			
  			$rut=explode('-',trim($reg->DOCUMENTO));
  			
  			if(isset($rut[1]) AND trim($rut[1])!='') {
	  			$qrut=($rut[0]*1).'-'.$rut[1];
  				$pac=cargar_paciente($qrut, 1); // Carga x RUT
  			} else {
  				$pac=cargar_paciente($rut[0], 2); // Carga x Ficha
  			}
  			
  			if($pac==-1) {
  				print("\n[$qrut] PACIENTE NO ENCONTRADO.\n");
  				$sinrut++;
  				continue;	
  			}
  			
  			$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac");  			
  			$pac_id=$pac['pac_id'];
  			
  			$codigo=str_replace(' ','',str_replace('.','',$reg->CODIGO));
  			$cantidad=1;
  			$extra='false';
  			$inst_id=$sgh_inst_id;
  			$esp_id='-256';
  			$desc=$reg->DESCRICAO;
			
				pg_query("
					INSERT INTO prestacion VALUES (
					DEFAULT, '$fecha', $pac_id, -1, 
					$tipo, $id, 
					'$codigo', '$codigo', $cantidad,
					$extra, $inst_id, $esp_id, '', '$desc', 0, 0, 0, -1, -1, 0, 
					0, 0		
					);
				");
	
			$nuevos++;
						  	
  }
  
	echo "\n [$fechax] NUEVOS: $nuevos REPETIDOS: $repetidos SIN RUT: $sinrut \n\n";  
  
?>