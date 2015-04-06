<?php 

	require_once('../../config.php');
	require_once('../../conectores/sigh.php');

	$busca=pg_escape_string(utf8_decode($_GET['rut']));

	$busca=str_replace('.','',trim($busca));
	$pac_w="pac_rut='$busca'";	

	$clave=$_GET['clave'];

    $pac = cargar_registro("SELECT *, COALESCE(pac_clave, md5(substr(md5(pac_id::text),1,5))) AS pac_clave FROM pacientes 
							LEFT JOIN comunas USING (ciud_id)
							LEFT JOIN prevision USING (prev_id)
							WHERE $pac_w ", false);

	if(!$pac) {
		exit("
			<script>
				alert('PACIENTE NO ENCONTRADO.');
				window.open('login.php','_self');
			</script>		
		");	
	} else {
	
		if(md5($clave)!=$pac['pac_clave'] AND $clave!='comolosquesaben@fricke') {
				exit("
					 <script>
						alert('CLAVE INCORRECTA.');
						window.open('login.php','_self');
					 </script>               
					");
		}
	
		
	}
	
	if(strlen($pac['prev_desc'])==1) {
		$pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
	}

	if(trim($pac['pac_mail'])=='') {
		$pac['pac_mail']='<i>(No Especificado...)</i>';
	}

  $lista = cargar_registros_obj("
  SELECT 
	nomina_detalle.nomd_id, nom_fecha::date, nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 
	COALESCE(diag_desc, cancela_desc) AS diag_desc, nomd_diag_cod,
	esp_desc, nomd_tipo, CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado,
	esp_lugar, COALESCE(esp_nombre_especialidad, esp_desc) AS esp_nombre_especialidad
  FROM nomina_detalle
  JOIN nomina USING (nom_id)
  JOIN pacientes USING (pac_id)
  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
  LEFT JOIN doctores ON nom_doc_id=doc_id
  LEFT JOIN especialidades ON nom_esp_id=esp_id 
  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
  WHERE $pac_w AND nomd_diag_cod NOT IN ('T','X') AND nom_fecha>=CURRENT_DATE
  
  ORDER BY nomina.nom_fecha ASC, nomd_hora 
  ", false);
  
  $lista_ic=cargar_registros_obj("
  select * from interconsulta 
  join especialidades on inter_especialidad=esp_id 
  join instituciones on inter_inst_id1=inst_id
  where inter_pac_id=".$pac['pac_id']." AND inter_estado>=0
  order by inter_ingreso DESC
  ");
    
?>

<html>
<title>Consulta en L&iacute;nea - Hospital Dr. Gustavo Fricke</title>

<style type="text/css">

body {
	background: url(consulta_backgr.png);
	background-repeat: repeat-x;
	background-color: #B3C4E1;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	margin: 0px; padding: 0px;
}

.Estilo1 {font-size: 14px}
.Estilo3 {color: #000000}
.Estilo6 {font-family: Geneva, Arial, Helvetica, sans-serif}
.Estilo7 {font-size: 14px; color: #000000; font-family: Geneva, Arial, Helvetica, sans-serif; }
.Estilo8 {
	font-size: 18px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

.Estilo9 {font-family: Verdana, Arial, Helvetica, sans-serif}

.citacion:hover { border:1px solid black; }

</style>

<script>

function realizar_accion(id_act) {

		window.open('consulta.php?accion='+id_act,'_self');
	
}

function imprimir_citacion(id_cit) {

		window.open('consulta.php?accion=1&citacion='+id_cit,'_self');
	
}

</script>


<body>

<center>

<table style='width:100%;height:201px;'>
	<tr>
		<td style='width:200px;'> 
		<center>		
		<img src='logo_min.png' style='width:180px;height:100px;'>
		</center>
		</td>

		<td style='color:#FFFFFF;'> 

			<h3>Consulta en L&iacute;nea</h3>

			<h2>Hospital Dr. Gustavo Fricke - Vi&ntilde;a del Mar</h2>
		
		
		</td>
		
		<td style='width:400px;'>
		
		<center>
		<table style='width:300px;font-size:16px;color:#FFFFFF;'>
			<tr onClick='realizar_accion(1);' style='cursor:pointer;'>
				<td><img src='printer.png' style='width:24px;height:24px;'></td>
				<td>Imprimir Citaciones Pendientes</td>
			</tr>
			<tr onClick='realizar_accion(2);' style='cursor:pointer;'>
				<td><img src='user_edit.png' style='width:24px;height:24px;'></td>
				<td>Modificar Datos Personales</td>
			</tr>
			<tr onClick='realizar_accion(3);' style='cursor:pointer;'>
				<td><img src='key.png' style='width:24px;height:24px;'></td>
				<td>Cambiar Contrase&ntilde;a de Ingreso</td>
			</tr>
			<tr onClick='realizar_accion(10);' style='cursor:pointer;'>
				<td><img src='lock.png' style='width:24px;height:24px;'></td>
				<td>Cerrar Sesi&oacute;n</td>
			</tr>
		</table>
		</center>
		 
		
		
		</td>
	</tr>
</table>

</center>

<table style='width:100%;font-size:11px;' class='lista_small celdas'>

<tr class='tabla_header'>
<td style='background-color:#aaaaaa;'><div align="center"><span class="Estilo8">Datos Personales</span></div></td>
</tr>

</table>


<table style='width:100%;font-size:18px;'>

<tr>

<td width="25%" class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>RUT:</td>
<td width="25%" class='tabla_fila' style='font-size:16px;font-weight:bold;'><div align="left" class="Estilo7"><?php echo $pac['pac_rut']; ?></div></td>
<td width="25%" class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>N&uacute;mero de Ficha:</td>
<td width="25%" class='tabla_fila' style='font-size:16px;'><div align="left" class="Estilo7"><?php echo $pac['pac_ficha']; ?></div></td>
</tr>

<tr>

<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Nombre:</td>
<td class='tabla_fila' style='font-size:14px;font-weight:bold;'>
  <div align="left" class="Estilo7"><?php echo htmlentities(trim($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'])); ?>  </div></td>
<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Fecha de Nacimiento:</td>
<td class='tabla_fila' style='font-size:14px;'><?php echo trim($pac['pac_fc_nac']); ?></td>
</tr>

<tr>

<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Tel&eacute;fono Fijo:</td>
<td class='tabla_fila' style='font-size:18px;'>
  <div align="left" class="Estilo7"><?php echo trim($pac['pac_fono']); ?>  </div></td>
<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Tel&eacute;fono Celular:</td>
<td class='tabla_fila' style='font-size:18px;'>
  <div align="left" class="Estilo7"><?php echo trim($pac['pac_celular']); ?></div></td>
</tr>


<tr>
<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Direcci&oacute;n:</td>
<td class='tabla_fila' style='font-size:18px;'>
  <div align="left" class="Estilo7"><?php echo htmlentities(trim(strtoupper($pac['pac_direccion']))); ?>  </div></td>
<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Ciudad:</td>
<td class='tabla_fila' style='font-size:18px;'>
  <div align="left" class="Estilo7"><?php echo htmlentities(trim($pac['ciud_desc'])); ?></div></td>
</tr>


<tr>

<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
e-mail:</td>
<td class='tabla_fila' style='font-size:14px;'><?php echo (trim($pac['pac_mail'])); ?></td>


<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Previsi&oacute;n:</td>
<td class='tabla_fila' style='font-size:14px;font-weight:bold;'><?php echo trim($pac['prev_desc']); ?></td>
</tr>


</table>



<table style='width:100%;font-size:11px;' class='lista_small celdas'>

<tr class='tabla_header'>
<td style='background-color:#aaaaaa;'><div align="center"><span class="Estilo8">Interconsultas con Hora de Citaci&oacute;n al <?php echo date('d/m/Y'); ?></span></div></td>
</tr>

</table>


<table style='width:100%;font-size:14px;' class='lista_small celdas'>
<tr class='tabla_header' style='background-color:#cccccc;'>

<td style='width:10%;'><div align="center" class="Estilo6">Fecha</td>
<td style='width:10%;'><div align="center" class="Estilo6">Hora</td>
<td style='width:30%;'><div align="center" class="Estilo6">Especialidad</td>
<td style='width:30%;'><div align="center" class="Estilo6">Profesional</td>
<td style='width:5%;'><div align="center" class="Estilo6">Imprimir</td>


</tr>

<?php

  if($lista) {

	  for($i=0;$i<count($lista);$i++) {

		($i%2==0) ? $clase='#dddddd' : $clase='#eeeeee';

		$lista[$i]['esp_desc']=str_replace('-HDGF', '', $lista[$i]['esp_desc']);

		$prof=strtoupper($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']);

		$prof=str_replace('(AGEN)', '', $prof);

		if($lista[$i]['nomd_hora']=='00:00:00') $lista[$i]['nomd_hora']='08:30:00';


		print("
		<tr class='citacion' style='background-color:$clase;'>
		 <td style='font-weight:bold;text-align:center;font-size:16px;'>".$lista[$i]['nom_fecha']."</td>    
		 <td style='font-weight:bold;text-align:center;font-size:16px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>    
		 <td style='text-align:left;font-weight:bold;'>".htmlentities($lista[$i]['esp_nombre_especialidad'])."</td>   
		 <td style='text-align:left;font-weight:bold;'>".htmlentities(strtoupper($prof))."</td>
		 <td style='text-align:left;font-weight:bold;'>
		 <center>
		 <img src='printer.png' style='cursor:pointer;' onClick='imprimir_citacion(".($lista[$i]['nomd_id']*1).");' />
		 </center>
		 </td>
		</tr>
		");    
		   
	  }
  
  } else {
	  
	  $clase='#eeeeee';

	  print("
		<tr class='citacion' style='background-color:$clase;'>
		 <td style='font-weight:bold;text-align:center;font-size:16px;' colspan=5>(No tiene citaciones pendientes...)</td>    
		</tr>
	  ");
	  
  }
  
  
?>
</table>

<!-----

<table style='width:100%;font-size:11px;' class='lista_small celdas'>

<tr class='tabla_header'>
<td style='background-color:#aaaaaa;'><div align="center"><span class="Estilo8">Historial de Interconsultas al <?php echo date('d/m/Y'); ?></span></div></td>
</tr>

<table style='width:100%;font-size:14px;' class='lista_small celdas'>
<tr class='tabla_header' style='background-color:#cccccc;'>

<td style='width:10%;'><div align="center" class="Estilo6">Fecha Ingreso</td>
<td style='width:30%;'><div align="center" class="Estilo6">Instituci&oacute;n Solicitante</td>
<td style='width:30%;'><div align="center" class="Estilo6">Especialidad</td>
<td style='width:30%;'><div align="center" class="Estilo6">Estado</td>


</tr>

<?php 

	if($lista_ic) {

	for($i=0;$i<sizeof($lista_ic);$i++) {
		
		($i%2==0) ? $clase='#dddddd' : $clase='#eeeeee';
		
		$estado='Pendiente';
		
    print("
    <tr class='citacion' style='background-color:$clase;'>
	 <td style='font-weight:bold;text-align:center;font-size:16px;'>".$lista_ic[$i]['inter_ingreso']."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:14px;'><i>".htmlentities($lista_ic[$i]['inst_nombre'])."</i></td>    
	 <td style='font-weight:bold;text-align:center;font-size:14px;'>".htmlentities($lista_ic[$i]['esp_desc'])."</td>    
	 <td style='text-align:center;font-weight:bold;'>".($estado)."</td>
	 </td>
	</tr>
    ");    

		
	}

	} else {
		
	  $clase='#eeeeee';

	  print("
		<tr class='citacion' style='background-color:$clase;'>
		 <td style='font-weight:bold;text-align:center;font-size:16px;' colspan=4>(No tiene interconsultas registradas...)</td>    
		</tr>
	  ");
	  
  }


?>

</table>

---->

<table style='width:100%;font-size:11px;' class='lista_small celdas'>

<tr class='tabla_header'>
<td style='background-color:#aaaaaa;'><div align="center"><span class="Estilo8">Hospitalizaciones</span></div></td>
</tr>

<table style='width:100%;font-size:14px;' class='lista_small celdas'>
<tr class='tabla_header' style='background-color:#cccccc;'>

<td style='width:30%;'><div align="center" class="Estilo6">Ubicaci&oacute;n</td>
<td style='width:5%;'><div align="center" class="Estilo6">Cama</td>
<td style='width:10%;'><div align="center" class="Estilo6">Condici&oacute;n del Paciente</td>
<td style='width:10%;'><div align="center" class="Estilo6">Fecha de Ingreso</td>
<td style='width:10%;'><div align="center" class="Estilo6">Fecha de Egreso</td>
<td style='width:35%;'><div align="center" class="Estilo6">Necesidades</td>


</tr>

<?php 

	$lista_h=cargar_registros_obj("
		SELECT *, COALESCE(hosp_cama_egreso, hosp_numero_cama) AS cama
		FROM hospitalizacion
		
		JOIN pacientes ON $pac_w AND hosp_pac_id=pac_id
		LEFT JOIN tipo_camas ON
			cama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND cama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)
		LEFT JOIN clasifica_camas ON 
			tcama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND tcama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)
			
		ORDER BY hosp_fecha_egr DESC;
	", true);
	
	if($lista_h) {

	for($i=0;$i<sizeof($lista_h);$i++) {
		
		$hosp_id=$lista_h[$i]['hosp_id']*1;

		($i%2==0) ? $clase='#dddddd' : $clase='#eeeeee';
		
		$estado='Pendiente';
		
		$ubica='<b>'.$lista_h[$i]['tcama_tipo'].'</b> / '.$lista_h[$i]['cama_tipo'].'';	

		$uval=pg_query("SELECT * FROM hospitalizacion_registro WHERE hosp_id = $hosp_id ORDER BY hreg_fecha DESC LIMIT 1;");
		
		if($v=pg_fetch_assoc($uval)) {
			$hcon_id=$v['hcon_id']*1; // idem
		} else {
			$hcon_id=1;
		}
		
		$tmp=cargar_registro("SELECT hcon_nombre FROM hospitalizacion_condicion WHERE hcon_id=$hcon_id;", true);
		
		$condicion=$tmp['condicion'];

		$tmp=cargar_registro("SELECT hospn_observacion FROM hospitalizacion_necesidades WHERE hosp_id=$hosp_id ORDER BY hospn_fecha DESC LIMIT 1;", true);
		
		$necesidades=$tmp['hospn_observacion'];
		
		if($condicion=='') $condicion='<i>----</i>';

		if($necesidades=='') $condicion='<i>(Sin Registros...)</i>';
		
		if($lista_h[$i]['hosp_fecha_egr']=='') {
			$lista_h[$i]['hosp_fecha_egr']='Vigente';
			$color='color:blue;';
		} else {
			$color='';
		}

		
    print("
    <tr class='citacion' style='background-color:$clase;'>
	 <td style='font-weight:bold;text-align:left;font-size:12px;'>".$ubica."</td>    
	 <td style='font-weight:bold;text-align:right;font-size:14px;'>".(($lista_h[$i]['cama']*1-$lista_h[$i]['tcama_num_ini']*1)+1)."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:12px;'>".$condicion."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:14px;'>".substr($lista_h[$i]['hosp_fecha_ing'],0,16)."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:14px;$color'>".substr($lista_h[$i]['hosp_fecha_egr'],0,16)."</td>    
	 <td style='text-align:center;font-weight:bold;'>".($necesidades)."</td>
	 </td>
	</tr>
    ");    

		
	}

	} else {
		
	  $clase='#eeeeee';

	  print("
		<tr class='citacion' style='background-color:$clase;'>
		 <td style='font-weight:bold;text-align:center;font-size:16px;' colspan=6>(No tiene hospitalizaciones registradas...)</td>    
		</tr>
	  ");
	  
  }


?>

</table>



<center><br /><br />

		<a href='http://www.sistemasexpertos.cl'><img src='logo_seis.png' style='border:0px;width:165px;height:45px;' /></a>

</center>

</body>

</html>
