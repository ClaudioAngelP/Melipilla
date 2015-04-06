<?php  require_once('../../conectar_db.php'); error_reporting(E_ALL);

	$tipo=$_POST['tipo_inf'];

	$fecha1 = pg_escape_string($_POST['fecha1']); 
	$fecha2 = pg_escape_string($_POST['fecha2']); 
	
	$esp_id = $_POST['esp_id']*1;
	if($esp_id!=-1) $esp="especialidades.esp_id=$esp_id";
	else $esp="true";
	
	$doc_id= $_POST['doc_id']*1;
	if($doc_id!=-1) $doc="doctores.doc_id=$doc_id";
	else $doc="true";
	$html='';
	$cont=0;
?>

<?php
	
 
	$listado = cargar_registros_obj("SELECT
	nom_fecha::date AS fecha,esp_desc,pac_ficha,pac_rut,pac_nombres||' '||pac_appat||' '||pac_apmat AS pac
	FROM nomina
	LEFT JOIN nomina_detalle USING (nom_id)
	LEFT JOIN especialidades on esp_id=nom_esp_id
	JOIN pacientes USING (pac_id)
	LEFT JOIN doctores ON doc_id=nom_doc_id
	WHERE nom_fecha BETWEEN '$fecha1 00:00:00' and '$fecha2 23:59:59'
	AND $esp
	ORDER BY nom_fecha,esp_desc,doc_nombres,nomd_hora
	");
				
  if($listado){
	  
	$cont=count($listado);
  
  $html.="<table style='width:100%;' class='lista_small'>
			<tr class='tabla_header'>
			<td>#</td>
			<td>Fecha</td>
			<td>Programa</td>
			<td>Ficha</td>
			<td>R.U.T.</td>
			<td>Paciente</td>
			</tr>";
			
	  for($i=0;$i<count($listado);$i++) {
		($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
		 
		$html.="<tr class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'>
			<td style='text-align:center;'>".($i+1)."</td>
			<td style='text-align:center;'>".$listado[$i]['fecha']."</td>
			<td style='text-align:center;'>".htmlentities($listado[$i]['esp_desc'])."</td>
			<td style='text-align:right;'>".$listado[$i]['pac_ficha']."</td>
			<td style='text-align:right;'>".$listado[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($listado[$i]['pac'])."</td>
			</tr>";
	}

		$html.="</table>";	
}	
		print(json_encode(Array($html, $cont)));

?>
