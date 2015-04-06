<?php 
	require_once('../../config.php');
	require_once('../../conectores/sigh.php');

	$busca=pg_escape_string(utf8_decode($_GET['rut']));

	$busca=str_replace('.','',trim($busca));
	$pac_w="pac_rut='$busca'";	

	$clave=$_GET['clave'];

	if($clave!='123123123') {
		exit("
                        <script>
                                alert('Clave incorrecta.');
                        </script>               
                ");
	}

  $pac = cargar_registro("SELECT * FROM pacientes 
							LEFT JOIN comunas USING (ciud_id)
							LEFT JOIN prevision USING (prev_id)
							WHERE $pac_w ", true);

	if(!$pac) {
		exit("
			<script>
				alert('Paciente no encontrado.');
			</script>		
		");	
	}
	
	if(strlen($pac['prev_desc'])==1) {
		$pac['prev_desc']='FONASA '.$pac['prev_desc'];
	}

	if(trim($pac['pac_mail'])=='') {
		$pac['pac_mail']='<i>(No Especificado...)</i>';
	}

  $lista = cargar_registros_obj("
  SELECT 
nom_fecha::date, nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 
COALESCE(diag_desc, cancela_desc) AS diag_desc, nomd_diag_cod,
esp_desc, nomd_tipo, CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado 
  FROM nomina_detalle
  JOIN nomina USING (nom_id)
  JOIN pacientes USING (pac_id)
  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
  LEFT JOIN doctores ON nom_doc_id=doc_id
  LEFT JOIN especialidades ON nom_esp_id=esp_id 
  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
  WHERE $pac_w AND NOT nomd_diag_cod = 'T' AND nom_fecha>=CURRENT_DATE
  
  ORDER BY nomina.nom_fecha DESC, nomd_hora 
  ");
  
  if($lista AND $tipo==1) {

	$nom_id=$lista[0]['nom_id']*1;
  
  	$n=cargar_registro("SELECT *, nom_fecha::date FROM nomina
  							 LEFT JOIN doctores ON nom_doc_id=doc_id
  							 LEFT JOIN especialidades ON nom_esp_id=esp_id
  							 WHERE nom_id=$nom_id", true);

	print("
		<div class='sub-content'>
		<table style='width:100%;'>
		<tr><td style='text-align:right;width:30%;'>Nro. N&oacute;mina:</td><td style='font-weight:bold;'>".$n['nom_folio']."</td></tr>		
		<tr><td style='text-align:right;'>Fecha:</td><td>".$n['nom_fecha']."</td></tr>		
		<tr><td style='text-align:right;'>Especialidad:</td><td>".$n['esp_desc']."</td></tr>		
		<tr><td style='text-align:right;'>Profesional:</td><td>".$n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']."</td></tr>		
		</table>	
		</div>
	");
  	
  }
  
?>

<html>
<title>Consulta en L&iacute;nea - Hospital Dr. Gustavo Fricke</title>

<style type="text/css">
body {
	background-color: #CBDEE4;
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
</style>

<body>

<center>

<table style='width:100%;background-color:#ffffff;'>
	<tr>
		<td style='width:200px;'> 
		<center>		
		<img src='logo_min.jpg' style='width:180px;height:100px;'>
		</center>
		</td>

		<td> 

			<h3>Consulta en L&iacute;nea</h3>

			<h2>Hospital Dr. Gustavo Fricke - Vi&ntilde;a del Mar</h2>
		
		
		</td>
		
		<td style='width:400px;'>
		
		<center>
		<table style='width:300px;font-size:16px;'>
			<tr>
				<td><img src='user_edit.png' style='width:24px;height:24px;'></td>
				<td>Modificar Datos Personales</td>
			</tr>
			<tr>
				<td><img src='key.png' style='width:24px;height:24px;'></td>
				<td>Cambiar Contrase&ntilde;a de Ingreso</td>
			</tr>
			<tr>
				<td><img src='printer.png' style='width:24px;height:24px;'></td>
				<td>Imprimir Citaciones Pendientes</td>
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
  <div align="left" class="Estilo7"><?php echo trim($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']); ?>  </div></td>
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
  <div align="left" class="Estilo7"><?php echo trim($pac['pac_direccion']); ?>  </div></td>
<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Ciudad:</td>
<td class='tabla_fila' style='font-size:18px;'>
  <div align="left" class="Estilo7"><?php echo trim($pac['ciud_desc']); ?></div></td>
</tr>


<tr>

<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
e-mail:</td>
<td class='tabla_fila' style='font-size:14px;'><?php echo trim($pac['pac_mail']); ?></td>


<td class='tabla_fila2 Estilo1 Estilo3 Estilo6' style='text-align:right;background-color:#cccccc;'>
Previsi&oacute;n:</td>
<td class='tabla_fila' style='font-size:14px;font-weight:bold;'><?php echo trim($pac['prev_desc']); ?></td>

</tr>


</table>



<table style='width:100%;font-size:11px;' class='lista_small celdas'>

<tr class='tabla_header'>
<td style='background-color:#aaaaaa;'><div align="center"><span class="Estilo8">Citaciones Pendientes al <?php echo date('d/m/Y'); ?></span></div></td>
</tr>

</table>


<table style='width:100%;font-size:14px;' class='lista_small celdas'>
<tr class='tabla_header' style='background-color:#cccccc;'>

<td style='width:10%;'><div align="center" class="Estilo6">Fecha</td>
<td style='width:10%;'><div align="center" class="Estilo6">Hora</td>
<td style='width:30%;'><div align="center" class="Estilo6">Especialidad</td>
<td style='width:30%;'><div align="center" class="Estilo6">Profesional</td>


</tr>

<?php

  if($lista)
  for($i=0;$i<count($lista);$i++) {

    ($i%2==0) ? $clase='#dddddd' : $clase='#eeeeee';

	$lista[$i]['esp_desc']=str_replace('-HDGF', '', $lista[$i]['esp_desc']);

	if($lista[$i]['nomd_hora']=='00:00:00') $lista[$i]['nomd_hora']='08:30:00';


    print("
    <tr style='background-color:$clase;'>
	 <td style='font-weight:bold;text-align:center;font-size:16px;'>".$lista[$i]['nom_fecha']."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:16px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>    
	 <td style='text-align:left;font-weight:bold;'>".htmlentities($lista[$i]['esp_desc'])."</td>   
	 <td style='text-align:left;font-weight:bold;'>".htmlentities(strtoupper($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']))."</td>
    ");    
	   
  }
  
?>
</table>


<table style='width:100%;font-size:11px;' class='lista_small celdas'>

<tr class='tabla_header'>
<td style='background-color:#aaaaaa;'><div align="center"><span class="Estilo8">Historial de Interconsultas al <?php echo date('d/m/Y'); ?></span></div></td>
</tr>

</table>




<center><br /><br />

<table style='width:450px;background-color:#ffffff;'>
	<tr>
		<td style='width:60px;'> 
		<img src='logo_seis.png' style='width:60px;height:80px;'/>
		</td>
		<td>
			<div style='font-size:11px;'>
			Desarrollado por Sistemas Expertos e Ingenier&iacute;a en Software LTDA.<br />
			<a href='http://www.sistemasexpertos.cl'>www.sistemasexpertos.cl</a>
			</div>		
		</td>
	</tr>
</table>

</center>

</body>

</html>
